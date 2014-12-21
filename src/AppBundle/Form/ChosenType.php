<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;

/**
 * Class ChosenType
 *
 * Type sémantique de formulaire pour un sélécteur avec recherche nommé "chosen"
 * Une classe est ajouté au <select> pour qu'il soit transformé par le code jQuery
 * Hérite de entity, n'est donc fait que pour les entités
 *
 * @package AppBundle\Form
 */
class ChosenType extends AbstractType
{
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'chosen';
    }
}