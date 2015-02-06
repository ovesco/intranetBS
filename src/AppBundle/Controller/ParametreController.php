<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


/**
 *
 *
 * Class ParametreController
 * @package AppBundle\Controller
 * @Route("/parametre")
 */
class ParametreController extends Controller
{


    /**
     * @Route("/liste", name="interne_parametres_liste")
     * @Template("AppBundle:Parametre:listeForm.html.twig")
     *
     * @return Response
     */
    public  function listeParametresAction()
    {
        $parametres = $this->get('parametres')->getParametres();

        //remet en form le fichier parametre.yml...
        //TODO: on peut enlever ca une fois le dév terminer.
        $this->get('parametres')->save();

        return array('parametres' => $parametres);
    }


    /*
     * Edition en ajax des parametres depuis la page d'affichage
     */
    /**
     * @Route("/update_ajax", name="interne_parametre_update_ajax", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function updateAjaxAction(Request $request)
    {

        if($request->isXmlHttpRequest()) {

            $groupe = $request->request->get('groupe');
            $value = $request->request->get('value');
            $parametre = $request->request->get('parametre');

            $this->get('parametres')->setValue($groupe,$parametre,$value);

            return new Response();

        }
        return new Response();

    }







}