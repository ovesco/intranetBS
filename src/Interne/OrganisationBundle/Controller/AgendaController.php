<?php

namespace Interne\OrganisationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController
 * @package Interne\OrganisationBundle\Controller
 * @route("/agenda")
 */
class AgendaController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @route("", name="interne_organisation_agenda_show")
     */
    public function showAction()
    {
        return $this->render('InterneOrganisationBundle:Agenda:page_agenda.html.twig');
    }
}
