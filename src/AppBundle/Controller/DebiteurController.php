<?php

namespace AppBundle\Controller;

/* Symfony */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\Debiteur;


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


}
