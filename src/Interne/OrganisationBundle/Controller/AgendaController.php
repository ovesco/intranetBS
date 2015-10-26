<?php

namespace Interne\OrganisationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AgendaController
 * @package Interne\OrganisationBundle\Controller
 * @Route("/agenda")
 */
class AgendaController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("", name="interne_organisation_agenda_show")
     */
    public function showAction()
    {
        return $this->render('InterneOrganisationBundle:Agenda:page_agenda.html.twig');
    }
}
