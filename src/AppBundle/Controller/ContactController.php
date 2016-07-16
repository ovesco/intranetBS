<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Contact;
use AppBundle\Entity\Email;
use AppBundle\Entity\Telephone;
use AppBundle\Form\AddEmailType;
use AppBundle\Form\AddTelephoneType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContactController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/contact")
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

    /**
     * @Route("/telephone/add", name="add_telephone_to_contact")
     * @param Request $request
     * @return Response
     * Génère la modale qui permet d'ajouter un téléphone et valide un éventuel numéro
     */
    public function renderModalTelephoneAction(Request $request) {

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

            $currentUrl = $request->headers->get('referer');

            // Si on a validé quelque chose, alors on actualise la page depuis où on l'a fait
            return $this->redirect($currentUrl);
        }

        return $this->render('AppBundle:Telephone:modal_add_telephone.html.twig', array('telephoneForm' => $telephoneForm->createView()));
    }

    /**
     * @Route("/email/add", name="add_email_to_contact")
     * @param Request $request
     * @return Response
     * Génère la modale qui permet d'ajouter un email
     */
    public function renderModalEmailAction(Request $request) {

        $email          = new Email();
        $emailForm      = $this->createForm(new AddEmailType(),$email);

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

            $currentUrl = $request->headers->get('referer');

            // Si on a validé quelque chose, alors on actualise la page depuis où on l'a fait
            return $this->redirect($currentUrl);
        }

        return $this->render('AppBundle:Email:modal_add_email.html.twig', array('emailForm' => $emailForm->createView()));
    }


    /**
     * Supprime un e-mail
     *
     * @Route("/email/remove/{object}", name="interne_contact_remove_email", options={"expose"=true})
     * @ParamConverter("email", class="AppBundle:Email")
     * @param $email
     * @return JsonResponse
     */
    public function removeEmailAction($email)
    {

        $em = $this->getDoctrine()->getManager();

        $em->remove($email);
        $em->flush();

        return new JsonResponse();
    }

    /**
     * Supprime un telephone
     * @Route("/telephone/remove/{object}", name="interne_contact_remove_telephone", options={"expose"=true})
     * @ParamConverter("telephone", class="AppBundle:Telephone")
     *
     * @param $telephone
     * @return JsonResponse
     */
    public function removeTelephoneAction($telephone)
    {

        $em = $this->getDoctrine()->getManager();

        $em->remove($telephone);
        $em->flush();

        return new JsonResponse();
    }
}