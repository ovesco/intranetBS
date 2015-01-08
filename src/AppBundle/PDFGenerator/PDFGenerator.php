<?php

namespace AppBundle\PDFGenerator;

/**
 * La classe PDFGenerator permet de génerer des PDFs en se basant sur des modèles existants.
 * @package AppBundle\PDFGenerator
 */
class PDFGenerator {

    /**
     * @var ModelsManager
     */
    public $manager;

    /**
     * Constructeur
     * initialise le générateur avec le ModelsManager
     */
    public function __construct() {

        $this->manager = new ModelsManager();
    }

    /**
     * @return ModelsManager
     */
    public function getModelsManager() {

        return $this->manager;
    }

    /**
     * Nettoie le Generateur en réinitialisant tout
     */
    public function purge() {

        $this->manager = new ModelsManager();
        $this->model   = null;
    }

}