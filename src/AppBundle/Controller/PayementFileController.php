<?php

namespace AppBundle\Controller;

/* Symfony */
use AppBundle\Entity\PayementFile;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/* Routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/* Form */
use AppBundle\Form\Payement\PayementUploadFileType;

/* Services */
use AppBundle\Utils\Finances\PayementFileParser;
use AppBundle\Repository\PayementFileRepository;



/**
 * Class PayementController
 * @package AppBundle\Controller
 * @Route("/intranet/payement/file")
 */
class PayementFileController extends Controller
{
    /**
     * List of the PayementFile
     *
     * @Route("/list", options={"expose"=true})
     * @param Request $request
     * @Template("AppBundle:PayementFile:page_list.html.twig")
     * @return Response
     */
    public function listAction(Request $request){
        return array();
    }

    /**
     * Form for upload payments file
     *
     * @Route("/upload", options={"expose"=true})
     * @param Request $request
     * @Template("AppBundle:PayementFile:form_upload_file.html.twig")
     * @return Response
     */
    public function uploadAction(Request $request){

        $payementFile = new PayementFile();

        $form = $this->createForm(new PayementUploadFileType(),$payementFile,array('action'=>$this->generateUrl('app_payementfile_upload')));

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()){

            // $file stores the uploaded PDF file
            /** @var UploadedFile $file */
            $file = $payementFile->getFile();

            $fileContent = implode(file($file));
            $hash = sha1($fileContent);
            $payementFile->setHash($hash);

            $payementFile->setFilename($file->getClientOriginalName());

            // Generate a unique name for the file before saving it
            $uniqid = md5(uniqid()).'.'.$file->getClientOriginalExtension();

            $payementFile->setFile($uniqid);

            $payementFile->setDate(new \DateTime('now'));

            /** @var PayementFileRepository $repo */
            $repo = $this->get('app.repository.payement_file');

            if(!$repo->hashAlreadyExist($hash))
            {
                //this is a new file!!! we can process the content of the file

                /** @var PayementFileParser $payementParser */
                $payementParser = new PayementFileParser($file);
                $payementParser->parse();
                $payementFile->setInfos($payementParser->getInfos());

                //the unicity of the hash is also check during the insertion in DB by an ORM constraint
                $repo->save($payementFile);

                // Move the file to the directory where they are stored
                $file->move($this->getParameter('upload_payement_file_dir'),$uniqid);


                /** @var ArrayCollection $payements */
                $payements = $payementParser->getPayements();

                foreach($payements as $payement)
                {
                    //check the payement and associeated facture
                    $payement = $this->get('app.payement.check')->check($payement);
                    $this->get('app.repository.payement')->save($payement);
                }

                $message = 'Fichier correctement téléchargé, avec '.$payements->count().' payements.';
            }
            else
            {
                $message = 'Ce fichier a déjà été télécharger...';
            }

            $this->get('session')->getFlashBag()->add('notice', $message);
            return $this->redirect($this->generateUrl('app_payement_add'));

        }

        return array('form'=>$form->createView());
    }






}