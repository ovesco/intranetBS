<?php

namespace Interne\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller
{

    /**
     * Petite redirection vers la page de login
     * @Route("", name="security_redirect_login")
     */
    public function baseIndexAction() {

        return $this->redirect($this->generateUrl('security_login'));
    }

    /**
     * Affiche le formulaire de login
     * @param Request $request la requete
     * @return Response la vue
     * @Route("/login", name="security_login")
     */
    public function loginAction(Request $request) {

        $session = $request->getSession();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR))
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);

        else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return $this->render('InterneSecurityBundle:Login:page_login.html.twig', array(

            'last_username' => $session->get(Security::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    /**
     * Méthode vide, on l'utilise juste pour que la route ait quelque chose sur lequel pointer
     * @Route("login_check", name="login_check")
     */
    public function checkAction(){

    }

    /**
     * Méthode vide, on l'utilise juste pour que la route ait quelque chose sur lequel pointer
     * @Route("logout", name="logout")
     */
    public function logoutAction(){

    }

}
