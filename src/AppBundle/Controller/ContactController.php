<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Email;
use AppBundle\Entity\Telephone;
use AppBundle\Form\AddEmailType;
use AppBundle\Form\AddTelephoneType;
use AppBundle\Entity\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContactController
 * @package AppBundle\Controller
 *
 * @Route("/contact")
 */
class ContactController extends Controller{

    /**
     * @Route("/telephone/get_form_modale", name="get_form_modale_telephone", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function getTelephoneModaleFormAjaxAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {

            /*
             * On récupère les infos dans la requete
             */
            $idContact = $request->request->get('idContact');


            $telephone = new Telephone();

            $telephoneForm = $this->createForm(new AddTelephoneType(),$telephone,
                array('action' => $this->generateUrl('add_telephone_to_contact')));

            $telephoneForm->get('contact_id')->setData($idContact);

                return $this->render('AppBundle:Telephone:telephone_modale_form.html.twig',array('form'=>$telephoneForm->createView()));


        }
        return new Response();
    }

    /**
     * @Route("/telephone/add", name="add_telephone_to_contact")
     * @param Request $request
     * @return Response
     */
    public function AddTelephoneToContactAction(Request $request)
    {
        $telephone = new Telephone();
        $telephoneForm = $this->createForm(new AddTelephoneType(),$telephone);

        $telephoneForm->handleRequest($request);

        if($telephoneForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $idContact = $telephoneForm->get('contact_id')->getData();


            /** @var Contact $contact */
            $contact = $em->getRepository('AppBundle:Contact')->find($idContact);

            $contact->addTelephone($telephone);

            $em->persist($contact);
            $em->persist($telephone);
            $em->flush();
        }

        //url of the current page
        $currentUrl = $request->headers->get('referer');


        return $this->redirect($currentUrl);

    }


    /**
     * @Route("/email/get_form_modale", name="get_form_modale_email", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function getEmailModaleFormAjaxAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {

            /*
             * On récupère les infos dans la requete
             */
            $idContact = $request->request->get('idContact');


            $email = new Email();

            $emailForm = $this->createForm(new AddEmailType(),$email,
                array('action' => $this->generateUrl('add_email_to_contact')));

            $emailForm->get('contact_id')->setData($idContact);

            return $this->render('AppBundle:Email:email_modale_form.html.twig',array('form'=>$emailForm->createView()));


        }
        return new Response();
    }


    /**
     * @Route("/email/add", name="add_email_to_contact")
     * @param Request $request
     * @return Response
     */
    public function AddEmailToContactAction(Request $request)
    {
        $email = new Email();
        $emailForm = $this->createForm(new AddEmailType(),$email);

        $emailForm->handleRequest($request);

        if($emailForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $idContact = $emailForm->get('contact_id')->getData();


            /** @var Contact $contact */
            $contact = $em->getRepository('AppBundle:Contact')->find($idContact);

            $contact->addEmail($email);

            $em->persist($contact);
            $em->persist($email);
            $em->flush();
        }

        //url of the current page
        $currentUrl = $request->headers->get('referer');

        return $this->redirect($currentUrl);

    }





}