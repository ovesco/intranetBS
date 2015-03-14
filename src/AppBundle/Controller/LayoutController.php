<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Le layoutController s'occupe de génerer les éléments du layout comme le menu en fonction
 * des utilisateurs
 * Class LayoutController
 * @package AppBundle\Controller
 */
class LayoutController extends Controller
{

    /**
     * Génère le menu principal.
     */
    public function mainMenuGenerateAction()
    {
        /** @var Membre $membre */
        // FIXME : le suer devrait être validé en amont
        if ($this->getUser() != null) {
            $membre = $this->getUser()->getMembre();
            $groupes = $membre->getActiveGroupes();
        }
        else {
            $groupes = array();
        }


        return $this->render('AppBundle:Layout:main_menu.html.twig', array(
            'groups' => $groupes
        ));
    }
}
