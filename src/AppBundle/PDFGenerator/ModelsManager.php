<?php

namespace AppBundle\PDFGenerator;

/**
 * ModelsManager
 * Récupère les différents modèles PDF disponible et permet de travailler avec
 * C'est dans cette classe qu'il faut inscrire les différents modèles disponibles
 */

class ModelsManager {

    private $models;

    public function __construct() {

        /*
         * Enregistrer les modeles PDFs ici
         * Pour créer des modèles, inspirez-vous des modeles existants
         */
        $this->models = array(

            new \AppBundle\PDFGenerator\Models\ListeDeTroupeModel,
        );
    }

    /**
     * Charge un modèle de PDF
     * @param string $name le modèle
     * @return Object le modèle demandé
     */
    public function loadModel($name) {

        foreach($this->models as $model)
            if($model->getName() == $name)
                return $model;
    }
}