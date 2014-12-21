<?php

namespace AppBundle\Controller;

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
        return $this->render('Layout/main_menu.html.twig');
    }
}
