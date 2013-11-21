<?php

namespace SimpleIT\ClaireAppBundle\UseCases\Course\Toc;

use SimpleIT\ApiResourcesBundle\Course\CourseResource;
use SimpleIT\ApiResourcesBundle\Course\PartResource;
use SimpleIT\ClaireAppBundle\Entity\Course\Part\PartFactoryImpl;
use SimpleIT\ClaireAppBundle\Gateways\Course\Toc\TocByCourseGateway;
use SimpleIT\ClaireAppBundle\Requestors\UseCase;
use SimpleIT\ClaireAppBundle\Requestors\UseCaseRequest;
use SimpleIT\ClaireAppBundle\UseCases\Course\Toc\DTO\AddElementToTocRequestDTO;
use SimpleIT\ClaireAppBundle\UseCases\Course\Toc\DTO\AddElementToTocResponseDTO;

/**
 * Class AddElementToToc
 *
 * @author Romain Kuzniak <romain.kuzniak@simple-it.fr>
 */
class AddElementToToc implements UseCase
{
    private static $correspondingChildSubtype = array(
        PartResource::COURSE  => PartResource::TITLE_1,
        PartResource::TITLE_1 => PartResource::TITLE_2
    );

    /**
     * @var AddElementToTocResponseDTO
     */
    private $response;

    /**
     * @var TocByCourseGateway
     */
    private $tocByCourseGateway;

    /**
     * @var int
     */
    private $courseId;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var PartResource
     */
    private $toc;

    /**
     * @var PartResource
     */
    private $parent;

    /**
     * @var PartResource
     */
    private $child;

    /**
     * @var PartResource
     */
    private $createdChild;

    /**
     * @var bool
     */
    private $find = false;

    /**
     * @var array
     */
    private $oldTocIndex = array();

    /**
     * @var PartResource
     */
    private $newToc;

    /**
     * @var PartResource
     */
    private $newElement;

    public function __construct($tocByCourseGateway)
    {
        $this->tocByCourseGateway = $tocByCourseGateway;
    }

    public function execute(UseCaseRequest $useCaseRequest)
    {
        /** @var AddElementToTocRequestDTO $useCaseRequest */
        $this->parentId = $useCaseRequest->parentId;
        $this->courseId = $useCaseRequest->courseId;

        $this->toc = $this->tocByCourseGateway->findByStatus(
            $this->courseId,
            array(CourseResource::STATUS => CourseResource::STATUS_DRAFT)
        );

        $this->parent = $this->toc;
        $this->oldTocIndex[] = $this->toc->getId();
        $this->addChild();
        $this->save();

        $this->getNewElement();

        $this->buildResponse();

        return $this->response;
    }

    private function addChild()
    {
        /** @var PartResource $child */
        foreach ($this->parent->getChildren() as $this->child) {
            $this->oldTocIndex[] = $this->child->getId();
            if ($this->isElementAddable()) {
                $this->addChildToToc();
            } else {
                $this->parent = $this->child;
                self::addChild();
            }
        }
    }

    /**
     * @return bool
     */
    private function isElementAddable()
    {
        return !$this->find && $this->parent->getId() == $this->parentId;
    }

    private function addChildToToc()
    {
        $this->buildChild();
        $this->parent->addChild($this->createdChild);
        $this->find = true;
    }

    private function buildChild()
    {
        $partFactory = new PartFactoryImpl();
        if (!isset(self::$correspondingChildSubtype[$this->parent->getSubtype()])) {
            throw new \DomainException();
        }
        $this->createdChild = $partFactory->make(
            self::$correspondingChildSubtype[$this->parent->getSubtype()]
        );
        $this->createdChild->setTitle('Sans titre');
    }

    private function save()
    {
        if ($this->find) {
            $this->newToc = $this->tocByCourseGateway->update($this->courseId, $this->toc);
        }
    }

    private function getNewElement()
    {

        $this->findNewElement($this->newToc);
    }

    private function findNewElement(PartResource $parent)
    {
        if (!in_array($parent->getId(), $this->oldTocIndex)) {
            $this->newElement = $parent;
        } else {
            foreach ($parent->getChildren() as $child) {
                $this->findNewElement($child);
            }
        }
    }

    private function buildResponse()
    {
        $this->response = new AddElementToTocResponseDTO();
        $this->response->toc = $this->toc;
        $this->response->newElement = $this->newElement;
    }
}
