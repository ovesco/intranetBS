<?php

namespace Interne\OrganisationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController
 * @package Interne\OrganisationBundle\Controller
 * @route("/dev")
 */
class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @route("/default", name="interne_organisation_default")
     */
    public function indexAction()
    {
        return $this->render('InterneOrganisationBundle:Default:index.html.twig');
    }
}
