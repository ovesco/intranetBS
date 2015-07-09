<?php


namespace AppBundle\Utils\ListRender;

class Column{

    /**
     * cette variable contient une fonction
     * @var
     */
    private $dataAccessor;

    private $modifiers;

    public function __construct($dataAccessor){
        $this->dataAccessor = $dataAccessor;
    }


    public function render($object)
    {
        /*
         * Petite subtilité pour pouvoir appler la fonction.
         */
        $function = $this->dataAccessor;
        $data = $function($object);



        return $function($object);
    }
}