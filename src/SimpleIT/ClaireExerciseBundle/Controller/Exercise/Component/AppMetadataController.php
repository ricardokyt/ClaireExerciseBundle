<?php

namespace SimpleIT\ClaireExerciseBundle\Controller\Exercise\Component;

use SimpleIT\Utils\Collection\CollectionInformation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ResourceController
 *
 * @author Baptiste Cablé <baptiste.cable@liris.cnrs.fr>
 */
abstract class AppMetadataController extends Controller
{
    /**
     * Create an array of metadata
     *
     * @param CollectionInformation $collectionInformation
     *
     * @return array
     */
    protected function metadataToArray(CollectionInformation $collectionInformation)
    {
        $filters = $collectionInformation->getFilters();

        $metadata = array();
        foreach ($filters as $key => $filter) {
            if (!empty($filter)) {
                $str = str_split($key, strlen('metaKey'));
                if ($str[0] == 'metaKey' && is_numeric($str[1])) {
                    $metadata[$filter] = $filters['metaValue' . $str[1]];
                }
            }
        }

        return $metadata;
    }

    /**
     * Create an array of keywords from collection information
     *
     * @param CollectionInformation $collectionInformation
     *
     * @return array
     */
    protected function miscToArray(CollectionInformation $collectionInformation)
    {
        $filters = $collectionInformation->getFilters();

        $misc = array();
        foreach ($filters as $key => $filter) {
            if (!empty($filter)) {
                $str = str_split($key, strlen('misc'));
                if ($str[0] == 'misc' && is_numeric($str[1])) {
                    $misc[] = $filter;
                }
            }
        }

        return $misc;
    }
}
