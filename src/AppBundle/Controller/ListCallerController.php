<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Membre;
use Interne\FinancesBundle\Entity\Debiteur;
use AppBundle\Entity\Famille;
use AppBundle\Utils\ListUtils\ListKey;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Utils\ListUtils\ListModels\ListModelsAttributions;
use AppBundle\Utils\ListUtils\ListModels\ListModelsDistinctions;
use AppBundle\Utils\ListUtils\ListModels\ListModelsMembre;
use Interne\FinancesBundle\Utils\ListModels\ListModelsCreances;
use Interne\FinancesBundle\Utils\ListModels\ListModelsFactures;

/* Annotations */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\Container;

/**
 * Ce controller est un service et un controller!!!
 *
 * (note: by calling the service "list_caller" in the route annotation,
 * the constructor is called)
 *
 * Class ListCallerController
 * @package AppBundle\Controller
 * @Route("/list", service="list_caller")
 *
 */
class ListCallerController extends Controller
{

    const CALL_BY_ROUTE = "route";
    const CALL_BY_TWIG = "twig";
    
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null){

        $this->setContainer($container);
    }

    /**
     * Do not put this in constructor: this avoid circular referance of service
     *
     * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private function getRouter()
    {
        return $this->get('router');
    }

    /**
     * Do not put this in constructor: this avoid circular referance of service
     *
     * @return \Twig_Environment
     */
    private function getTwig()
    {
        return $this->get('twig');
    }

    /**
     * Permet de retourner une liste en adaptant le rendu
     * selon si l'appel est fait dans twig ou depuis une route
     * @param $list
     * @param $call
     * @return Response
     */
    private function returnList($list,$call){
        if($call == ListCallerController::CALL_BY_ROUTE){
            return new Response($list);
        }
        else{
            return $list;
        }
    }

    
    /**
     * Permet d'appeler une liste (de session)
     *
     * @Route("/session/{key}/{call}", defaults={"call"="route"})
     *
     */
    public function Session($key,$call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $this->get('list_storage')->getObjects($key);
        $url = $this->generateUrl('app_listcaller_session',array('key'=>$key));
        switch($key)
        {
            case ListKey::CREANCES_SEARCH_RESULTS:
                $list = ListModelsCreances::getSearchResults($this->getTwig(),$this->getRouter(),$items,$url)->render();
                return $this->returnList($list,$call);
            case ListKey::FACTURES_SEARCH_RESULTS:
                $list = ListModelsFactures::getSearchResults($this->getTwig(),$this->getRouter(),$items,$url)->render();
                return $this->returnList($list,$call);
            case ListKey::MEMBRES_SEARCH_RESULTS:
                $list = ListModelsMembre::getDefault($this->getTwig(),$this->getRouter(),$items,$url)->render();
                return $this->returnList($list,$call);

        }

    }
    

    /**
     * @route("/membre/fraterie/{membre}/{call}", defaults={"call"="route"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $call
     * @return mixed
     */
    public function MembreFraterie(Membre $membre,$call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $membre->getFamille()->getMembres();
        $url = $this->getRouter()->generate('app_listcaller_membrefraterie',array('membre'=>$membre->getId()));
        $list = ListModelsMembre::getFraterie($this->getTwig(),$this->getRouter(),$items,$url)->render();
        return $this->returnList($list,$call);
    }

    /**
     * @route("/membre/attributions/{membre}/{call}", defaults={"call"="route"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $call
     * @return mixed
     */
    public function MembreAttributions(Membre $membre,$call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $membre->getAttributions();
        $url = $this->getRouter()->generate('app_listcaller_membreattributions',array('membre'=>$membre->getId()));
        $list = ListModelsAttributions::getDefault($this->getTwig(),$this->getRouter(),$items,$url)->render();
        return $this->returnList($list,$call);
    }

    /**
     * @route("/membre/distinctions/{membre}/{call}", defaults={"call"="route"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $call
     * @return mixed
     */
    public function MembreDistinctions(Membre $membre,$call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $membre->getDistinctions();
        $url = $this->getRouter()->generate('app_listcaller_membredistinctions',array('membre'=>$membre->getId()));
        $list = ListModelsDistinctions::getDefault($this->getTwig(),$this->getRouter(),$items,$url)->render();
        return $this->returnList($list,$call);
    }

    /**
     * @route("/debiteur/creances/{debiteur}/{call}", defaults={"call"="route"})
     * @ParamConverter("debiteur", class="InterneFinancesBundle:Debiteur")
     * @param Debiteur $debiteur
     * @param $call
     * @return mixed
     */
    public function DebiteurCreances(Debiteur $debiteur,$call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $debiteur->getCreances();
        $url = $this->getRouter()->generate('app_listcaller_debiteurcreances',array('debiteur'=>$debiteur->getId()));
        $list = ListModelsCreances::getDefault($this->getTwig(),$this->getRouter(),$items,$url)->render();
        return $this->returnList($list,$call);
    }

    /**
     * @route("/debiteur/factures/{debiteur}/{call}", defaults={"call"="route"})
     * @ParamConverter("debiteur", class="InterneFinancesBundle:Debiteur")
     * @param Debiteur $debiteur
     * @param $call
     * @return mixed
     */
    public function DebiteurFactures(Debiteur $debiteur,$call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $debiteur->getFactures();
        $url = $this->getRouter()->generate('app_listcaller_debiteurfactures',array('debiteur'=>$debiteur->getId()));
        $list = ListModelsFactures::getDefault($this->getTwig(),$this->getRouter(),$items,$url)->render();
        return $this->returnList($list,$call);
    }

    /**
     * @route("/famille/membres/{famille}/{call}", defaults={"call"="route"})
     * @ParamConverter("famille", class="AppBundle:Famille")
     * @param Famille $famille
     * @param $call
     * @return mixed
     */
    public function FamilleMembres(Famille $famille,$call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $famille->getMembres();
        $url = $this->getRouter()->generate('app_listcaller_famillemembres',array('famille'=>$famille->getId()));
        $list = ListModelsMembre::getDefault($this->getTwig(),$this->getRouter(),$items,$url)->render();
        return $this->returnList($list,$call);
    }

}
