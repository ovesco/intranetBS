<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 30.11.15
 * Time: 22:18
 */

namespace AppBundle\Utils\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * https://fr.wikipedia.org/wiki/Fabrique_(patron_de_conception)
 *
 * Class ResponseFactory
 */
class ResponseFactory {

    static function ok($text = null)
    {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_OK,$text);
        return $response;
    }

    static function forbidden($text = null)
    {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_FORBIDDEN,$text);
        return $response;
    }

    static function conflict($text = null)
    {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_CONFLICT,$text);
        return $response;
    }

    static function badRequest($text = null)
    {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_BAD_REQUEST,$text);
        return $response;
    }

    /**
     * A utiliser lorsqu'on veut envoyer le contenu de $fileContent dans un fichier
     *
     * @param $fileContent
     * @param $fileName
     * @param $contentType
     * @return Response
     */
    static function streamFile($fileContent,$fileName,$contentType)
    {
        $response = new Response($fileContent);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,$fileName
        );
        $response->headers->set('Content-Disposition',$disposition);
        $response->headers->set('Content-Type',$contentType);

        return $response;
    }

    /**
     * A utiliser lorsqu'on veut envoyer un fichier $file déjà présent dans le serveur
     *
     * @param $file
     * @param $outputFileName
     * @param null $contentType
     * @return BinaryFileResponse
     */
    static function sendFile($file,$outputFileName,$contentType = null)
    {
        $response = new BinaryFileResponse($file);
        BinaryFileResponse::trustXSendfileTypeHeader();
        if($contentType != null)
        {
            $response->headers->set('Content-Type', $contentType);
        }
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $outputFileName
        );
        return $response;
    }






}