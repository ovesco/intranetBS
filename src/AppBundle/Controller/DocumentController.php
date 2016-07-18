<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * Class DocumentController
 * @package AppBundle\Controller
 * @Route("/intranet/document")
 */
class DocumentController extends Controller
{
    /**
     * @Route("/download/{document}", options={"expose"=true})
     * @param Request $request
     * @ParamConverter("document", class="AppBundle:Document")
     * @param Document $document
     * @return Response
     */
    public function downloadAction(Request $request, Document $document)
    {
        $response =  new BinaryFileResponse($document->getFile());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$document->getName());
        return $response;
    }
}
