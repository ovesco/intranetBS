<?php

namespace AppBundle\Controller;

/* Symfony */
use AppBundle\Utils\Response\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\Debiteur;
use AppBundle\Utils\Finances\Facturation;


/**
 * Class DebiteurController
 * @package AppBundle\Controller
 * @Route("/intranet/debiteur")
 */
class DebiteurController extends Controller
{

    /**
     * @Route("/show/{debiteur}", options={"expose"=true})
     * @param Request $request
     * @param Debiteur $debiteur
     * @ParamConverter("debiteur", class="AppBundle:Debiteur")
     * @Template("AppBundle:Debiteur:show.html.twig")
     * @return Response
     */
    public function showAction(Request $request,Debiteur $debiteur)
    {
        return array('debiteur'=>$debiteur);
    }


    /**
     * cette methode permet de facturé tout les créances ouverte
     * d'un débiteur
     *
     * @Route("/facturation/{debiteur}", options={"expose"=true})
     * @param Request $request
     * @param Debiteur $debiteur
     * @ParamConverter("debiteur", class="AppBundle:Debiteur")
     * @return Response
     */
    public function facturationAction(Request $request,Debiteur $debiteur)
    {
        /** @var Facturation $facturation */
        $facturation = $this->get('app.facturation');
        $facturation->facturationDebiteur($debiteur);

        return ResponseFactory::ok();
    }

}
