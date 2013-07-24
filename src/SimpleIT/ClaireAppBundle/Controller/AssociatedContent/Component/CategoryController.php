<?php


namespace SimpleIT\ClaireAppBundle\Controller\AssociatedContent\Component;

use SimpleIT\AppBundle\Controller\AppController;
use SimpleIT\Utils\Collection\CollectionInformation;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 *
 * @author Romain Kuzniak <romain.kuzniak@simple-it.fr>
 */
class CategoryController extends AppController
{

    /**
     * Get a list of categories
     *
     * @param CollectionInformation $collectionInformation Collection information
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(CollectionInformation $collectionInformation)
    {
        $categories = $this->get('simple_it.claire.associated_content.category')->getAll(
            $collectionInformation
        );

        return $this->render(
            'SimpleITClaireAppBundle:AssociatedContent/Category/Component:list.html.twig',
            array('categories' => $categories)
        );
    }

    /**
     * Get a list of courses of a category
     *
     * @param CollectionInformation $collectionInformation Collection information
     * @param mixed                 $categoryIdentifier    Category id | slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCoursesAction(CollectionInformation $collectionInformation, $categoryIdentifier)
    {
        $courses = $this->get('simple_it.claire.associated_content.category')->getAllCourses(
            $categoryIdentifier,
            $collectionInformation
        );

        return $this->render(
            'SimpleITClaireAppBundle:Course/Course/Component:list.html.twig',
            array('courses' => $courses)
        );
    }

    /**
     * View a category
     *
     * @param int | string $categoryIdentifier Category id | slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($categoryIdentifier)
    {
        $category = $this->get('simple_it.claire.associated_content.category')->get(
            $categoryIdentifier
        );

        return $this->render(
            'SimpleITClaireAppBundle:AssociatedContent/Category/Component:view.html.twig',
            array('category' => $category)
        );
    }
}