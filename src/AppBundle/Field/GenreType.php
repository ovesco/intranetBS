<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Personne;

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

    public function getName()
    {
        return 'genre';
    }
}