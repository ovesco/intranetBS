<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attribution;
use AppBundle\Entity\Membre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        $membre = $this->getUser()->getMembre();

        /** @var Attribution[] $attributions */
        $attributions = $membre->getActiveAttributions();

        /** @var Groupe[] $groups */
        $groups = array();

        /** @var Attribution $attribution */
        foreach($attributions as $attribution) {
            $groups[] = $attribution->getGroupe();
        }



        return $this->render('Layout/main_menu.html.twig', array(
            'groups' => $groups
        ));
    }
}
