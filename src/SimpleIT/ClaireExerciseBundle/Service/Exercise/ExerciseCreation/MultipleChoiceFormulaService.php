<?php
/*
 * This file is part of CLAIRE.
 *
 * CLAIRE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CLAIRE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CLAIRE. If not, see <http://www.gnu.org/licenses/>
 */

namespace SimpleIT\ClaireExerciseBundle\Service\Exercise\ExerciseCreation;

use Claroline\CoreBundle\Entity\User;
use SimpleIT\ClaireExerciseBundle\Entity\CreatedExercise\Answer;
use SimpleIT\ClaireExerciseBundle\Entity\CreatedExercise\Item;
use SimpleIT\ClaireExerciseBundle\Entity\ExerciseModel\ExerciseModel;
use SimpleIT\ClaireExerciseBundle\Exception\InvalidAnswerException;
use SimpleIT\ClaireExerciseBundle\Model\ExerciseObject\MultipleChoiceFormulaQuestion;
use SimpleIT\ClaireExerciseBundle\Model\Resources\AnswerResourceFactory;
use SimpleIT\ClaireExerciseBundle\Model\Resources\Exercise\MultipleChoiceFormula\Exercise;
use SimpleIT\ClaireExerciseBundle\Model\Resources\Exercise\MultipleChoiceFormula\Proposition;
use SimpleIT\ClaireExerciseBundle\Model\Resources\Exercise\MultipleChoiceFormula\Question;
use SimpleIT\ClaireExerciseBundle\Model\Resources\ExerciseModel\Common\CommonModel;
use SimpleIT\ClaireExerciseBundle\Model\Resources\ExerciseModel\MultipleChoiceFormula\Model;
use SimpleIT\ClaireExerciseBundle\Model\Resources\ExerciseModel\MultipleChoiceFormula\QuestionBlock;
use SimpleIT\ClaireExerciseBundle\Model\Resources\ExerciseResource\CommonResource;
use SimpleIT\ClaireExerciseBundle\Model\Resources\ItemResource;
use SimpleIT\ClaireExerciseBundle\Model\Resources\ItemResourceFactory;

/* TODO BRYAN : NE DOIT PAS ETRE LA... MODIFIER LE CODE POUR DEPLACER LES SUIVANTS dans les Formules... */
use SimpleIT\ClaireExerciseBundle\Model\Resources\DomainKnowledge\Formula\Variable as ResourceVariable;
use SimpleIT\ClaireExerciseBundle\Model\DomainKnowledge\Formula\Equation;
use SimpleIT\ClaireExerciseBundle\Model\DomainKnowledge\Formula\Expression;
use SimpleIT\ClaireExerciseBundle\Model\DomainKnowledge\Formula\Variable;


/**
 * Service which manages Multiple Choice Exercises.
 *
 * @author Baptiste Cablé <baptiste.cable@liris.cnrs.fr>
 */
class MultipleChoiceFormulaService extends ExerciseCreationService
{
    /**
     * @inheritdoc
     */
    public function generateExerciseFromExerciseModel(
        ExerciseModel $exerciseModel,
        CommonModel $commonModel,
        User $owner
    )
    {
        /** @var Model $commonModel */
        // Generation of the exercise with the model
        $exercise = $this->generateMCFExercise($commonModel, $owner);

        // Transformation of the exercise into entities (StoredExercise and Items)
        return $this->toStoredExercise(
            $exercise,
            $exerciseModel,
            "multiple-choice-formula",
            $exercise->getQuestions()
        );
    }

    /**
     * Generate a multiple choice exercise from a model
     *
     * @param Model $model
     * @param User $owner
     *
     * @return Exercise
     */
    private function generateMCFExercise(Model $model, User $owner)
    {
        /* On crée l'exercice avec la consigne du model, rien a faire ici donc. */
        $exercise = new Exercise($model->getWording());



        // Documents
        $this->addDocuments($model, $exercise, $owner);

        // array to collect all the questions to add
        $modelQuestionToAdd = array();

        // get the blocks
        foreach ($model->getQuestionBlocks() as $block) {
            $this->addQuestionsFromBlock($block, $modelQuestionToAdd, $owner);
        }

        /*
         *  Create and add the exercise questions
         */
        $this->addQuestionsToTheExercise($modelQuestionToAdd, $exercise, $owner);

        // shuffle the order of the questions (if needed) and the propositions
        if ($model->isShuffleQuestionsOrder()) {
            $exercise->shuffleQuestionOrder();
        }
        $exercise->shufflePropositionOrder();

        $exercise->finalize();

        return $exercise;
    }

    /**
     * Add questions from an array of MultipleChoiceFormulaExerciseQuestions
     * to the Exercise
     *
     * @param array $modelQuestionToAdd The array of questions
     * @param Exercise $exercise The MultipleChoiceFormulaExercise
     * @param \Claroline\CoreBundle\Entity\User $owner
     */
    private function addQuestionsToTheExercise(
        array $modelQuestionToAdd,
        Exercise &$exercise,
        User $owner
    )
    {
        /*
         *  TODO BRYAN : LA VERSION ACTUELLE EST TROP TARABSICOTE. UN PEU D'ALGO ET JE DEVRAIS POUVOIR PRODUIRE
         *  TODO BRYAN : UNE VERSION PLUS "simple", QUI APELLE UNE FONCTION FAITE POUR DANS FORMULASERVICE...
         * */
        /*
         * TODO BRYAN : Bon, ici c est pour la generation d exercice a partir de la ressource generee... */
        /** @var MultipleChoiceFormulaQuestion $modelQuestion */
        foreach ($modelQuestionToAdd as $modelQuestion) {
            // initialize the exercise question
            $exerciseQuestion = new Question();
            $exerciseQuestion->setDoNotShuffle($modelQuestion->getDoNotShuffle());

            // On genere les valeurs pour l enonce. Cela résoud aussi les inconnues.
            $variables = $this->computeFormulaVariableValues($modelQuestion->getFormulas(), $owner);
            // VU QUE J'AI APPELE CETTE FONCTION, ON SUPPUTE QUE PAR LA SUITE LES VARIABLES SONT DEJA VERIFIEES
            // COMME ETANT VALABLE. SINON, ON AURAIT DEJA EU UN TROW

            /* TODO BRYAN : Les valeurs doivent être les mêmes pour chaque formula... */
            /* TODO BRYAN : 1) Récuperer toutes les variables 2) Instancier chacune. 3) Trouver les solutions
                La récuperation de toutes les variables est a prendre en compte avant...
                Il faut faire un tableau avec toutes les variables presentes en une seule fois.
            */
            /* On récupère dans cette array toutes les variables qui existent*/
            $array_variable_unique = array();
            foreach( $modelQuestion->getFormulas() as $local_formula )
            {
                foreach($local_formula->getVariables() as $newVar )
                {
                    $array_variable_unique[ $local_formula->getName().":".$newVar->getName()] =  $newVar;
                }
            }


            /* TODO BRYAN : On change les valeurs... Si une variable a le même nom que dans l'autre, on remplace la valeur
               TODO BRYAN : Attention ! Cela veut dire qu'il faut que l'autre formule soit quand même valable... */
            foreach ($array_variable_unique as $key => $newVar)
            {
                $var_name = substr($key, strpos($key, ':'));
                foreach($variables as $result_key => $var_value)
                {
                    if( $var_name == substr($result_key, strpos($result_key, ':')) )
                    {
                        $variables[$result_key] = $variables[$key];
                    }
                }
            }
            /* TODO BRYAN : On recalcule les inconnues... Il s'agit d'une partie de resolve formula mais sans la partie
               TODO BRYAN : instanciation des variables...*/
            foreach( $modelQuestion->getFormulas() as $local_formula)
            {
                $unknown = $local_formula->getUnknown();
                $unknownName = $unknown->getName();

                $equation = $this->formulaService->textExpressionToExpression($local_formula->getEquation());
                /*if ($equation->getExprName() !== Equation::EXPR_NAME)
                {
                    throw new InvalidKnowledgeException('The formula is not an equation');
                }*/

                /* On reconstruit l'array de valeur correspondant sans les prefixes */
                $values = array();
                foreach($variables as $lkey => $lvar)
                {
                    if( ( preg_match( "/".$local_formula->getName()."/i", $lkey ) == 1 )
                          and ( substr($lkey, (strpos($lkey, ':')+1)) != $unknownName ) )
                    {
                        $values[substr($lkey, (strpos($lkey, ':'))+1)] = $variables[$lkey] ;
                    }
                }
                /*TODO BRYAN : On doit recalculer aussi tout ce qui est expression... Attention donc... Si expression..*/
                /*foreach( $variables as $lkey2 => $lvar2)
                {
                    if( $array_variable_unique[$lkey2]->getValueType() === Formula\Variable::EXPRESSION )
                    {
                        $this->formulaService->instantiateVariableWithExpression($array_variable_unique[$lkey2]->getValueType(), $values);
                    }
                }*/
                $unknownValue = $this->formulaService->resolveEquation($equation, $unknownName)->evaluate($values);

                // format value
                if ($unknown->getType() === ResourceVariable::INTEGER) {
                    if (!is_integer($unknownValue)) {
                        throw new InvalidKnowledgeException('The computed answer is not an integer');
                    }
                } elseif ($unknown->getType() === ResourceVariable::FLOAT) {
                    if ($unknown->getDigitsAfterPoint() > 0) {
                        $unknownValue = round($unknownValue, $unknown->getDigitsAfterPoint());
                    }
                } elseif ($unknown->getType() === ResourceVariable::SCIENTIFIC) {
                    if ($unknown->getDigitsAfterPoint() > 0) {
                        $unknownValue = $this->roundSignificantDigits(
                            $unknownValue,
                            $unknown->getDigitsAfterPoint() + 1
                        );
                    }
                }

                $values[$unknownName] = $unknownValue;
                $variables[$local_formula->getName().':'.$unknownName] = $unknownValue;
            }

            /* Une fois que j'ai tout généré, il me faut juste remplacer partout !!
               1) Je remplace dans la question de QCM les valeurs qui vont bien... */
            $wording = $this->parseStringWithVariablesForMCFQ($modelQuestion->getQuestion(), $variables);

            /*
            foreach ($array_variable_unique as $key => $newVar)
            {
            $wording = $wording."var : ";
            $wording = $wording.$newVar->getName();
            $wording = $wording." , ";
            $wording = $wording.$variables[$key];
            }
            */
            /*
            foreach( $variables as $key => $local_value)
            {
                $wording = $wording."var : ";
                $wording = $wording.$key;
                $wording = $wording." , ";
                $wording = $wording.$local_value;
            } */

            /* TODO BRYAN ! ATTENTION !! DANS LE CAS D'UNE VARIABLE EXPRESSION, ON DOIT RECALCULER >< */

            $exerciseQuestion->setQuestion($wording);
            $exerciseQuestion->setComment($modelQuestion->getComment());
            $exerciseQuestion->setOriginResource($modelQuestion->getOriginResource());
            
            // On reupere l'imageId de question et la liste d'imageId de propositions
            $exerciseQuestion->setPictureId($modelQuestion->getPictureId());
            $exerciseQuestion->setPicturePropositionIds($modelQuestion->getPicturePropositionIds());

            // organise the propositions ids
            $forcedRightId = array();
            $forcedWrongId = array();
            $rightId = array();
            $wrongId = array();

            /*
             * TODO BRYAN : Pour chaque proposition, recuperer les variables et les remplacer !
             * */
            foreach ($modelQuestion->getPropositions() as $key => $proposition) {
                if ($modelQuestion->getForceUse()[$key] === true) {
                    if ($modelQuestion->getRight()[$key] === true) {
                        $forcedRightId[] = $key;
                    } else {
                        $forcedWrongId[] = $key;
                    }
                } else {
                    if ($modelQuestion->getRight()[$key] === true) {
                        $rightId[] = $key;
                    } else {
                        $wrongId[] = $key;
                    }
                }
            }
            // Determine the number of proposition and right proposition to be added in
            $this->numberOfPropositions(
                $modelQuestion,
                $forcedRightId,
                $forcedWrongId,
                $rightId,
                $wrongId,
                $numberOfRAToAdd,
                $numberOfWAToAdd
            );

            // add the forced propositions
            $propositionIds = array_merge($forcedWrongId, $forcedRightId);

            // add the correct number of right propositions
            for ($ind = 0; $ind < $numberOfRAToAdd; $ind++) {
                $key = array_rand($rightId);
                $propositionIds[] = $rightId[$key];
                unset($rightId[$key]);
            }

            // add the correct number of wrong propositions
            for ($ind = 0; $ind < $numberOfWAToAdd; $ind++) {
                $key = array_rand($wrongId);
                $propositionIds[] = $wrongId[$key];
                unset($wrongId[$key]);
            }

            // TODO BRYAN : J'ai modifié ici, et je le note pour bien voir où sont mes modifications
            // add the propositions to the exercise question
            sort($propositionIds);
            foreach ($propositionIds as $propId) {
                // il faut corriger l'appel à la methode d'ajout de proposition en lui passant l'attribut picturePropositionId en parametre
                $exerciseQuestion->addProposition(
                    $modelQuestion->getRight()[$propId],
                    $this->parseStringWithVariablesForMCFQ($modelQuestion->getPropositions()[$propId], $variables) /*,
                    null,
                    ($exerciseQuestion->getPropositions()[$propId])->getPicturePropositionId() */
                );
            }

            // TODO BRYAN : Autre modification ici ! Generation de propositions supplémentaires si pas assez.
            // TODO BRYAN : INCOMPLET... Et cela fait planter !
            $nb_actuel = count($modelQuestion->getPropositions());
            $nb_voulu = $modelQuestion->getMaxNumberOfPropositions() ;
            $nb_a_generer = $nb_voulu - $nb_actuel;
            while($nb_a_generer>0)
            {
                $iteration_actuelle = 100;


                $nb_a_generer = $nb_a_generer -1;
            }
            /*$wording = $modelQuestion->getGeneratedProposition();
            $nb_proposition = $modelQuestion->getMaxNOfRightPropositions();

            $number_total = $nb_proposition - $modelQuestion->getPropositions().length();
            $generate = array();
            $variables_generated = [];
            while($number_total>0)
            {

            } */

            $exercise->addQuestion($exerciseQuestion);
        }
    }

    /**
     * Compute the number of propositions and right propositions for one question.
     * The result are stored in $numberOfRAToAdd and $numberOfWAToAdd
     *
     * EDIT: Max for right prop number is used if not let blank by the user
     *
     * @param \SimpleIT\ClaireExerciseBundle\Model\ExerciseObject\MultipleChoiceFormulaQuestion|\SimpleIT\ClaireExerciseBundle\Service\Exercise\ExerciseCreation\MultipleChoiceFormulaQuestion $modelQuestion The question
     * @param array $forcedRightId
     * @param array $forcedWrongId
     * @param array $rightId
     * @param array $wrongId
     * @param int $numberOfRAToAdd (result) number of right propositions to be added
     * @param int $numberOfWAToAdd (result) number of wrong propositions to be added
     */
    private function numberOfPropositions(
        MultipleChoiceFormulaQuestion $modelQuestion,
        $forcedRightId,
        $forcedWrongId,
        $rightId,
        $wrongId,
        &$numberOfRAToAdd,
        &$numberOfWAToAdd
    )
    {
        // number of propositions to use
        $numberOfForcedRightPropositions = count($forcedRightId);
        $numberOfForcedWrongPropositions = count($forcedWrongId);;
        $numberOfRightPropositions = count($rightId);
        $numberOfWrongPropositions = count($wrongId);

        //get the max number of propositions parameters
        if ($modelQuestion->getMaxNumberOfPropositions() == 0) {
            $maxNumberOfPropositions = $numberOfRightPropositions + $numberOfWrongPropositions;
        } else {
            $maxNumberOfPropositions = $modelQuestion->getMaxNumberOfPropositions()
                - $numberOfForcedWrongPropositions - $numberOfForcedRightPropositions;
            if ($maxNumberOfPropositions < 0) {
                $maxNumberOfPropositions = 0;
            }
        }

        if ($modelQuestion->getMaxNOfRightPropositions() == 0) {
            $maxNumberOfRightPropositions = $numberOfRightPropositions;
            $useMaxForRight = false;
        } else {
            $maxNumberOfRightPropositions = $modelQuestion->getMaxNOfRightPropositions()
                - $numberOfForcedRightPropositions;

            if ($maxNumberOfRightPropositions < 0) {
                $maxNumberOfRightPropositions = 0;
            }
            $useMaxForRight = true;
        }

        // determine the real possible max number of propositions
        if ($maxNumberOfPropositions > $numberOfRightPropositions + $numberOfWrongPropositions) {
            $maxNumberOfPropositions = $numberOfRightPropositions + $numberOfWrongPropositions;
        }

        // determine the real possible max number of right propositions
        if ($maxNumberOfRightPropositions > $numberOfRightPropositions) {
            $maxNumberOfRightPropositions = $numberOfRightPropositions;
        }
        if ($maxNumberOfRightPropositions > $maxNumberOfPropositions) {
            $maxNumberOfRightPropositions = $maxNumberOfPropositions;
        }

        // determine the min number of right anwers
        if ($numberOfForcedRightPropositions > 0) {
            $minNumberOfRightPropositions = 0;
        } else {
            $minNumberOfRightPropositions = 1;
        }
        if ($numberOfWrongPropositions + 1 < $maxNumberOfPropositions) {
            $minNumberOfRightPropositions = $maxNumberOfPropositions - $numberOfWrongPropositions;
            if ($minNumberOfRightPropositions > $maxNumberOfRightPropositions) {
                $minNumberOfRightPropositions = $maxNumberOfRightPropositions;
            }
        }

        // number of right proposition (RA) and wrong proposition (WA) to add
        if ($useMaxForRight) {
            $numberOfRAToAdd = $maxNumberOfRightPropositions;
        } else {
            $numberOfRAToAdd = rand($minNumberOfRightPropositions, $maxNumberOfRightPropositions);
        }
        $numberOfWAToAdd = $maxNumberOfPropositions - $numberOfRAToAdd;
        if ($numberOfWAToAdd > $numberOfWrongPropositions) {
            $numberOfWAToAdd = $numberOfWrongPropositions;
        }

    }

    /**
     * Add questions to the question-to-add list from a questionBlock
     *
     * @param QuestionBlock $questionBlock
     * @param array $modelQuestionToAdd
     * @param User $owner
     */
    private function addQuestionsFromBlock(
        QuestionBlock $questionBlock,
        &$modelQuestionToAdd,
        User $owner
    )
    {
        // get the questions from the block
        $blockQuestions = $this->questionsFromBlock($questionBlock, $owner);

        // Complete the questions with block information and add it to the list
        foreach ($blockQuestions as $question) {
            /** @var MultipleChoiceFormulaQuestion $question */
            // overload the max number of propositions
            if ($questionBlock->getMaxNumberOfPropositions() > 0) {
                $question->setMaxNumberOfPropositions($questionBlock->getMaxNumberOfPropositions());
            }
            if ($questionBlock->getMaxNumberOfRightPropositions() > 0) {
                $question->setMaxNOfRightPropositions(
                    $questionBlock->getMaxNumberOfRightPropositions()
                );
            }

            // add it to the list
            $modelQuestionToAdd[] = $question;
        }
    }

    /**
     * Retrieve MultipleChoiceFormulaQuestions from a question block
     *
     * @param QuestionBlock $questionBlock
     * @param User $owner
     *
     * @return array An array of MultipleChoiceQuestion
     */
    private function questionsFromBlock(QuestionBlock $questionBlock, User $owner)
    {
        $blockQuestions = array();
        $numOfQuestions = $questionBlock->getNumberOfOccurrences();

        /*
         * if the block is a list
         */
        if ($questionBlock->isList()) {
            $this->getObjectsFromList($questionBlock, $numOfQuestions, $blockQuestions, $owner);
        } /*
         * if the block is question constraints
         */
        else {
            $oc = $questionBlock->getResourceConstraint();
            $oc->setType(CommonResource::MULTIPLE_CHOICE_FORMULA_QUESTION);
            $blockQuestions = $this->exerciseResourceService
                ->getExerciseObjectsFromConstraints(
                    $oc,
                    $numOfQuestions,
                    $owner
                );
        }

        return $blockQuestions;
    }

    /**
     * Correct the multiple choice question
     *
     * @param Item $item
     * @param Answer $answer
     *
     * @return ItemResource
     */
    public function correct(Item $item, Answer $answer)
    {
        $itemResource = ItemResourceFactory::create($item);
        /** @var Question $question */
        $question = $itemResource->getContent();
        $la = AnswerResourceFactory::create($answer);

        $userTicks = $la->getContent();

        foreach ($userTicks as $key => $tick) {
            $question->setTicked($key, $tick);
        }

        $this->mark($question);

        $itemResource->setContent($question);

        return $itemResource;
    }

    /**
     * Compute and set mark to the question
     *
     * @param Question $question
     */
    private function mark(Question &$question)
    {
        $mark = 100.0;

        foreach ($question->getPropositions() as $prop) {
            /** @var Proposition $prop */
            if ($prop->getTicked() != $prop->getRight()) {
                $mark = 0.0;
            }
        }

        if ($mark < 0) {
            $mark = 0;
        }

        $question->setMark($mark);
    }

    /**
     * Validate the answer to an item
     *
     * @param Item $itemEntity
     * @param array $answer
     *
     * @throws InvalidAnswerException
     */
    public function validateAnswer(Item $itemEntity, array $answer)
    {
        /** @var Question $question */
        $question = ItemResourceFactory::create($itemEntity)->getContent();

        $nbProp = count($question->getPropositions());

        if (count($answer) !== $nbProp) {
            throw new InvalidAnswerException('Invalid number of objects in the answer');
        }

        foreach ($answer as $ans) {
            if ($ans !== 1 && $ans !== 0) {
                throw new InvalidAnswerException('Invalid format for response : 0 or 1 expected.');
            }
        }
    }

    /**
     * Return an item without solution
     *
     * @param ItemResource $itemResource
     *
     * @return ItemResource
     */
    public function noSolutionItem($itemResource)
    {
        /** @var Question $content */
        $content = $itemResource->getContent();

        $content->setComment(null);

        /** @var Proposition $prop */
        foreach ($content->getPropositions() as $prop) {
            $prop->setRight(null);
        }

        return $itemResource;
    }
}
