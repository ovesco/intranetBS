<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 *
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
                'm' => 'Homme',
                'f' => 'Femme',
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