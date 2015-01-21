<?php

namespace AppBundle\Action;

use AppBundle\Entity\Action;
use AppBundle\Entity\Membre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * ActionManager
 * Une action dans le netBS est une demande de confirmation envoyée à un membre. Une fois l'action lancée,
 * un message apparaît sur l'écran du membre concerné lui demandant de valider l'action. En fonction de l'action réalisée
 * par le membre, l'event sera Validé ou Refusé puis supprimé, exécutant en passant le code correspondant
 *
 * L'actionManager permet d'ajouter, supprimer ou valider des actions.
 *
 * @package AppBundle\Action
 */
class EventManager {

    /** @var  EntityManager $em */
    private $em;


    public function __construct(EntityManager $em) {

        $this->em = $em;
    }


    /**
     * Permet d'enregistrer une action.
     * Pour ce faire, la méthode va génerer une nouvelle entité Action vide et l'hydrater avec les données de l'action,
     * puis la persister en BDD
     * @param $action
     */
    public function register($action) {

        if(!in_array('ActionInterface', class_implements($action)))
            throw new \LogicException("L'action n'implémente pas l'interface obligatoire \\AppBundle\\Action\\ActionInterface");

        $entity = new Action();

        $entity->setDateCreation(new \DateTime("now"));
        $entity->setDatePeremption($action->getDatePeremption());
        $entity->setConcernedMembre($action->getConcernedMembre());
        $entity->setThrowerMembre($action->getThrowerMembre());
        $entity->setKey(time());
        $entity->setClassName(get_class($action));
        $entity->setMessage($action->getMessage());

        $this->em->persist($entity);
        $this->em->flush();
    }


    /**
     * Retourne tous les actions liés au membre passé en paramètre
     * @param Membre $membre
     * @return ArrayCollection
     */
    public function getActions(Membre $membre) {

        $actions    = $this->em->getRepository('AppBundle:Action')->findAll();
        $concerned = array();

        /** @var \AppBundle\Entity\Action $e */
        foreach($actions as $e)
            if($e->getConcernedMembre() == $membre)
                $concerned[] = $this->hydrateAction($e);

        return new ArrayCollection($concerned);
    }


    /**
     * Va hydrater une classe Action personnalisée à partir des données stockées en BDD
     * @param action $action
     * @return Object
     */
    private function hydrateAction($action) {

        $actionName = $action->getClassName();
        $object    = new $actionName();

        $object->setConcernedMembre($action->getConcernedMembre());
        $object->setThrowerMembre($action->getThrowerMembre());
        $object->setDatePeremption($action->getDatePeremption());

        return $object;
    }
}