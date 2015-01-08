<?php

namespace AppBundle\PDFGenerator\Models\resources;

use AppBundle\PDFGenerator\TemplateLoader;

/**
 * Cette classe permet d'implémenter la faculté au modèle de charger un template sur lequel écrire
 * Un template est un fichier PDF de base qui représente le Header du document. Celui-ci y sera chargé
 * @package AppBundle\PDFGenerator\Models\resources
 */
abstract class TemplatedModel extends BaseModel {

    /**
     * Emplacement des templates
     * @var string
     */
    public $templateDir;

    /**
     * Type d'importation
     * http://manuals.setasign.com/fpdi-manual/the-fpdi-class/#index-3-2
     * @var string
     */
    public $cropBox = '/MediaBox';

    /**
     * @var TemplateLoader
     */
    protected $template;




    /**
     * On appelle le constructeur principal, et on charge le répértoire de base des templates
     */
    public function __construct() {

        parent::__construct();
        $this->templateDir = getcwd() . '\PDFTemplates\\';
    }




    /**
     * Charge un template sur lequel travailler
     * @param string $template le template à charger
     */
    public function loadTemplate($template) {

        $pageCount = $this->setSourceFile($this->templateDir . $template);
        $tplIdx    = $this->importPage(1, $this->cropBox);

        $this->AddPage($this->orientation);
        $this->useTemplate($tplIdx);
    }

    
}