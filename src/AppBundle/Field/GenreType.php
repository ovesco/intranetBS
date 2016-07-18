<?php

namespace AppBundle\Field;

use AppBundle\Entity\Personne;
use Symfony\Component\Form\AbstractType;

/**
 * Class GenreType
 *
 * Type à utiliser pour un champ définissant le genre dans un formulaire
 *
 * @package AppBundle\Form
 */
class GenreType extends AbstractType
{
    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
               Personne::HOMME => 'Homme',
               Personne::FEMME => 'Femme',
            )
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getBlockPrefix()
    {
        return 'genre';
    }
}