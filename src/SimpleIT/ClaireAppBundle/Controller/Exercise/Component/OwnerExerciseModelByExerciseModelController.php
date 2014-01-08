<?php

namespace SimpleIT\ClaireAppBundle\Controller\Exercise\Component;

use SimpleIT\AppBundle\Controller\AppController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class OwnerExerciseModelByExerciseModelController
 *
 * @author Baptiste Cablé <baptiste.cable@liris.cnrs.fr>
 */
class OwnerExerciseModelByExerciseModelController extends AppController
{
    /**
     * Add an exercise model to the personal space: create an owner exercise model
     *
     * @param $exerciseModelId
     *
     * @return JsonResponse
     */
    public function addToPersoAction($exerciseModelId)
    {
        // TODO user
        $userId = 1000001;

        $ownerExerciseModel = $this->get('simple_it.claire.exercise.owner_exercise_model')->addToPerso(
            $exerciseModelId,
            $userId
        );

        return new JsonResponse(array(
            "id"       => $ownerExerciseModel->getId(),
            "metadata" => $ownerExerciseModel->getMetadata(),
            "type"     => $ownerExerciseModel->getType()
        ));
    }
}
