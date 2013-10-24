<?php

namespace SimpleIT\ClaireAppBundle\Repository\Course;

use SimpleIT\AppBundle\Repository\AppRepository;
use SimpleIT\Utils\Collection\CollectionInformation;
use SimpleIT\AppBundle\Annotation\Cache;

/**
 * Class MetadataByCourseRepository
 *
 * @author Romain Kuzniak <romain.kuzniak@simple-it.fr>
 */
class MetadataByCourseRepository extends AppRepository
{
    /**
     * @type string
     */
    protected $path = 'courses/{courseIdentifier}/metadatas/{metadataIdentifier}';

    /**
     * @type string
     */
    protected $resourceClass = 'array';

    /**
     * Find metadatas
     *
     * @param int | string          $courseIdentifier      Course id | slug
     * @param CollectionInformation $collectionInformation Collection information
     *
     * @return array
     * @cache (namespacePrefix="claire_app_course_course", namespaceAttribute="courseIdentifier", lifetime=0)
     */
    public function findAll($courseIdentifier, CollectionInformation $collectionInformation = null)
    {
        return parent::findAllResources(
            array('courseIdentifier' => $courseIdentifier),
            $collectionInformation
        );
    }

    /**
     * Find metadatas to edit (No cache)
     *
     * @param int|string            $courseIdentifier      Course id | slug
     * @param CollectionInformation $collectionInformation Collection information
     *
     * @return array
     */
    public function findAllToEdit(
        $courseIdentifier,
        CollectionInformation $collectionInformation = null
    )
    {
        return parent::findAllResources(
            array('courseIdentifier' => $courseIdentifier),
            $collectionInformation
        );
    }

    /**
     * Insert metadatas
     *
     * @param string $courseIdentifier Course id | slug
     * @param array  $metadatas        Metadatas (key => value)
     * @param array  $parameters       Parameters
     *
     * @return array
     */
    public function insert($courseIdentifier, $metadatas, $parameters = array())
    {
        return $metadatasInserted = parent::insertResource(
            $metadatas,
            array('courseIdentifier' => $courseIdentifier),
            $parameters
        );

    }

    /**
     * Update metadatas
     *
     * @param string $courseIdentifier Course id | slug
     * @param array  $metadatas        Metadatas (key => value)
     * @param array  $parameters       Parameters
     *
     * @return array
     */
    public function update($courseIdentifier, $metadatas, $parameters = array())
    {
        $metadatasUpdated = array();
        foreach ($metadatas as $key => $value) {
            $metadatasUpdated[] = parent::updateResource(
                $metadatas,
                array('courseIdentifier' => $courseIdentifier, 'metadataIdentifier' => $key),
                $parameters
            );
        }

        return $metadatasUpdated;
    }

    /**
     * Delete metadatas
     *
     * @param string $courseIdentifier Course id | slug
     * @param array  $metadatas        Metadatas (key => value)
     * @param array  $parameters       Parameters
     *
     * @return array
     */
    public function delete($courseIdentifier, $metadatas, $parameters = array())
    {
        foreach ($metadatas as $key => $value) {
            parent::deleteResource(
                $metadatas,
                array(
                    'courseIdentifier'   => $courseIdentifier,
                    'metadataIdentifier' => $key
                ),
                $parameters
            );
        }
    }
}
