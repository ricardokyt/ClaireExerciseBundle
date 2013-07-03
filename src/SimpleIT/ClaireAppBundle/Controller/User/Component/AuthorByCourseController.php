<?php


namespace SimpleIT\ClaireAppBundle\Controller\User\Component;

use SimpleIT\AppBundle\Controller\AppController;
use SimpleIT\AppBundle\Util\RequestUtils;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthorByCourse
 *
 * @author Romain Kuzniak <romain.kuzniak@simple-it.fr>
 */
class AuthorByCourseController extends AppController
{
    /**
     * Edit authors
     *
     * @param Request         $request Request
     * @param integer |string $courseIdentifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editListAction(Request $request, $courseIdentifier)
    {
        $authors = array();

        if (RequestUtils::METHOD_GET == $request->getMethod()) {
            $authors = $this->get('simple_it.claire.user.author')->getAllByCourse(
                $courseIdentifier
            );
        }
        $authorsString = '';
        foreach ($authors as $author) {
            if ($authorsString != '') {
                $authorsString .= ',';
            }
            $authorsString .= $author->getUsername();
        }

        return $this->render(
            'SimpleITClaireAppBundle:User/Author/Component:editByCourse.html.twig',
            array(
                'courseIdentifier' => $courseIdentifier,
                'authors'          => $authorsString
            )
        );
    }
}
