<?php

namespace AppBundle\Controller;

/* Symfony */
use AppBundle\Utils\ListUtils\ListKey;
use AppBundle\Utils\Response\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/* Entity */
use AppBundle\Entity\Facture;

/* Elastica repository */
use AppBundle\Search\Facture\FactureRepository;
use AppBundle\Search\Facture\FactureSearch;
use AppBundle\Search\Facture\FactureSearchType;

/* routing */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Menu\Menu;

/* Service */
use AppBundle\Utils\Export\Pdf;
use AppBundle\Utils\ListUtils\ListStorage;
use AppBundle\Utils\Finances\FacturePrinter;
use AppBundle\Search\Mode;

/**
 * Class FactureController
 * @package AppBundle\Controller
 *
 * @Route("/intranet/finance/facture")
 */
class FactureController extends Controller
{

    /**
     * @Route("/search", options={"expose"=true})
     * @Menu("Recherche de factures",block="finances",order=2,icon="search")
     * @param Request $request
     * @Template("AppBundle:Facture:page_recherche.html.twig")
     * @return Response
     *
     *
     */
    public function searchAction(Request $request){


        $factureSearch = new FactureSearch();

        $searchForm = $this->createForm(new FactureSearchType(),$factureSearch);


        /** @var ListStorage $sessionContainer */
        $sessionContainer = $this->get('list_storage');
        $sessionContainer->setRepository(ListKey::FACTURES_SEARCH_RESULTS,'AppBundle:Facture');


        $searchForm->handleRequest($request);
        if ($searchForm->isValid()) {

            $factureSearch = $searchForm->getData();

            $elasticaManager = $this->container->get('fos_elastica.manager');

            /** @var FactureRepository $repository */
            $repository = $elasticaManager->getRepository('AppBundle:Facture');

            $results = $repository->search($factureSearch);

            //get the search mode
            $mode = $searchForm->get(Mode::FORM_FIELD)->getData();
            switch($mode)
            {
                case Mode::INCLUDE_PREVIOUS: //include new results with the previous
                    $sessionContainer->addObjects(ListKey::FACTURES_SEARCH_RESULTS,$results);
                    break;
                case Mode::EXCLUDE_PREVIOUS: //exclude new results to the previous
                    $sessionContainer->removeObjects(ListKey::FACTURES_SEARCH_RESULTS,$results);
                    break;
                case Mode::STANDARD:
                default:
                    $sessionContainer->setObjects(ListKey::FACTURES_SEARCH_RESULTS,$results);

            }

        }


        return array('searchForm'=>$searchForm->createView(),'list_key'=>ListKey::FACTURES_SEARCH_RESULTS);

    }


    /**
     * @Route("/show/{facture}", options={"expose"=true})
     * @param Facture $facture
     * @ParamConverter("facture", class="AppBundle:Facture")
     * @param Request $request
     * @return Response
     * @Template("AppBundle:Facture:show_modal.html.twig")
     */
    public function showAction(Request $request,Facture $facture){

        return  array('facture' => $facture);
    }


    /**
     * @Route("/remove/{facture}", options={"expose"=true})
     * @param Facture $facture
     * @ParamConverter("facture", class="AppBundle:Facture")
     * @param Request $request
     * @return Response
     */
    public function removeAction(Request $request,Facture $facture)
    {
        if($facture->isRemovable())
        {
            $this->get('app.repository.facture')->remove($facture);
            return ResponseFactory::ok();
        }
        return ResponseFactory::conflict('La facture a déjà été payée.');
    }

    /**
     * Create a PDF and send it to the client browser
     *
     * @param Facture $facture
     * @Route("/print/{facture}", options={"expose"=true})
     * @return Response
     * @ParamConverter("facture", class="AppBundle:Facture")
     */
    public function printAction(Facture $facture)
    {

        /** @var FacturePrinter $printer */
        $printer = $this->get('app.facture_printer');
        /** @var Pdf $pdf */
        $pdf = $printer->factureToPdf($facture);

        /*
         * Ajout de l'adresse
         *
         * todo NUR à faire
         */
        //$adresse = $facture->getDebiteur()->getOwner()->getAdresseExpedition();

        //$pdf->addAdresseEnvoi($adresse);


        $filePath = $this->getParameter('cache_facture_dir');
        $fileName = 'facture_'.$facture->getId().'.pdf';

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
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
            //'Facture_'.$facture->getId().'.pdf' //change file name
        );

        return $response;
    }




}