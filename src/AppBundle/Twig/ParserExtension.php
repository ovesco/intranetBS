<?php

namespace AppBundle\Twig;

use AppBundle\Utils\Accessor\Parser;
use AppBundle\Utils\Data\Validation;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;

class ParserExtension extends \Twig_Extension
{
    private $parser;

    public function __construct(Parser $parser) {

        $this->parser       = $parser;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('parser', array($this, 'parserFilter')),
        );
    }

    /**
     * L'extension parser fonctionne en lui passant un paramètre. En effet, plusieurs types de parsages sont disponibles.
     * Types disponible : default
     * @param $data mixed la valeur d'entrée
     * @param $type string le type de parsage a effectuer
     * @return mixed
     */
    public function parserFilter($data, $type)
    {
        if($type == 'default') return $this->parser->parseToString($data);

        return $data;
    }

    public function getName() {

        return 'parser_extension';
    }
}
