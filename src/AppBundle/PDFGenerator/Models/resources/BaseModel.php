<?php

namespace AppBundle\PDFGenerator\Models\resources;

use fpdi\FPDI;

/**
 * Base pour les modèles PDF
 * permet d'initialiser les bases du fichier
 * et les fonctions à implémenter obligatoirement
 * @package AppBundle\PDFGenerator\Models\resources
 */
abstract class BaseModel extends FPDI {

    protected $orientation  = 'portrait';
    protected $size         = 'A4';

    /**
     * Constructeur
     * Fixe les paramètres de base du document
     */
    public function __construct() {

        $this->SetCreator( 'netBS', true);
        $this->SetAuthor(  'Guillaume Hochet', true);
        $this->SetSubject( "Document géneré à l'aide du NetBS", true);
        $this->SetKeywords('netBS Brigade Sauvabelin intranet', true);
    }



    /**
     * Retourne le nom du modèle
     * @return string
     */
    abstract public function getName();

    /**
     * Set le titre du document
     */
    abstract public function setTitre();

}