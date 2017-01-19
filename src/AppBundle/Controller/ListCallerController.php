<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Debiteur;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Receiver;
use AppBundle\Entity\Sender;
use AppBundle\Utils\ListUtils\ListModels\ListModelsMail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

use AppBundle\Utils\ListUtils\ListKey;
use Doctrine\ORM\EntityManager;
use AppBundle\Repository\PayementRepository;
use AppBundle\Repository\ParameterRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Utils\ListUtils\ListModels\ListModelsAttributions;
use AppBundle\Utils\ListUtils\ListModels\ListModelsDistinctions;
use AppBundle\Utils\ListUtils\ListModels\ListModelsMembre;
use AppBundle\Utils\ListUtils\ListModels\ListModelsCreances;
use AppBundle\Utils\ListUtils\ListModels\ListModelsFactures;
use AppBundle\Utils\ListUtils\ListModels\ListModelsFamille;
use AppBundle\Utils\ListUtils\ListModels\ListModelsModel;
use AppBundle\Utils\ListUtils\ListModels\ListModelsCategorie;
use AppBundle\Utils\ListUtils\ListModels\ListModelsUser;
use AppBundle\Utils\ListUtils\ListModels\ListModelsFonction;
use AppBundle\Utils\ListUtils\ListModels\ListModelsGroupe;
use AppBundle\Utils\ListUtils\ListModels\ListModelsPayement;
use AppBundle\Utils\ListUtils\ListModels\ListModelsPayementFile;
use AppBundle\Utils\ListUtils\ListModels\ListModelsParameter;

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
 * @Route("/intranet/list", service="list_caller")
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
    public function Session($key, $call = self::CALL_BY_TWIG)
    {
        $items = $this->get('list_storage')->getObjects($key);
        $url = $this->generateUrl('app_listcaller_session', array('key' => $key));
        switch ($key) {
            case ListKey::CREANCES_SEARCH_RESULTS:
                $list = ListModelsCreances::getSearchResults($this->getTwig(), $this->getRouter(), $items, $url, $key)->render();
                return $this->returnList($list, $call);
            case ListKey::FACTURES_SEARCH_RESULTS:
                $list = ListModelsFactures::getSearchResults($this->getTwig(), $this->getRouter(), $items, $url)->render();
                return $this->returnList($list, $call);
            case ListKey::MEMBRES_SEARCH_RESULTS:
                $list = ListModelsMembre::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
                return $this->returnList($list, $call);

            case ListKey::PAYEMENTS_SEARCH_RESULTS:
                $list = ListModelsPayement::getSearchResults($this->getTwig(), $this->getRouter(), $items, $url)->render();
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
     * Do not put this in constructor: this avoid circular referance of service
     *
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
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
    public function MembreFraterie(Membre $membre, $call = self::CALL_BY_TWIG)
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
    public function MembreAttributions(Membre $membre, $call = self::CALL_BY_TWIG)
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
    public function MembreDistinctions(Membre $membre, $call = self::CALL_BY_TWIG)
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
     *
     * todo injecter le groupe dans le getEffectifs(....groupe)
     */
    public function GroupeEffectifs(Groupe $groupe, $call = self::CALL_BY_TWIG)
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
    public function DebiteurCreances(Debiteur $debiteur, $call = self::CALL_BY_TWIG)
    {
        $items = $debiteur->getCreances();
        $url = $this->getRouter()->generate('app_listcaller_debiteurcreances', array('debiteur' => $debiteur->getId()));
        $list = ListModelsCreances::getForDebiteur($this->getTwig(), $this->getRouter(), $items, $url,$debiteur)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/debiteur/factures/{debiteur}/{call}", defaults={"call"="route"})
     * @ParamConverter("debiteur", class="InterneFinancesBundle:Debiteur")
     * @param Debiteur $debiteur
     * @param $call
     * @return mixed
     */
    public function DebiteurFactures(Debiteur $debiteur, $call = self::CALL_BY_TWIG)
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
    public function FamilleMembres(Famille $famille, $call = self::CALL_BY_TWIG)
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
    public function ReceiverMails(Receiver $receiver, $call = self::CALL_BY_TWIG)
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
    public function SenderMailsNotSent(Sender $sender, $call = self::CALL_BY_TWIG)
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
    public function SenderMailsSent(Sender $sender, $call = self::CALL_BY_TWIG)
    {
        $items = $sender->getSentMails();
        $url = $this->getRouter()->generate('app_listcaller_sendermailssent', array('sender' => $sender->getId()));
        $list = ListModelsMail::getMyMail($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/model/all", defaults={"call"="route"})
     * @param $call
     * @return mixed
     */
    public function modelAll( $call = self::CALL_BY_TWIG)
    {
        $items = $this->getEntityManager()->getRepository('AppBundle:Model')->findAll();
        $url = $this->getRouter()->generate('app_listcaller_modelall');
        $list = ListModelsModel::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/categorie/all", defaults={"call"="route"})
     * @param $call
     * @return mixed
     */
    public function categorieAll( $call = self::CALL_BY_TWIG)
    {
        $items = $this->get('app.repository.categorie')->findAll();
        $url = $this->getRouter()->generate('app_listcaller_categorieall');
        $list = ListModelsCategorie::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/user/all", defaults={"call"="route"})
     * @param $call
     * @return mixed
     */
    public function userAll( $call = self::CALL_BY_TWIG)
    {
        $this->denyAccessUnlessGranted('ROLE_SECURITY');
        $items = $this->get('app.repository.user')->findAll();
        $url = $this->getRouter()->generate('app_listcaller_userall');
        $list = ListModelsUser::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/fonction/all", defaults={"call"="route"})
     * @param $call
     * @return mixed
     */
    public function fonctionAll( $call = self::CALL_BY_TWIG)
    {
        $items = $this->get('app.repository.fonction')->findAll();
        $url = $this->getRouter()->generate('app_listcaller_fonctionall');
        $list = ListModelsFonction::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/groupe/all", defaults={"call"="route"})
     * @param $call
     * @return mixed
     */
    public function groupeAll( $call = self::CALL_BY_TWIG)
    {
        $items = $this->get('app.repository.groupe')->findAll();
        $url = $this->getRouter()->generate('app_listcaller_groupeall');
        $list = ListModelsGroupe::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/payementfile/all", defaults={"call"="route"})
     * @param $call
     * @return mixed
     */
    public function payementFileAll( $call = self::CALL_BY_TWIG)
    {
        $items = $this->get('app.repository.payement_file')->findAll();
        $url = $this->getRouter()->generate('app_listcaller_payementfileall');
        $list = ListModelsPayementFile::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/payement/notvalidated", defaults={"call"="route"})
     * @param $call
     * @return mixed
     */
    public function payementNotValidated( $call = self::CALL_BY_TWIG)
    {
        /** @var PayementRepository $repo */
        $repo = $this->get('app.repository.payement');
        $items = $repo->findNotValidated();
        $url = $this->getRouter()->generate('app_listcaller_payementnotvalidated');
        $list = ListModelsPayement::getNotValidated($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }

    /**
     * @route("/parameter/all", defaults={"call"="route"})
     * @param $call
     * @return mixed
     */
    public function parameterAll( $call = self::CALL_BY_TWIG)
    {
        /** @var ParameterRepository $repo */
        $repo = $this->get('app.repository.parameter');
        $items = $repo->findAll();
        $url = $this->getRouter()->generate('app_listcaller_payementnotvalidated');
        $list = ListModelsParameter::getDefault($this->getTwig(), $this->getRouter(), $items, $url)->render();
        return $this->returnList($list, $call);
    }
}
