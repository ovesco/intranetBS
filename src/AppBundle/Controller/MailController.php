<?php

namespace AppBundle\Controller;

use AppBundle\Form\Mail\MailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Interne\SecurityBundle\Entity\User;
use AppBundle\Utils\Menu\Menu;
use AppBundle\Entity\Receiver;
use AppBundle\Entity\Mail;

/**
 * Class ReceiverController
 * @package MailBundle\Controller
 * @Route("/mail")
 */
class MailController extends Controller
{
    /**
     * @Route("/add_to_receiver/{receiver}", options={"expose"=true})
     * @param Request $request
     * @ParamConverter("receiver", class="AppBundle:Receiver")
     * @param Receiver $receiver
     * @return Response
     * @Template("AppBundle:Mail:form_modal.html.twig")
     */
    public function addToReceiverAction(Request $request, Receiver $receiver)
    {
        $mail = new Mail();

        $form = $this->createForm(new MailType(),$mail,
            array('action' => $this->generateUrl('app_mail_addtoreceiver',array('receiver'=>$receiver->getId()))));

        $form->handleRequest($request);

        if ($form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();
            $mail->setSender($user->getMembre()->getSender());

            // $file stores the uploaded PDF file
            /** @var UploadedFile $file */
            $file = $mail->getDocument()->getFile();

            $filePath = $this->get('document_storage')->saveUploadedDocument($file);
            $mail->getDocument()->setFile($filePath);
            $mail->getDocument()->setName($file->getClientOriginalName());

            $receiver->addMail($mail);

            $em = $this->getDoctrine()->getManager();
            $em->persist($mail);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return array('form'=>$form->createView());
    }


    /**
     * @Route("/my_mail", options={"expose"=true})
     * @Menu("Ma liste d'envois",block="envois",icon="send")
     * @param Request $request
     * @return Response
     * @Template("AppBundle:Mail:page_my_mail.html.twig")
     */
    public function myMailAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        return array('sender'=>$user->getMembre()->getSender());
    }
}
