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
            'modificationRoute' => new \Twig_Function_Method($this, 'modificationRoute'),
            'class' => new \Twig_SimpleFunction('class', array($this, 'getClass'))
        );
    }

    public function getClass($object)
    {
        return (new \ReflectionClass($object))->getShortName();
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
        $i      = 0;
        $total  = count($choices);

        foreach($choices as $id => $choice) {

            $value   = $choice->value;
            $text    = $choice->label;
            $return .= '{"value":"' . $value . '", "text":"' . $text . '"}';

            if($i != ($total - 1))
                $return .= ",";

            $i++;
        }

        return $return . "]";
    }

    public function getName()
    {
        return 'validation_extension';
    }
}