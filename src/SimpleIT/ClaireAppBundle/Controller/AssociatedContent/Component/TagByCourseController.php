<?php

namespace SimpleIT\ClaireAppBundle\Controller\AssociatedContent\Component;

use SimpleIT\AppBundle\Annotation\Cache;
use SimpleIT\ApiResourcesBundle\AssociatedContent\TagResource;
use SimpleIT\ApiResourcesBundle\Course\CourseResource;
use SimpleIT\AppBundle\Controller\AppController;
use SimpleIT\AppBundle\Util\RequestUtils;
use SimpleIT\Utils\Collection\CollectionInformation;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class TagByCourseController
 *
 * @author Romain Kuzniak <romain.kuzniak@simple-it.fr>
 */
class TagByCourseController extends AppController
{
    /**
     * Get a list of tags of a course
     *
     * @param CollectionInformation $collectionInformation Collection information
     * @param mixed                 $courseIdentifier      Course id | slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Cache
     */
    public function listAction(CollectionInformation $collectionInformation, $courseIdentifier)
    {
        $tags = $this->get('simple_it.claire.associated_content.tag')->getAllByCourse(
            $courseIdentifier,
            $collectionInformation
        );

        return $this->render(
            'SimpleITClaireAppBundle:AssociatedContent/Tag/Component:viewByCourse.html.twig',
            array('tags' => $tags)
        );
    }

    /**
     * Edit a list of tags (GET)
     *
     * @param CollectionInformation $collectionInformation Collection Information
     * @param int                   $courseId              Course id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listEditViewAction(CollectionInformation $collectionInformation, $courseId)
    {
        $tags = $this->get('simple_it.claire.associated_content.tag')->getAllByCourse(
            $courseId,
            $collectionInformation
        );
        $outputTags = array();

        /** @type TagResource $tag */
        foreach ($tags as $tag) {
            $outputTags[$tag->getId()] = $tag->getName();
        }

        $form = $this->createFormBuilder($outputTags)
            ->add(
                'tags',
                'text',
                array(
                    'required' => true
                )
            )
            ->getForm();

        return $this->render(
            'SimpleITClaireAppBundle:AssociatedContent/Tag/Component:editListByCourse.html.twig',
            array(
                'courseId' => $courseId,
                'form'     => $form->createView()
            )
        );
    }

    /**
     * Edit tags
     *
     * @param Request         $request          Request
     * @param integer |string $courseIdentifier Course id | slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editListAction(Request $request, $courseIdentifier)
    {
        $tags = array();
        if (RequestUtils::METHOD_GET == $request->getMethod()) {
            $tags = $this->get('simple_it.claire.associated_content.tag')->getAllByCourse(
                $courseIdentifier
            );
        }
        $tagsString = '';
        foreach ($tags as $tag) {
            if ($tagsString != '') {
                $tagsString .= ',';
            }

            $tagsString .= $tag->getName();
        }

        return $this->render(
            'editListByCourse.html.twig',
            array(
                'courseIdentifier' => $courseIdentifier,
                'tags'             => $tagsString
            )
        );
    }

    /**
     * Set status to draft if not defined in collection information
     *
     * @param CollectionInformation $collectionInformation Collection information
     *
     * @return CollectionInformation
     */
    protected function setStatusToDraftIfNotDefined(CollectionInformation $collectionInformation)
    {
        $status = $collectionInformation->getFilter(CourseResource::STATUS);
        if (is_null($status)) {
            $collectionInformation->addFilter(CourseResource::STATUS, CourseResource::STATUS_DRAFT);
        }

        return $collectionInformation;
    }
}
