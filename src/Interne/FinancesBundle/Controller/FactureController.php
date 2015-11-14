<?php

namespace Interne\FinancesBundle\Controller;

/* Symfony */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/* Entity */
use Interne\FinancesBundle\Entity\Facture;



/* Elastica repository */
use Interne\FinancesBundle\Search\FactureRepository;
use Interne\FinancesBundle\Search\FactureSearch;
use Interne\FinancesBundle\Search\FactureSearchType;

/* routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;

/* Service */
use AppBundle\Utils\Export\Pdf;

/**
 * Class FactureController
 * @package Interne\FinancesBundle\Controller
 *
 * @Route("/facture")
 */
class FactureController extends Controller
{

    /**
     * @Route("/search", options={"expose"=true})
     * @Menu("Recherche de factures",block="finances",order=2,icon="search")
     * @param Request $request
     * @return Response
     */
    public function searchAction(Request $request){


        $factureSearch = new FactureSearch();

        $searchForm = $this->createForm(new FactureSearchType(),$factureSearch);

        $results = array();

        $searchForm->handleRequest($request);

        if ($searchForm->isValid()) {

            $factureSearch = $searchForm->getData();

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var FactureRepository $repository */
            $repository = $elasticaManager->getRepository('InterneFinancesBundle:Facture');

            $results = $repository->search($factureSearch);


        }


        return $this->render('InterneFinancesBundle:Facture:page_recherche.html.twig',
            array('searchForm'=>$searchForm->createView(),'factures'=>$results));

    }


    /**
     * @Route("/show/{facture}", options={"expose"=true})
     * @param Facture $facture
     * @ParamConverter("facture", class="InterneFinancesBundle:Facture")
     * @param Request $request
     * @return Response
     * @Template("InterneFinancesBundle:Facture:show_modal.html.twig")
     */
    public function showAction(Request $request,Facture $facture){

        return  array('facture' => $facture);
    }


    /**
     * @Route("/delete/{facture}", options={"expose"=true})
     * @param Facture $facture
     * @ParamConverter("facture", class="InterneFinancesBundle:Facture")
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request,Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($facture);
        $em->flush();
        $response = new Response();
        return $response->setStatusCode(200);//OK
    }

    /**
     * Create a PDF and send it to the client browser
     *
     * @param Facture $facture
     * @Route("/print/{facture}", options={"expose"=true})
     * @return Response
     * @ParamConverter("facture", class="InterneFinancesBundle:Facture")
     */
    public function printAction(Facture $facture)
    {

        $printer = $this->get('facture_printer');
        /** @var Pdf $pdf */
        $pdf = $printer->factureToPdf($facture);

        /*
         * Ajout de l'adresse
         */
        $adresse = $facture->getDebiteur()->getOwner()->getAdresseExpedition();

        $pdf->addAdresseEnvoi($adresse);


        $filePath = $this->get('kernel')->getCacheDir().'/temp_pdf/';
        $fileName = 'facture.pdf';

        $fs = new Filesystem();

        if(!$fs->exists($filePath))
        {
            $fs->mkdir($filePath);
        }


        /*
         * Save the PDF in cache dir
         */
        $pdf->Output($filePath.$fileName,'F');

        $response = new BinaryFileResponse($filePath.$fileName);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Facture_'.$facture->getId().'.pdf' //change file name
        );

        return $response;
    }




}