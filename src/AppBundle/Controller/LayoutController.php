<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;

/**
 * Le layoutController s'occupe de génerer les éléments du layout comme le menu en fonction
 * des utilisateurs
 * Class LayoutController
 * @package AppBundle\Controller
 * @route("/intranet/layout")
 */
class LayoutController extends Controller
{

    /**
     * Génère le menu principal.
     */
    public function menuAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->hasMembre()) {
            $groupes = $user->getMembre()->getActiveGroupes();
        }
        else {
            $groupes = array();
        }


        return $this->render('AppBundle:Layout:main_menu.html.twig', array(
            'groups' => $groupes
        ));
    }
}
