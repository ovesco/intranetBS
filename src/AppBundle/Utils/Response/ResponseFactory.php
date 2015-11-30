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

    static function ok()
    {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    static function forbidden()
    {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        return $response;
    }

}