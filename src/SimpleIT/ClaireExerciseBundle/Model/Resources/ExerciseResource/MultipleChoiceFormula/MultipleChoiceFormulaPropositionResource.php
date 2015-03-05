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

namespace SimpleIT\ClaireExerciseBundle\Model\Resources\ExerciseResource\MultipleChoiceFormula;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MultipleChoicePropositionResource
 *
 * @author Baptiste Cablé <baptiste.cable@liris.cnrs.fr>
 */
class MultipleChoiceFormulaPropositionResource
{
    /**
     * @var string $text
     * @Serializer\Type("string")
     * @Serializer\Groups({"details", "resource_storage"})
     * @Assert\NotBlank(groups={"create"})
     */
    private $text;

    /**
     * @var boolean $right
     * @Serializer\Type("boolean")
     * @Serializer\Groups({"details", "resource_storage"})
     * @Assert\NotBlank(groups={"create"})
     */
    private $right;

    /**
     * @var boolean $forceUse
     * @Serializer\Type("boolean")
     * @Serializer\Groups({"details", "resource_storage"})
     * @Assert\NotBlank(groups={"create"})
     */
    private $forceUse;
    
    /**
     * @var integer
     * @Serializer\Type("integer")
     * @Serializer\Groups({"details", "resource_storage"})
     * @Assert\NotBlank(groups={"create"})
     */    
    private $picturePropositionId = 0;

    /**
     * Set right
     *
     * @param boolean $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * Get right
     *
     * @return boolean
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set text
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set forceUse
     *
     * @param boolean $forceUse
     */
    public function setForceUse($forceUse)
    {
        $this->forceUse = $forceUse;
    }

    /**
     * Get forceUse
     *
     * @return boolean
     */
    public function getForceUse()
    {
        return $this->forceUse;
    }

    /**
     * Get picturePropositionId
     * @return int
     */
    public function getPicturePropositionId() {
        return $this->picturePropositionId;
    }

    /**
     * Set picturePropositionId
     * @param int $picturePropositionId
     */
    public function setPicturePropositionId($picturePropositionId) {
        $this->picturePropositionId = $picturePropositionId;
    }
}
