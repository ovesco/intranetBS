<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Utils\Envoi\ListeEnvoi;
use AppBundle\Utils\Email\Email;
use \Swift_Attachment;
use AppBundle\Utils\Export\Pdf;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EnvoiController
 * @package AppBundle\Controller
 *
 * @Route("/envoi")
 */
class EnvoiController extends Controller{


    /**
     * Genere la page principale d'action sur les envois
     *
     * @route("/liste", name="utils_envoi_liste")
     * @Template("AppBundle:Envoi:listeEnvoi.html.twig")
     * @return Response
     */
    public function listeAction()
    {
        $listeEnvoi = $this->get('listeEnvoi');

        return array('listeEnvoi'=>$listeEnvoi);
    }


    /**
     * @route("/clear", name="utils_envoi_clear")
     * @Template("AppBundle:Envoi:listeEnvoi.html.twig")
     * @return Response
     */
    public function clearAction()
    {
        $listeEnvoi = $this->get('listeEnvoi');
        $listeEnvoi->clearEnvois();

        return $this->redirect($this->generateUrl('utils_envoi_liste'));
    }

    /**
     * @param $token
     * @route("/remove_token/{token}", name="utils_envoi_remove_by_token", options={"expose"=true})
     * @Template("AppBundle:Envoi:listeEnvoi.html.twig")
     * @return Response
     */
    public function removeByToken($token)
    {
        $listeEnvoi = $this->get('listeEnvoi');
        $listeEnvoi->removeEnvoiByToken($token);
        $listeEnvoi->save();
        unset($listeEnvoi);

        return $this->redirect($this->generateUrl('utils_envoi_liste'));
    }



    /**
     *
     * @route("/document_form", name="utils_envoi_document_form", options={"expose"=true})
     * @param Request $request
     * @Template("AppBundle:Envoi:envoi_document_modale_form.html.twig")
     * @return Response
     */
    public function envoiDocuementAction(Request $request)
    {

        $envoiDocumentForm = $this->createFormBuilder();
        $envoiDocumentForm
            ->setAction($this->generateUrl('utils_envoi_document_form'))
            ->add('description','text',array('label'=>'Description du document'))
            ->add('document','file',array('label'=>'Document PDF'))
            ->add('idGroupe','hidden')
            ->add('tokenListe','hidden');
        $form = $envoiDocumentForm->getForm();

        if($request->isXmlHttpRequest())
        {
            /*
             * Demande pour le formulaire modale
             */
            $idGroupe = $request->request->get('idGroupe');
            $tokenListe = $request->request->get('tokenListe');



            if($idGroupe != null)
            {
                $form->get('idGroupe')->setData($idGroupe);
                $form->get('tokenListe')->setData(null);
            }
            if($tokenListe != null)
            {
                $form->get('idGroupe')->setData(null);
                $form->get('tokenListe')->setData($tokenListe);
            }

            return array('form'=>$form->createView());
        }
        else
        {

            $form->handleRequest($request);

            $description = $form->get('description')->getData();
            $file = $form->get('document')->getData();

            $path = $this->get('kernel')->getRootDir().'/cache/'.$this->get('kernel')->getEnvironment().'/temporary_pdf';

            $extension = $file->guessExtension();
            if (!$extension) {
                // l'extension n'a pas été trouvée
                $extension = 'bin';
            }
            if($extension == 'pdf')
            {

                $fileName = 'pdf_tmp_'.str_shuffle('1234567890abcdefghijk').'.'.$extension;
                $file->move($path,$fileName);
                $filePath = $path.'/'.$fileName;

                $idGroupe = $form->get('idGroupe')->getData();
                $tokenListe = $form->get('tokenListe')->getData();

                $em   = $this->getDoctrine()->getManager();


                if($idGroupe != null)
                {

                    $groupe = $em->getRepository('AppBundle:Groupe')->find($idGroupe);
                    $membres = $groupe->getMembersRecursive();

                    foreach($membres as $membre)
                    {
                        $listeEnvoi = $this->get('listeEnvoi');
                        $listeEnvoi->addEnvoiWithPath($membre->getId(),'Membre', $filePath, $description );
                    }

                }

            }


            return $this->redirect($this->generateUrl('utils_envoi_liste'));
        }




    }




    /**
     * @route("/process", name="utils_envoi_process" , options={"expose"=true})
     */
    public function processAction()
    {

        /*
         * Todo Voir si il y a pas moyen de enelever la limit de mémoire.
         */
        ini_set('memory_limit', '-1'); //avoid memory limit exception!!!

        $listeEnvoi = $this->get('listeEnvoi'); //call service
        $pdf = $this->get('Pdf'); //call service


        $arrayEnvois = $listeEnvoi->getEnvois();

        /*
         * Phase 1: regroupement des envois par cible.
         */
        $regroupementAdresse = array();
        $regroupementEmails = array();

        foreach($arrayEnvois as $envoi) {

            $token = $envoi['token'];

            $adresse = $envoi['owner']->getAdresseExpedition();
            if($adresse != null)
            {
                /*
                 * Il y a un envoi par courrier
                 */
                $key = $adresse['adresse']->getId();


                /*
                 * On sauve tout les token d'envoi ayant la même adresse
                 */
                if(isset($regroupementAdresse[$key])){
                    array_push($regroupementAdresse[$key],$token);
                }
                else{
                    $regroupementAdresse[$key] = array($token);
                }
            }

            $emails = $envoi['owner']->getListeEmailsExpedition();
            if(!empty($emails))
            {
                /*
                 * Il y a des envois par emails
                 */
                foreach($emails as $email)
                {

                    if(isset($regroupementEmails[$email])){
                        array_push($regroupementEmails[$email],$token);
                    }
                    else{
                        $regroupementEmails[$email] = array($token);
                    }

                }
            }



        }

        /*
         * Phase 2: traitement des Emails
         */
        if(!empty($regroupementEmails))
        {
            $email = $this->get('email'); //call service
            $mailer = $this->get('mailer'); //call service
            $kernel = $this->get('kernel'); //call service
            $parametres = $this->get('parametres'); //call service

            //Lien sur le dossier temporaire des documents (création si nécaissaire)
            $pathTempDir = $kernel->getRootDir() . '/cache/' . $kernel->getEnvironment().'/temporary_email';
            if (!file_exists($pathTempDir)) {
                mkdir($pathTempDir, 0777, true);
            }

            /*
             * Création des emails
             */
            foreach($regroupementEmails as $adresseEmail => $tokens)
            {

                $senderMail = $parametres->getValue('service_mailer','noReplyEmail');
                $nomGroupe = $parametres->getValue('info_groupe','nom');
                $emailToSend = clone $email;
                $emailToSend
                    ->setSubject($nomGroupe.': Envoi automatique de documents')
                    ->setFrom(array($senderMail=>$nomGroupe))
                    //->setSender($senderMail)
                    ->setTo($adresseEmail);



                /*
                 * Pièce jointes
                 */
                $emailBodyContent = array();
                foreach($tokens as $token)
                {
                    $filePath = $pathTempDir.'/pdf_to_send_tmp_'.$token.'.pdf';
                    $envoi = $listeEnvoi->getEnvoiByToken($token);

                    $pdfPath = $envoi['envoi']->documentPdfPath;

                    $documentPdf = Pdf::getInTemporaryFolder($pdfPath,$kernel);

                    //on sauve le fichier dans le dossier temporaire pour les emails
                    $documentPdf->Output($filePath,'F');

                    $emailToSend->attachFile($filePath,$envoi['envoi']->description.'.pdf');

                    $owner = $envoi['owner'];

                    $emailBodyContent[] = array(
                        'document'=>$envoi['envoi']->description.'.pdf',
                        'owner'=> ($owner->isClass('Membre') ? $owner->getPrenom().' '.$owner->getNom(): 'Famille '.$owner->getNom()));

                }

                $emailToSend->setBody($this->renderView('AppBundle:Envoi:email_envoi.txt.twig', array('content'=>$emailBodyContent)));


                $mailer->send($emailToSend);
                //TODO: faire une tache CRON avec la commande : php app/console swiftmailer:spool:send --env=dev
                //TODO: supprimer ensuite les fichiers temporaire.



            }
        }


        /*
         * Phase 3: traitement des PDFs
         *
         */
        if(!empty($regroupementAdresse))
        {
            $arrayOfDocumentPath = array();

            $kernel = $this->get('kernel'); //call service


            $numEnvoi = 1;
            foreach($regroupementAdresse as $adresseId => $tokens)
            {
                $nbDocuments = count($tokens);

                $numDocument = 1;

                foreach($tokens as $token)
                {
                    $envoi = $listeEnvoi->getEnvoiByToken($token);
                    $pdfPath = $envoi['envoi']->documentPdfPath;


                    /*
                     * On traite la première page de chaque document
                     */
                    $documentPdf = Pdf::getInTemporaryFolder($pdfPath,$kernel);
                    $firstPage = $documentPdf->getPage(1);
                    $firstPage->tagInTopRight('Envoi '.$numEnvoi.' ('.$numDocument.'/'.$nbDocuments.')');

                    if($numDocument == 1)
                    {
                        $adresse = $envoi['owner']->getAdresseExpedition();
                        $firstPage->addAdresseEnvoi($adresse);
                    }

                    /*
                     * On est obligé de rappeler le PDF depuis le dossier...c'est bizarre mais ca marche comme ca!
                     */
                    $documentPdf = Pdf::getInTemporaryFolder($pdfPath,$kernel);
                    $documentToSave = $documentPdf->replacePage(1,$firstPage);
                    $arrayOfDocumentPath[] = $documentToSave->saveInTemporaryFolder();

                    $numDocument++;
                }
                $numEnvoi++;
            }



            /*
             * Je n'appel volontairement pas le service car ca bug...
             * Du coup ca marche comme ca!
             */
            $pdfOutput = new Pdf($kernel);
            $pdfOutput->fusionOfDocuments($arrayOfDocumentPath);

            $listeEnvoi->clearEnvois();

            return $pdfOutput->Output('','I');
        }
        return new Response();


    }







}