<?php

namespace Interne\FinancesBundle\Controller;

/* Symfony */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* Entity */
use Interne\FinancesBundle\Entity\Debiteur;

/* routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class DebiteurController
 * @package Interne\FinancesBundle\Controller
 * @Route("/debiteur")
 */
class DebiteurController extends Controller
{

    /**
     * @Route("/show/{debiteur}", options={"expose"=true})
     * @param Request $request
     * @param Debiteur $debiteur
     * @ParamConverter("debiteur", class="InterneFinancesBundle:Debiteur")
     * @Template("InterneFinancesBundle:Debiteur:show.html.twig")
     * @return Response
     */
    public function showAction(Request $request,Debiteur $debiteur)
    {
        return array('debiteur'=>$debiteur);
    }


}
