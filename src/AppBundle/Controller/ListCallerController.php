<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Debiteur;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Receiver;
use AppBundle\Entity\Sender;
use AppBundle\Utils\ListUtils\ListKey;
use AppBundle\Utils\ListUtils\ListModels\ListModelsAttributions;
use AppBundle\Utils\ListUtils\ListModels\ListModelsCreances;
use AppBundle\Utils\ListUtils\ListModels\ListModelsDistinctions;
use AppBundle\Utils\ListUtils\ListModels\ListModelsFactures;
use AppBundle\Utils\ListUtils\ListModels\ListModelsFamille;
use AppBundle\Utils\ListUtils\ListModels\ListModelsMail;
use AppBundle\Utils\ListUtils\ListModels\ListModelsMembre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;


/* Annotations */

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
    public function __construct(ContainerInterface $container = null)
    {

        $this->setContainer($container);
    }

    /**
     * Permet d'appeler une liste (de session)
     *
     * @Route("/session/{key}/{call}", defaults={"call"="route"})
     *
     */
    public function Session($key, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $this->get('list_storage')->getObjects($key);
        $url = $this->generateUrl('app_listcaller_session', array('key' => $key));
        switch ($key) {
            case ListKey::CREANCES_SEARCH_RESULTS:
                $list = ListModelsCreances::getSearchResults($this->getTwig(), $this->getRouter(), $items, $url)->render();
                return $this->returnList($list, $call);
            case ListKey::FACTURES_SEARCH_RESULTS:
                $list = ListModelsFactures::getSearchResults($this->getTwig(), $this->getRouter(), $items, $url)->render();
                return $this->returnList($list, $call);
            case ListKey::MEMBRES_SEARCH_RESULTS:
                $list = ListModelsMembre::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
                return $this->returnList($list, $call);
            case ListKey::FAMILLE_SEARCH_RESULTS_ADD_MEMBRE:
                $list = ListModelsFamille::getSearchResults($this->getTwig(), $this->getRouter(), $items, $url)->render();
                return $this->returnList($list, $call);
        }
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
     * Do not put this in constructor: this avoid circular referance of service
     *
     * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private function getRouter()
    {
        return $this->get('router');
    }

    /**
     * Permet de retourner une liste en adaptant le rendu
     * selon si l'appel est fait dans twig ou depuis une route
     * @param $list
     * @param $call
     * @return Response
     */
    private function returnList($list, $call)
    {
        if ($call == ListCallerController::CALL_BY_ROUTE) {
            return new Response($list);
        } else {
            return $list;
        }
    }

    /**
     * @route("/membre/fraterie/{membre}/{call}", defaults={"call"="route"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $call
     * @return mixed
     */
    public function MembreFraterie(Membre $membre, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $membre->getFamille()->getMembres();
        $url = $this->getRouter()->generate('app_listcaller_membrefraterie', array('membre' => $membre->getId()));
        $list = ListModelsMembre::getFraterie($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/membre/attributions/{membre}/{call}", defaults={"call"="route"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $call
     * @return mixed
     */
    public function MembreAttributions(Membre $membre, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $membre->getAttributions();
        $url = $this->getRouter()->generate('app_listcaller_membreattributions', array('membre' => $membre->getId()));
        $list = ListModelsAttributions::getDefault($this->getTwig(), $this->getRouter(), $items, $membre, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/membre/distinctions/{membre}/{call}", defaults={"call"="route"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $call
     * @return mixed
     */
    public function MembreDistinctions(Membre $membre, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $membre->getDistinctions();
        $url = $this->getRouter()->generate('app_listcaller_membredistinctions', array('membre' => $membre->getId()));
        $list = ListModelsDistinctions::getDefault($this->getTwig(), $this->getRouter(), $items, $membre, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/groupe/effectifs/{groupe}/{call}", defaults={"call"="route"})
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     * @param Groupe $groupe
     * @param $call
     * @return mixed
     */
    public function GroupeEffectifs(Groupe $groupe, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $groupe->getMembers();
        $url = $this->getRouter()->generate('app_listcaller_groupeeffectifs', array('groupe' => $groupe->getId()));
        $list = ListModelsMembre::getEffectifs($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/debiteur/creances/{debiteur}/{call}", defaults={"call"="route"})
     * @ParamConverter("debiteur", class="InterneFinancesBundle:Debiteur")
     * @param Debiteur $debiteur
     * @param $call
     * @return mixed
     */
    public function DebiteurCreances(Debiteur $debiteur, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $debiteur->getCreances();
        $url = $this->getRouter()->generate('app_listcaller_debiteurcreances', array('debiteur' => $debiteur->getId()));
        $list = ListModelsCreances::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/debiteur/factures/{debiteur}/{call}", defaults={"call"="route"})
     * @ParamConverter("debiteur", class="InterneFinancesBundle:Debiteur")
     * @param Debiteur $debiteur
     * @param $call
     * @return mixed
     */
    public function DebiteurFactures(Debiteur $debiteur, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $debiteur->getFactures();
        $url = $this->getRouter()->generate('app_listcaller_debiteurfactures', array('debiteur' => $debiteur->getId()));
        $list = ListModelsFactures::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/famille/membres/{famille}/{call}", defaults={"call"="route"})
     * @ParamConverter("famille", class="AppBundle:Famille")
     * @param Famille $famille
     * @param $call
     * @return mixed
     */
    public function FamilleMembres(Famille $famille, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $famille->getMembres();
        $url = $this->getRouter()->generate('app_listcaller_famillemembres', array('famille' => $famille->getId()));
        $list = ListModelsMembre::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/receiver/mails/{receiver}/{call}", defaults={"call"="route"})
     * @ParamConverter("receiver", class="InterneMailBundle:Receiver")
     * @param Receiver $receiver
     * @param $call
     * @return mixed
     */
    public function ReceiverMails(Receiver $receiver, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $receiver->getMails();
        $url = $this->getRouter()->generate('app_listcaller_receivermails', array('receiver' => $receiver->getId()));
        $list = ListModelsMail::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/sender/mails/not_sent/{sender}/{call}", defaults={"call"="route"})
     * @ParamConverter("sender", class="InterneMailBundle:Sender")
     * @param Sender $sender
     * @param $call
     * @return mixed
     */
    public function SenderMailsNotSent(Sender $sender, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $sender->getNotSentMails();
        $url = $this->getRouter()->generate('app_listcaller_sendermailsnotsent', array('sender' => $sender->getId()));
        $list = ListModelsMail::getMyMail($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/sender/mails/sent/{sender}/{call}", defaults={"call"="route"})
     * @ParamConverter("sender", class="InterneMailBundle:Sender")
     * @param Sender $sender
     * @param $call
     * @return mixed
     */
    public function SenderMailsSent(Sender $sender, $call = ListCallerController::CALL_BY_TWIG)
    {
        $items = $sender->getSentMails();
        $url = $this->getRouter()->generate('app_listcaller_sendermailssent', array('sender' => $sender->getId()));
        $list = ListModelsMail::getMyMail($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }


}
