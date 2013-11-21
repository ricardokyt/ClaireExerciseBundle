<?php

namespace SimpleIT\ClaireAppBundle\UseCases\Course\Toc\DTO;

use SimpleIT\ApiResourcesBundle\Course\PartResource;
use SimpleIT\ClaireAppBundle\Responders\Course\Toc\AddElementToTocResponse;

/**
 * Class AddElementToTocResponseDTO
 *
 * @author Romain Kuzniak <romain.kuzniak@simple-it.fr>
 */
class AddElementToTocResponseDTO implements AddElementToTocResponse
{
    /**
     * @var PartResource
     */
    public $toc;

    /**
     * @var PartResource
     */
    public $newElement;

    /**
     * @return PartResource
     */
    public function getNewElement()
    {
        return $this->newElement;
    }

    /**
     * @return \SimpleIT\ApiResourcesBundle\Course\PartResource
     */
    public function getToc()
    {
        return $this->toc;
    }

}
