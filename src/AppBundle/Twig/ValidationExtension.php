<?php

namespace AppBundle\Twig;

use Symfony\Component\Routing\Router;

class ValidationExtension extends \Twig_Extension
{

    private $router;

    public function __construct(Router $router) {

        $this->router       = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'modificationRoute' => new \Twig_Function_Method($this, 'modificationRoute')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('choiceToXeditable', array($this, 'choiceToXeditable')),
        );
    }

    /**
     * Génère la route appelée par xEditable pour modifier une valeur
     */
    public function modificationRoute() {

        return $this->router->generate('interne_ajax_app_modify_property');
    }

    /**
     * Génère le tableau json qui ira dans xeditable pour un choices donné
     * @param array $choices
     * @return string
     */
    public function choiceToXeditable(array $choices) {

        $return = "[";

        for($i = 0; $i < count($choices); $i++) {

            $value = $choices[$i]->value;
            $text  = $choices[$i]->label;

            $return .= '{"value":"' . $value . '", "text":"' . $text . '"}';

            if($i != (count($choices) -1))
                $return .= ",";
        }

        return $return . "]";
    }

    public function getName()
    {
        return 'validation_extension';
    }
}