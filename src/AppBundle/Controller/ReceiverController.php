<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Receiver;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ReceiverController
 * @package AppBundle\Controller
 * @Route("/intranet/receiver")
 */
class ReceiverController extends Controller
{
    /**
     * @Route("/show/{receiver}", options={"expose"=true})
     * @param Request $request
     * @ParamConverter("receiver", class="AppBundle:Receiver")
     * @param Receiver $receiver
     * @return Response
     * @Template("AppBundle:Receiver:show.html.twig")
     */
    public function showAction(Request $request, Receiver $receiver)
    {
        return array('receiver'=>$receiver);
    }
}
