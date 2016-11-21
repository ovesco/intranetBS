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






}