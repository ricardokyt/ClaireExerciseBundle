<?php
namespace SimpleIT\ClaireExerciseBundle\Controller;

use SimpleIT\ClaireExerciseBundle\Exception\Api\ApiBadRequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

/**
 * API Controller
 *
 * @author Baptiste Cablé <baptiste.cable@liris.cnrs.fr>
 */
abstract class BaseController extends Controller
{
    /**
     * Get the current user's id
     *
     * @throws InsufficientAuthenticationException
     * @return int
     */
    protected function getUserId()
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->get('security.context')->getToken()->getUser()->getId();
        } else {
            throw new InsufficientAuthenticationException();
        }
    }

    /**
     * Get the current user's id or null if she/he is a creator
     *
     * @return int
     */
    protected function getUserIdIfNoCreator()
    {
        if (!$this->get('security.context')->isGranted('ROLE_WS_CREATOR')) {
            return $this->getUserId();
        } else {
            return null;
        }
    }

    /**
     * Validate a resource
     *
     * @param mixed   $resource         Resource to validate
     * @param array   $validationGroups Validation groups
     * @param Boolean $traverse         Whether to traverse the value if it is traversable.
     * @param Boolean $deep             Whether to traverse nested traversable values recursively.
     *
     * @throws \SimpleIT\ClaireExerciseBundle\Exception\Api\ApiBadRequestException
     */
    protected function validateResource(
        $resource,
        $validationGroups = array(),
        $traverse = false,
        $deep = false
    )
    {
        $violations = $this->get('validator')->validate(
            $resource,
            $validationGroups,
            $traverse,
            $deep
        );

        $formatedErrors = array();
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $formatedErrors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new ApiBadRequestException($formatedErrors);
        }
    }
}