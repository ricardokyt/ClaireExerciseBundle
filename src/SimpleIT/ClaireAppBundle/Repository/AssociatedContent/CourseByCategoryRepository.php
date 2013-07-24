<?php


namespace SimpleIT\ClaireAppBundle\Repository\AssociatedContent;

use SimpleIT\AppBundle\Repository\AppRepository;
use SimpleIT\Utils\Collection\CollectionInformation;
use SimpleIT\Utils\Collection\PaginatedCollection;

/**
 * Class CourseByCategoryRepository
 *
 * @author Romain Kuzniak <romain.kuzniak@simple-it.fr>
 */
class CourseByCategoryRepository extends AppRepository
{
    /**
     * @var string
     */
    protected $path = 'categories/{categoryIdentifier}/courses/';

    /**
     * @var  string
     */
    protected $resourceClass = 'SimpleIT\ApiResourcesBundle\Course\CourseResource';

    /**
     * Find all categories
     *
     * @param int |string           $categoryIdentifier    Category id | slug
     * @param CollectionInformation $collectionInformation Collection information
     *
     * @return PaginatedCollection
     */
    public function findAll($categoryIdentifier, CollectionInformation $collectionInformation = null)
    {
        return parent::findAllResources(
            array('categoryIdentifier' => $categoryIdentifier),
            $collectionInformation
        );
    }
}