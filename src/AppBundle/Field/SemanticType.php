<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;

/**
 * Class SemanticType
 *
 * Type sémantique de formulaire pour un sélécteur avec recherche
 * Une classe est ajouté au <select> pour qu'il soit transformé par le code jQuery
 * Hérite de entity, n'est donc fait que pour les entités
 *
 * @package AppBundle\Form
 */
class SemanticType extends AbstractType
{
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'semantic';
    }
}