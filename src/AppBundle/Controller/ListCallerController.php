<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Debiteur;
use AppBundle\Entity\Famille;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Receiver;
use AppBundle\Entity\Sender;
use AppBundle\Utils\ListUtils\AbstractList;
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
     * @Route("/session/{key}/{format}", defaults={"format"="include_html"})
     *
     */
    public function Session($key, $format = AbstractList::FORMAT_INCLUDE_HTML)
    {
        $items = $this->get('list_storage')->getObjects($key);
        $url = $this->generateUrl('app_listcaller_session', array('key' => $key));
        switch ($key) {
            case ListKey::CREANCES_SEARCH_RESULTS:
                return $this->get('app.list.creance')->getSearchResults($items, $url, $key)->render($format);

            case ListKey::FACTURES_SEARCH_RESULTS:
                return $this->get('app.list.facture')->getSearchResults($items, $url)->render($format);

            case ListKey::MEMBRES_SEARCH_RESULTS:
                return $this->get('app.list.membre')->getDefault($items, $url)->render($format);

            case ListKey::FAMILLE_SEARCH_RESULTS_ADD_MEMBRE:
                return $this->get('app.list.famille')->getSearchResults($items, $url)->render($format);

            case ListKey::PAYEMENTS_SEARCH_RESULTS:
                return $this->get('app.list.payement')->getSearchResults($items, $url)->render($format);

        }
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
     * @route("/membre/fraterie/{membre}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $format
     * @return mixed
     */
    public function MembreFraterie(Membre $membre, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $membre->getFamille()->getMembres();
        $url = $this->get('router')->generate('app_listcaller_membrefraterie', array('membre' => $membre->getId()));
        return $this->get('app.list.membre')->getFraterie($items, $url)->render($format);
    }

    /**
     * @route("/membre/attributions/{membre}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $format
     * @return mixed
     */
    public function MembreAttributions(Membre $membre, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $membre->getAttributions();
        $url = $this->get('router')->generate('app_listcaller_membreattributions', array('membre' => $membre->getId()));
        return $this->get('app.list.attribution')->getMembreAttribution($items, $url, $membre)->render($format);
    }

    /**
     * @route("/membre/distinctions/{membre}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("membre", class="AppBundle:Membre")
     * @param Membre $membre
     * @param $format
     * @return mixed
     */
    public function MembreDistinctions(Membre $membre, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $membre->getDistinctions();
        $url = $this->get('router')->generate('app_listcaller_membredistinctions', array('membre' => $membre->getId()));
        return $this->get('app.list.obtention_distinction')->getMembreDistinctions($items,$url,$membre)->render($format);

    }

    /**
     * @route("/groupe/effectifs/{groupe}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     * @param Groupe $groupe
     * @param $format
     * @return mixed
     *
     * todo CMR injecter le groupe dans le getEffectifs(....groupe)
     */
    public function GroupeEffectifs(Groupe $groupe, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $groupe->getMembers();
        $url = $this->get('router')->generate('app_listcaller_groupeeffectifs', array('groupe' => $groupe->getId()));
        return $this->get('app.list.membre')->getEffectifs( $items, $url)->render($format);
    }

    /**
     * @route("/debiteur/creances/{debiteur}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("debiteur", class="InterneFinancesBundle:Debiteur")
     * @param Debiteur $debiteur
     * @param $format
     * @return mixed
     */
    public function DebiteurCreances(Debiteur $debiteur, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $debiteur->getCreances();
        $url = $this->get('router')->generate('app_listcaller_debiteurcreances', array('debiteur' => $debiteur->getId()));
        return $this->get('app.list.creance')->getForDebiteur($items, $url,$debiteur)->render($format);
    }

    /**
     * @route("/debiteur/factures/{debiteur}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("debiteur", class="InterneFinancesBundle:Debiteur")
     * @param Debiteur $debiteur
     * @param $format
     * @return mixed
     */
    public function DebiteurFactures(Debiteur $debiteur, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $debiteur->getFactures();
        $url = $this->get('router')->generate('app_listcaller_debiteurfactures', array('debiteur' => $debiteur->getId()));
        return $this->get('app.list.facture')->getDefault($items, $url)->render($format);
    }

    /**
     * @route("/famille/membres/{famille}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("famille", class="AppBundle:Famille")
     * @param Famille $famille
     * @param $format
     * @return mixed
     */
    public function FamilleMembres(Famille $famille, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $famille->getMembres();
        $url = $this->get('router')->generate('app_listcaller_famillemembres', array('famille' => $famille->getId()));
        return $this->get('app.list.membre')->getDefault( $items, $url)->render($format);
    }

    /**
     * @route("/receiver/mails/{receiver}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("receiver", class="InterneMailBundle:Receiver")
     * @param Receiver $receiver
     * @param $format
     * @return mixed
     */
    public function ReceiverMails(Receiver $receiver, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $receiver->getMails();
        $url = $this->get('router')->generate('app_listcaller_receivermails', array('receiver' => $receiver->getId()));
        return $this->get('app.list.mail')->getDefault( $items, $url)->render($format);
    }

    /**
     * @route("/sender/mails/not_sent/{sender}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("sender", class="InterneMailBundle:Sender")
     * @param Sender $sender
     * @param $format
     * @return mixed
     */
    public function SenderMailsNotSent(Sender $sender, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $sender->getNotSentMails();
        $url = $this->get('router')->generate('app_listcaller_sendermailsnotsent', array('sender' => $sender->getId()));
        return $this->get('app.list.mail')->getMyMail( $items, $url)->render($format);
    }

    /**
     * @route("/sender/mails/sent/{sender}/{format}", defaults={"format"="include_html"})
     * @ParamConverter("sender", class="InterneMailBundle:Sender")
     * @param Sender $sender
     * @param $format
     * @return mixed
     *
     * todo NUR passer ici
     */
    public function SenderMailsSent(Sender $sender, $format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $sender->getSentMails();
        $url = $this->get('router')->generate('app_listcaller_sendermailssent', array('sender' => $sender->getId()));
        return $this->get('app.list.mail')->getMyMail( $items, $url)->render($format);
    }

    /**
     * @route("/model/all/{format}", defaults={"format"="include_html"})
     * @param $format
     * @return mixed
     */
    public function modelAll($format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $this->getEntityManager()->getRepository('AppBundle:Model')->findAll();
        $url = $this->get('router')->generate('app_listcaller_modelall');
        return $this->get('app.list.model')->getDefault($items, $url)->render($format);
    }

    /**
     * @route("/categorie/all/{format}", defaults={"format"="include_html"})
     * @param $format
     * @return mixed
     */
    public function categorieAll($format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $this->get('app.repository.categorie')->findAll();
        $url = $this->get('router')->generate('app_listcaller_categorieall');
        return $this->get('app.list.categorie')->getDefault($items, $url)->render($format);
    }

    /**
     * @route("/user/all/{format}", defaults={"format"="include_html"})
     * @param $format
     * @return mixed
     */
    public function userAll($format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $this->denyAccessUnlessGranted('ROLE_SECURITY');
        $items = $this->get('app.repository.user')->findAll();
        $url = $this->get('router')->generate('app_listcaller_userall');
        return $this->get('app.list.user')->getDefault($items, $url)->render($format);
    }

    /**
     * @route("/fonction/all/{format}", defaults={"format"="include_html"})
     * @param $format
     * @return mixed
     */
    public function fonctionAll($format = ListRenderer::FORMAT_INCLUDE_HTML)
    {
        $items = $this->get('app.repository.fonction')->findAll();
        $url = $this->get('router')->generate('app_listcaller_fonctionall');
        return $this->get('app.list.fonction')->getDefault( $items, $url)->render($format);
    }

    /**
     * @route("/groupe/all/{format}", defaults={"format"="include_html"})
     * @param $format
     * @return mixed
     */
    public function groupeAll($format = AbstractList::FORMAT_INCLUDE_HTML)
    {
        $items = $this->get('app.repository.groupe')->findAll();
        $url = $this->get('router')->generate('app_listcaller_groupeall');
        return $this->get('app.list.groupe')->getDefault($items, $url)->render($format);
    }

    /**
     * @route("/payementfile/all/{format}", defaults={"format"="include_html"})
     * @param $format
     * @return mixed
     */
    public function payementFileAll($format = AbstractList::FORMAT_INCLUDE_HTML)
    {
        $items = $this->get('app.repository.payement_file')->findAll();
        $url = $this->get('router')->generate('app_listcaller_payementfileall');
        return $this->get('app.list.payement_file')->getDefault($items, $url)->render($format);
    }

    /**
     * @route("/payement/notvalidated/{format}", defaults={"format"="include_html"})
     * @param $format
     * @return mixed
     */
    public function payementNotValidated($format = AbstractList::FORMAT_INCLUDE_HTML)
    {
        $items = $this->get('app.repository.payement')->findNotValidated();
        $url = $this->get('router')->generate('app_listcaller_payementnotvalidated');
        return $this->get('app.list.payement')->getNotValidated($items, $url)->render($format);
    }

    /**
     * @route("/parameter/all/{format}", defaults={"format"="include_html"})
     * @param $format
     * @return mixed
     */
    public function parameterAll($format = AbstractList::FORMAT_INCLUDE_HTML)
    {
        $items = $this->get('app.repository.parameter')->findAll();
        $url = $this->get('router')->generate('app_listcaller_parameterall');
        return $this->get('app.list.parameter')->getDefault($items, $url)->render($format);
    }
}
