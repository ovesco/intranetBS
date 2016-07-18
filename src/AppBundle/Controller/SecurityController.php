<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class SecurityController
 * @package AppBundle\Controller
 *
 * Le controller de la sécurité ne doit pas avoir le préfix "/intranet" afin que
 * la route / et /login soit atteniable avant le fierwall.
 *
 * @Route("")
 */
class SecurityController extends Controller
{

    /**
     * Petite redirection vers la page de login
     * @Route("", name="app_security_redirect")
     */
    public function redirectAction() {

        return $this->redirect($this->generateUrl('app_security_login'));
    }

    /**
     * Affiche le formulaire de login
     * @param Request $request la requete
     * @return Response la vue
     * @Route("/login", name="app_security_login")
     * @Template("AppBundle:Security:page_login.html.twig")
     */
    public function loginAction(Request $request) {

        $session = $request->getSession();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR))
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);

        else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(Security::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * Méthode vide, on l'utilise juste pour que la route ait quelque chose sur lequel pointer
     * @Route("/login_check", name="app_security_check")
     */
    public function checkAction(){

    }

    /**
     * Méthode vide, on l'utilise juste pour que la route ait quelque chose sur lequel pointer
     * @Route("/logout", name="app_security_logout")
     */
    public function logoutAction(){

    }

}
