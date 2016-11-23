<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Le controller de compte utilisateur. Réunit toutes les tâches relatives à la gestion du compte utilisateur d'un
 * membre
 * @Route("/intranet/account")
 * @package Interne\SecurityBundle\Controller
 */
class AccountController extends Controller
{
    /**
     * Affiche la page de gestion de compte
     * @Route("", name="app_account")
     */
    public function accountAction(){

        return $this->render('AppBundle:Account:page_account.html.twig');
    }



    /**
     * Permet à un utilisateur de modifier son mot de passe
     * @Route("/modify-password", name="security_modify_password")
     *
     * todo NUR passer un coup ici...
     *
     */
    public function modifyPasswordAction(Request $request) {

        $errors  = array();

        /** @var \Interne\SecurityBundle\Entity\User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $encoder = $this->get('security.password_encoder');
        $current = $encoder->encodePassword($user, $request->request->get('current_password'));
        $new     = $encoder->encodePassword($user, $request->request->get('new_password'));
        $repeat  = $encoder->encodePassword($user, $request->request->get('repeat_new_password'));

        if($current == $new)
            $errors[] = 'Le nouveau mot de passe est identique à l\'ancien';

        if($repeat != $new)
            $errors[] = 'Le nouveau mot de passe ne correspond pas à la vérification !';

        if(strlen($new) < 5)
            $errors[] = 'Mot de passe trop court (minimum 6 caractères';


        /*
         * Si on a pas d'erreur, on encode le nouveau mot de passe à l'aide du moteur d'encodage par défaut du firewall
         * puis on modifie la base de données, sinon on retourne sur la page et on affiche les erreurs
         */
        if(count($errors) == 0) {

            $em      = $this->getDoctrine()->getManager();

            $user->setPassword($new);
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Mot de passe modifié avec succès");
        }
        else
            foreach($errors as $error)
                $this->get('session')->getFlashBag()->add('error', $error);


        return $this->redirect($this->generateUrl('app_account'));
    }
}
