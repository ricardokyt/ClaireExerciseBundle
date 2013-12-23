<?php

namespace SimpleIT\ClaireAppBundle\UseCases\Course\Content\DTO;

use SimpleIT\ClaireAppBundle\Requestors\Course\Content\GetWaitingForPublicationContentRequest;

/**
 * @author Romain Kuzniak <romain.kuzniak@openclassrooms.com>
 */
class GetWaitingForPublicationContentRequestDTO implements GetWaitingForPublicationContentRequest
{
    /**
     * @var int
     */
    public $courseId;

    public function __construct($courseId)
    {
        $this->courseId = $courseId;
    }

    /**
     * @return int
     */
    public function getCourseId()
    {
        return $this->courseId;
    }
}
