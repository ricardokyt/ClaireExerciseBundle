<?php


namespace SimpleIT\ClaireAppBundle\Repository\Course;

use SimpleIT\AppBundle\Repository\AppRepository;
use SimpleIT\Utils\FormatUtils;

/**
 * Class MetadataByPartRepository
 *
 * @author Romain Kuzniak <romain.kuzniak@simple-it.fr>
 */
class MetadataByPartRepository extends AppRepository
{
    /**
     * @var string
     */
    protected $path = 'courses/{courseIdentifier}/parts/{partIdentifier}/metadatas/{metadataIdentifier}';

    /**
     * @var  string
     */
    protected $resourceClass = 'array';

    /**
     * Find metadatas
     *
     * @param string $courseIdentifier Course id | slug
     * @param string $partIdentifier   Part id | slug
     * @param array  $parameters       Parameters
     *
     * @return array
     */
    public function find($courseIdentifier, $partIdentifier, $parameters = array())
    {
        return parent::findResource(
            array('courseIdentifier' => $courseIdentifier, 'partIdentifier' => $partIdentifier),
            $parameters
        );
    }

    /**
     * Insert metadatas
     *
     * @param string $courseIdentifier Course id | slug
     * @param string $partIdentifier   Part id | slug
     * @param array  $metadatas        Metadatas (key => value)
     * @param array  $parameters       Parameters
     *
     * @return array
     */
    public function insert($courseIdentifier, $partIdentifier, $metadatas, $parameters = array())
    {
        $metadatasInserted = parent::insertResource(
            $metadatas,
            array('courseIdentifier' => $courseIdentifier, 'partIdentifier' => $partIdentifier),
            $parameters
        );

        return $metadatasInserted;
    }

    /**
     * Update metadatas
     *
     * @param string $courseIdentifier Course id | slug
     * @param string $partIdentifier   Part id | slug
     * @param array  $metadatas        Metadatas (key => value)
     * @param array  $parameters       Parameters
     *
     * @return array
     */
    public function update($courseIdentifier, $partIdentifier, $metadatas, $parameters = array())
    {
        $metadatasUpdated = array();
        foreach ($metadatas as $key => $value) {
            $metadatasUpdated = parent::updateResource(
                $metadatas,
                array(
                    'courseIdentifier'   => $courseIdentifier,
                    'partIdentifier'     => $partIdentifier,
                    'metadataIdentifier' => $key
                ),
                $parameters
            );
        }

        return $metadatasUpdated;
    }

    /**
     * Delete metadatas
     *
     * @param string $courseIdentifier Course id | slug
     * @param string $partIdentifier   Part id | slug
     * @param array  $metadatas        Metadatas (key => value)
     * @param array  $parameters       Parameters
     *
     * @return array
     */
    public function delete($courseIdentifier, $partIdentifier, $metadatas, $parameters = array())
    {
        foreach ($metadatas as $key => $value) {
            parent::deleteResource(
                $metadatas,
                array(
                    'courseIdentifier'   => $courseIdentifier,
                    'partIdentifier'     => $partIdentifier,
                    'metadataIdentifier' => $key
                ),
                $parameters
            );
        }
    }
}