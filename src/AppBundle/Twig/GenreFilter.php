<?php

namespace AppBundle\Twig;

class GenreFilter extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('genre', array($this, 'genreFilter')),
        );
    }

    public function genreFilter($value)
    {
        return ($value == 'm') ? 'Homme' : 'Femme';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'genre_filter';
    }
}