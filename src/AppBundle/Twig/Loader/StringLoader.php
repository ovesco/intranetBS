<?php

namespace AppBundle\Twig\Loader;

class StringLoader implements \Twig_LoaderInterface {

    public function getSource($name)
    {
        return $name;
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
    {
        return true;
    }
}