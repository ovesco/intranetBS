<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 19.11.16
 * Time: 18:02
 */

namespace AppBundle\Utils\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


class AjaxResponseFactory {

    const POST_ACTION_RELOAD = 'reload';

    static function ok($post_action)
    {
        $response = new JsonResponse();
        $response->setData(array(
            'post_action' => $post_action
        ));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}