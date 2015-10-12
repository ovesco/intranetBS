<?php

namespace AppBundle\Controller;

use AppBundle\Utils\Email\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;


/**
 * Class BugReportController
 * @package AppBundle\Controller
 * @Route("/bug_report")
 */
class BugReportController extends Controller
{

    /**
     * Formulaire de rapport de bug.
     *
     * Ce formulaire apparait en modal. Il permet l'envoie d'un e-mail de l'utiliateur
     * au modérateur avec un détail du bug et le contenu html de la page courante.
     *
     * @Route("/form", name="debug_report_form", options={"expose"=true})
     * @param Request $request
     * @return mixed
     */
    public function bugReportFormAction(Request $request){

        if($request->isXmlHttpRequest())
        {
            /*
             * Construction du formulaire
             */
            $form = $this->createFormBuilder()
                ->add('action', 'textarea',
                    array('label'=>'Comment le bug est-il apparu?',
                        'attr' => array(
                            'placeholder' => 'Une breve description de(s) action(s) qui ont mené à ce bug nous aidra!!!')))
                ->add('remarque', 'textarea',array('label'=>'Remarque'))
                ->add('url','hidden',
                    array('label'=>'Url de la page actuelle','read_only'=> true))
                ->add('user','hidden',
                    array('label'=>'Auteur du rapport','read_only'=> true))
                ->add('html','hidden',
                    array('label'=>'Contenu html de la page','read_only'=> true))

                ->getForm();

            if ($request->isMethod('POST')) {

                /*
                 * Lorsque le formulaire est posté
                 */
                $form->handleRequest($request);

                if ($form->isValid()) {

                    /*
                     * Get info from form...
                     */
                    $action = $form->get('action')->getData();
                    $remarque = $form->get('remarque')->getData();
                    $url =  $form->get('url')->getData();
                    $username = $form->get('user')->getData();


                    /*
                     * Création du fichier Html
                     */
                    $fs = new Filesystem();
                    $kernel = $this->get('kernel');
                    $tmp_path_debug = $kernel->getRootDir() . '/cache/' . $kernel->getEnvironment() . '/tmp/debug_report_html';


                    if(!$fs->exists($tmp_path_debug))
                    {
                        $fs->mkdir($tmp_path_debug);
                    }

                    $html_file_name = $tmp_path_debug.'/bug_report_'.$username.'_'.str_shuffle('1234567890abcdefghijk').'.html';
                    //creat file and set the content inside
                    $fs->dumpFile($html_file_name,$form->get('html')->getData());

                    /*
                     * Generation de l'email
                     */
                    $adresse = $this->container->getParameter('bug_report_email');
                    /** @var Email $message */
                    $message = $this->get('email');
                    $message
                        ->setSubject('BUG REPORT')
                        ->setFrom($adresse)
                        ->setTo($adresse)
                        ->setBody(
                            $this->renderView(
                                'AppBundle:BugReport:bug_report_mail.txt.twig',
                                array(
                                    'action' => $action,
                                    'remarque' => $remarque,
                                    'url' => $url,
                                    'user'=>$username,
                                )
                            )
                        );
                    $message->attachFile($html_file_name,'bug_report.html');

                    $this->get('mailer')->send($message);


                    return new Response('Rapport de bug envoyé!');
                }
            }

            /*
             * Remplisage du formulaire avec des infos utiles...
             */
            $url = $request->request->get('url');
            $html = $request->request->get('html');

            $form->get('url')->setData($url);

            $form->get('html')->setData($html);

            $form->get('user')->setData($this->getUser()->getUsername());

            return $this->render('AppBundle:BugReport:bug_report_modal.html.twig', array(
                'form' => $form->createView()
            ));
        }
        else{
            return new Response();
        }



    }


}
