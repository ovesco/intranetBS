<?php

namespace Interne\MailBundle\Controller;

use Interne\MailBundle\Entity\Receiver;
use Interne\MailBundle\Form\MailType;
use Interne\MailBundle\Entity\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ReceiverController
 * @package Interne\MailBundle\Controller
 * @Route("/mail")
 */
class MailController extends Controller
{
    /**
     * @Route("/add_to_receiver/{receiver}", options={"expose"=true})
     * @param Request $request
     * @ParamConverter("receiver", class="InterneMailBundle:Receiver")
     * @param Receiver $receiver
     * @return Response
     * @Template("InterneMailBundle:Mail:form_modal.html.twig")
     */
    public function addToReceiverAction(Request $request, Receiver $receiver)
    {
        $mail = new Mail();

        $form = $this->createForm(new MailType(),$mail,
            array('action' => $this->generateUrl('interne_mail_mail_addtoreceiver',array('receiver'=>$receiver->getId()))));

        $form->handleRequest($request);

        if ($form->isValid()) {
            // $file stores the uploaded PDF file
            /** @var UploadedFile $file */
            $file = $mail->getDocument()->getFile();

            $filePath = $this->get('interne_mail.document_storage')->saveUploadedDocument($file);
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
}
