<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Utils\Envoi\ListeEnvoi;
use AppBundle\Utils\Email\Email;
use \Swift_Attachment;
use AppBundle\Utils\Export\Pdf;


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
     * @Template("Envoi/listeEnvoi.html.twig")
     * @return Response
     */
    public function listeAction()
    {
        $listeEnvoi = $this->get('listeEnvoi');

        return array('listeEnvoi'=>$listeEnvoi);
    }


    /**
     * @route("/clear", name="utils_envoi_clear")
     * @Template("Envoi/listeEnvoi.html.twig")
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
     * @route("/remove_token/{token}", name="utils_envoi_remove_by_token")
     * @Template("Envoi/listeEnvoi.html.twig")
     * @return Response
     */
    public function removeByToken($token)
    {
        $listeEnvoi = $this->get('listeEnvoi');
        $listeEnvoi->removeEnvoiByTocken($token);
        $listeEnvoi->save();
        unset($listeEnvoi);

        return $this->redirect($this->generateUrl('utils_envoi_liste'));
    }


    /**
     * Cette fonction crée un Pdf avec tout les courriers à envoyer sous format
     * papier (méthode: Courrier). Elle supprime de la liste chaque envois qui
     * a été imprimer en Pdf.
     *
     *
     * @route("/print_pdf", name="utils_envoi_print_pdf")
     *
     * @return Pdf
     */
    public function printPdfAction()
    {
        $listeEnvoi = $this->get('listeEnvoi');
        $pdf = $this->get('Pdf'); //call service
        //$pdf = $listeEnvoi->printPdfToCourrier($pdf);


        $arrayEnvois = $listeEnvoi->getEnvois();

        foreach($arrayEnvois as $envoi)
        {
            /*
             * TODO: On peut amélioré cette fonction en regroupant les envois de chaque cible.
             * TODO: par exemple, une facture et une circulaire à la meme famille pourrait être regroupée.
             */

            $adresse = $envoi['owner']->getAdressePrincipale();
            $methodeEnvoi = $adresse['adresse']->getMethodeEnvoi();
            $pdfToAdd = $envoi['envoi']->documentPDF;

            if($methodeEnvoi == 'Courrier')
            {
                $pdfToAdd->addAdresseEnvoi($adresse);
                $pdf->AddPageWithPdf($pdfToAdd);

                $this->removeEnvoiByTocken($envoi['token']);

            }

        }

        $listeEnvoi->save();

        return $pdf->Output('','I');
    }

    /**
     * @route("/send_email", name="utils_envoi_email")
     *
     * @return Pdf
     */
    public function sendEmailAction()
    {
        $listeEnvoi = $this->get('listeEnvoi'); //call service
        $email = $this->get('email'); //call service
        $mailer = $this->get('mailer');

        $arrayEnvois = $listeEnvoi->getEnvois();

        foreach($arrayEnvois as $envoi)
        {
            $adresse = $envoi['owner']->getAdressePrincipale();
            $methodeEnvoi = $adresse['adresse']->getMethodeEnvoi();

            if($methodeEnvoi == 'Email')
            {
                $pdf = $envoi['envoi']->documentPDF;
                $pdf->addAdresseEnvoi($adresse);



                //Attribue un nom de fichier temporaire
                $kernel = $this->get('kernel');
                $path = $kernel->getRootDir() . '/cache/' . $kernel->getEnvironment().'/temporary_email';
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $filePath = $path.'/pdf_tmp_'.$envoi['token'].'.pdf';;

                //on sauve le fichier dans le dossier temporaire
                $pdf->Output($filePath,'F');


                $adresseEmail = $adresse['adresse']->getEmail();

                $emailToSend = clone $email;
                $emailToSend
                    ->setSubject('Envoi automatique de documents')
                    ->setFrom('noreply@sauvabelin.ch')
                    ->setTo($adresseEmail)
                    ->setBody($this->renderView('Envoi/email_envoi.txt.twig', array('envoi'=>$envoi)))
                    ->attachFile($filePath,$envoi['envoi']->description.'.pdf');

                $mailer->send($emailToSend);

                //TODO: faire une tache CRON avec la commande : php app/console swiftmailer:spool:send --env=dev
                //TODO: supprimer ensuite les fichiers temporaire.

            }
        }

        return $this->redirect($this->generateUrl('utils_envoi_liste'));
    }


}