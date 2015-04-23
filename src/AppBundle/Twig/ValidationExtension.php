<?php

namespace AppBundle\Twig;

use AppBundle\Utils\Accessor\Parser;
use AppBundle\Utils\Data\Validation;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;

class ValidationExtension extends \Twig_Extension
{

    private $validation;
    private $parser;
    private $request;
    private $router;

    public function __construct(Validation $validation, Parser $parser, Router $router) {

        $this->validation   = $validation;
        $this->parser       = $parser;
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
     * Génère la route appelée par xEditable pour modifier une valeur
     */
    public function modificationRoute() {

        return $this->router->generate('interne_ajax_app_modify_property');
    }

    public function setRequest(RequestStack $request_stack)
    {
        $this->request = $request_stack->getCurrentRequest();
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('validation', array($this, 'validationFilter')),
        );
    }

    /**
     * Génère l'attribut à afficher dans le champ de formulaire, c'est à dire la classe editable ou no-editable ainsi
     * que le data-path
     * @param $label string le titre du label complet
     * @return string
     */
    public function validationFilter($label)
    {
        $parameters = $this->request->attributes;

        $path = $this->getPath($parameters, $label);

        if($this->validation->isInModification($path) == true) {

            if($this->validation->accessor->extractEntity($path)->getValidity() == 1) {
                $popup =
                    '<table class="ui definition table"><tbody>' .
                        '<tr><td>Ancienne valeur : </td><td>' . $this->parser->parseToString($this->validation->getModification($path)->getOldValue()) . '</td></tr>' .
                    '</tbody></table>';

                return 'class=no-editable data-content=' . base64_encode($popup);
            }

            return 'class=no-editable';
        }

        else
            return 'class=editable data-path=' . $path;

    }


    /**
     * Construit un schema d'attribut d'entité
     * @param $parameters ParameterBag paramètres de la requete
     * @param $label string le titre du label complet
     * @return string
     */
    public function getPath(ParameterBag $parameters, $label) {

        $label = $this->get_string_between($label, '"', '"');
        $data   = explode('_', $label);

        $id     = $parameters->get('_route_params')[$data[1]];

        $path   = $data[1] . '.' . $id . '.';

        for($i = 2; $i < count($data); $i++){

            $path .= $data[$i];
            if($i < count($data)-1)
                $path .= '.';
        }

        return $path;
    }

    /**
     * Retourne la chaine de caractère comprise entre deux chaînes de caractères
     * @param $string string la chaine totale
     * @param $start string le premier morceau
     * @param $end string le dernier morceau
     * @return string
     */
    private function get_string_between($string, $start, $end) {

        $string = " ".$string;
        $ini    = strpos($string,$start);

        if ($ini == 0) return "";

        $ini    += strlen($start);
        $len     = strpos($string,$end,$ini) - $ini;

        return substr($string,$ini,$len);
    }



    public function getName()
    {
        return 'validation_extension';
    }
}