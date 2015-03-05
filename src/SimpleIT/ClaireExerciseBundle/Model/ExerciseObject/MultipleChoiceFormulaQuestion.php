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

namespace SimpleIT\ClaireExerciseBundle\Model\ExerciseObject;

use SimpleIT\ClaireExerciseBundle\Model\Resources\ExerciseObject\ExerciseObject;

/**
 * A MultipleChoiceQuestion is the representation of a multiple choice question
 * retrieved from a resource. The question is not under a final form that can be
 * presented in an exercise.
 * A MultipleChoiceQuestion can contain more propositions that will be used in the
 * exercise. The maximum number of (right) propositions to be used is specified in
 * the parameters of the MultipleChoiceQuestion.
 *
 * @author Baptiste Cablé <baptiste.cable@liris.cnrs.fr>
 */
class MultipleChoiceFormulaQuestion extends ExerciseObject
{
    const OBJECT_TYPE = "multiple-choice-formula-question";

    /*
     * @var variable name from formula that are common for all formula. TODO BRYAN : Use them !
     */
    private $variables;

    /**
     * @var string $question The wording of the question (text)
     */
    private $question;

    /**
     * The propositions
     *
     * @var array $rightPropositions An array of strings
     */
    private $propositions = array();

    /**
     * If the propositions are right or wrong
     *
     * @var array $wrongPropositions An array of strings
     */
    private $right = array();

    /**
     * If the propositions must be used
     *
     * @var array $rightPropositions An array of strings
     */
    private $forceUse = array();

    /**
     * @var string $comment The comment of the question to be displayed after the correction
     */
    private $comment = "";

    /**
     * @var int $maxNumberOfPropositions The max number of propositions to be displayed
     */
    private $maxNumberOfPropositions;

    /**
     * @var int $maxNOfRightPropositions The max number of right propositions to be displayed
     */
    private $maxNOfRightPropositions;

     /**
     * @var string generated_proposition The proposition pattern to generate other proposition.
     */
    private $generated_proposition;

    /**
     * @var boolean $doNotShuffle
     */
    private $doNotShuffle;
    
    /**
     * @var int $pictureId
     */
    private $pictureId;
    
    /**
     * @var array<int> $picturePropositionIds
     */
    private $picturePropositionIds = array();

    /**
     * setWording for generated proposition
     *
     * @param string $generated_proposition
     */
    public function setGeneratedProposition($generated_proposition)
    {
        $this->generated_proposition = $generated_proposition;
    }

    /**
     *
     * getWording for generated proposition
     *
     * @return string
     */
    public function getGeneratedProposition()
    {
        return $this->generated_proposition;
    }

    /**
     * Set comment
     *
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set forceUse
     *
     * @param array $forceUse
     */
    public function setForceUse($forceUse)
    {
        $this->forceUse = $forceUse;
    }

    /**
     * Get forceUse
     *
     * @return array
     */
    public function getForceUse()
    {
        return $this->forceUse;
    }

    /**
     * Set maxNOfRightPropositions
     *
     * @param int $maxNOfRightPropositions
     */
    public function setMaxNOfRightPropositions($maxNOfRightPropositions)
    {
        $this->maxNOfRightPropositions = $maxNOfRightPropositions;
    }

    /**
     * Get maxNOfRightPropositions
     *
     * @return int
     */
    public function getMaxNOfRightPropositions()
    {
        return $this->maxNOfRightPropositions;
    }

    /**
     * Set maxNumberOfPropositions
     *
     * @param int $maxNumberOfPropositions
     */
    public function setMaxNumberOfPropositions($maxNumberOfPropositions)
    {
        $this->maxNumberOfPropositions = $maxNumberOfPropositions;
    }

    /**
     * Get maxNumberOfPropositions
     *
     * @return int
     */
    public function getMaxNumberOfPropositions()
    {
        return $this->maxNumberOfPropositions;
    }

    /**
     * Set propositions
     *
     * @param array $propositions
     */
    public function setPropositions($propositions)
    {
        $this->propositions = $propositions;
    }

    /**
     * Get propositions
     *
     * @return array
     */
    public function getPropositions()
    {
        return $this->propositions;
    }

    /**
     * Set question
     *
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set right
     *
     * @param array $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * Get right
     *
     * @return array
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set doNotShuffle
     *
     * @param boolean $doNotShuffle
     */
    public function setDoNotShuffle($doNotShuffle)
    {
        $this->doNotShuffle = $doNotShuffle;
    }

    /**
     * Get doNotShuffle
     *
     * @return boolean
     */
    public function getDoNotShuffle()
    {
        return $this->doNotShuffle;
    }
    
    /**
     * Get pictureId
     * 
     * @return int
     */
    public function getPictureId() {
        return $this->pictureId;
    }

    /**
     * Set pictureId
     * 
     * @param int $pictureId
     */
    public function setPictureId($pictureId) {
        $this->pictureId = $pictureId;
    }
    
    /**
     * Get picturePropositionIds
     * 
     * @return array
     */
    public function getPicturePropositionIds() {
        return $this->picturePropositionIds;
    }

    /**
     * Set picturePropositionIds
     * 
     * @param array $picturePropositionIds
     */
    public function setPicturePropositionIds($picturePropositionIds) {
        $this->picturePropositionIds = $picturePropositionIds;
    }
    
    /**
     * Add a proposition
     *
     * @param $text
     * @param $right
     * @param $forceUse
     * @param $picturePropositionId
     */
    public function addProposition($text, $right, $forceUse, $picturePropositionId = 0)
    {
        $key = count($this->propositions);
        $this->propositions[$key] = $text;
        $this->right[$key] = $right;
        $this->forceUse[$key] = ($forceUse === true);
        $this->picturePropositionIds[$key] = $picturePropositionId;
    }

}
