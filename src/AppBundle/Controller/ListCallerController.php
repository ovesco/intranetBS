<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* Annotations */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\Container;

/**
 * Ce controller est un service et un controller!!!
 *
 *
 * Class ListCallerController
 * @package AppBundle\Controller
 * @Route("/list_call")
 */
class ListCallerController extends Controller
{

    /**
     * Ce constructeur est applé avec une valeur non null dans le cas
     * où il est instancier comme service.
     * Dans le cas du controller, le container est autrement (dans le framework).
     *
     * @param Container $container
     */
    public function __construct(Container $container = null){

        if(!is_null($container))
        {
            $this->setContainer($container);
        }

    }

    /**
     * Permet d'appeler une liste (de session) depuis le service disponible dans twig.
     *
     * @param $key
     * @return string
     * @throws \Exception
     */
    public function listInSession($key)
    {
        $objects = $this->get('list_storage')->getObjects($key);
        $model = $this->get('list_storage')->getModel($key);
        $url = $this->generateUrl('list_render_session',array('key'=>$key));
        return $this->get('list_container')->getModel($model,$objects,$url)->render();
    }

    /**
     * Permet d'appeler une liste (d'entité) depuis le service disponible dans twig.
     *
     * @param $model
     * @param $ids
     * @return string
     */
    public function listById($model,$ids){

        $idsParsed = array_map('intval', explode('-', $ids));
        $class = $this->get('list_container')->getRepresentedClass($model);
        $repo = $this->getDoctrine()->getRepository($class);
        $objects = $repo->findBy(array('id'=>$idsParsed));
        $url = $this->generateUrl('list_render_entity',array('model'=>$model,'ids'=>$ids));
        return $this->get('list_container')->getModel($model,$objects,$url)->render();
    }

    /**
     *
     * @Route("/session_list/{key}", name="list_render_session")
     *
     */
    public function sessionAction($key)
    {
        return new Response($this->listInSession($key));
    }


    /**
     * Pattern of ids exemple: 23-34-5-6-7
     * @Route("/entity_list/{model}/{ids}", name="list_render_entity", requirements={ "ids": "([0-9]+-?)+"})
     *
     */
    public function entityAction($model, $ids)
    {
        return new Response($this->listById($model,$ids));
    }


}
