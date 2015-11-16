<?php

namespace Interne\MailBundle\Controller;

use Interne\MailBundle\Entity\Receiver;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReceiverController
 * @package Interne\MailBundle\Controller
 * @Route("/receiver")
 */
class ReceiverController extends Controller
{
    /**
     * @Route("/show/{receiver}", options={"expose"=true})
     * @param Request $request
     * @ParamConverter("receiver", class="InterneMailBundle:Receiver")
     * @param Receiver $receiver
     * @return Response
     * @Template("InterneMailBundle:Receiver:show.html.twig")
     */
    public function showAction(Request $request, Receiver $receiver)
    {
        return array('receiver'=>$receiver);
    }
}
