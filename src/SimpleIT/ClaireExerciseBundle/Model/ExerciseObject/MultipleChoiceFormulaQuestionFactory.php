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

use SimpleIT\ClaireExerciseBundle\Model\Resources\ExerciseResource\MultipleChoiceFormula\MultipleChoiceFormulaPropositionResource;
use SimpleIT\ClaireExerciseBundle\Model\Resources\ExerciseResource\MultipleChoiceFormulaQuestionResource;

/**
 * This class manages the creation of instances of MultipleChoiceQuestion.
 *
 * @author Baptiste Cablé <baptiste.cable@liris.cnrs.fr>
 */
class MultipleChoiceFormulaQuestionFactory
{
    /**
     * Create a model question from an exerciseResource
     *
     * @param MultipleChoiceFormulaQuestionResource $res The resource
     *
     * @return MultipleChoiceQuestion
     */
    public static function createFromCommonResource(MultipleChoiceFormulaQuestionResource $res)
    {
        $mcq = new MultipleChoiceFormulaQuestion();

        $mcq->setComment($res->getComment());
        $mcq->setQuestion($res->getQuestion());
        $mcq->setDoNotShuffle($res->getDoNotShuffle() === true);
        $mcq->setMaxNOfRightPropositions($res->getMaxNumberOfRightPropositions());
        $mcq->setMaxNumberOfPropositions($res->getMaxNumberOfPropositions());
        $mcq->setPictureId($res->getPictureId());
        $mcq->setPicturePropositionIds($res->getPicturePropositionIds());

        /** @var MultipleChoicePropositionResource $prop */
        foreach ($res->getPropositions() as $prop) {
            $mcq->addProposition($prop->getText(), $prop->getRight(), $prop->getForceUse(), $prop->getPicturePropositionId());
        }

        return $mcq;
    }
}
