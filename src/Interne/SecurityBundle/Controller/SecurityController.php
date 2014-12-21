<?php

namespace Interne\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
	/**
	 * offre la vue qui permet de gérer les roles et fonctions, lier les uns aux autres...
	 */
    public function rolesFonctionsAction()
    {
        return $this->render('InterneSecurityBundle:Security:roles_fonctions.html.twig');
    }
}
