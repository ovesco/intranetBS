<?php

namespace AppBundle\Action;
use AppBundle\Entity\Membre;

/**
 * Cette interface doit être implémentée par toutes les Actions
 * @package AppBundle\Action
 */
interface ActionInterface {

    /**
     * retourne le membre ciblé par l'event
     * @return \AppBundle\Entity\Membre
     */
    public function getConcernedMembre();

    /**
     * set le membre ciblé par l'event
     * @return void
     */
    public function setConcernedMembre(Membre $membre);

    /**
     * Retourne le membre qui a lancé l'event
     * @return \AppBundle\Entity\Membre
     */
    public function getThrowerMembre();

    /**
     * set le membre qui a lancé l'event
     * @return void
     */
    public function setThrowerMembre(Membre $membre);

    /**
     * Les Specials sont les informations supplémentaires dont l'event a besoin pour exister. Elles sont stockées dans
     * un array qui est ensuite encodé par l'eventManager. Ces informations ne peuvent pas être des entités.
     * @return array
     */
    public function getSpecials();

    /**
     * Set Specials
     * @param array $specials
     * @return void
     */
    public function setSpecials(array $specials);

    /**
     * retourne une date indiquant quand l'event devient périmé, c'est-à-dire dépassé.
     * A partir du moment où la date est passé, l'event sera supprimé
     * @return \Datetime
     */
    public function getDatePeremption();

    /**
     * set la date de peremption de l'event
     * @return void
     */
    public function setDatePeremption(\DateTime $datePeremption);



    /**
     * Cette méthode doit retourner le message qui représente l'event. Ce message sera affiché au membre concerné par l'event
     * Par exemple "yo veux-tu organiser le super camp d'étey ?"
     * @return string
     */
    public function getMessage();

    /**
     * Cette méthode est appelée par l'EventManager si le membre a validé l'event
     * @return void
     */
    public function isValidated();

    /**
     * Cette méthode est appelée par l'EventManager si le membre a refusé l'event
     * @return void
     */
    public function isRefused();
}