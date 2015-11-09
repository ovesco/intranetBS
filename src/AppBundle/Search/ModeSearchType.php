<?php

namespace AppBundle\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Search\Mode;

/**
 * Class ModeSearchType
 *
 * Type à utiliser pour un champ définissant le mode de recherche
 * dans les formulaire de recherche.
 *
 *
 */
class ModeSearchType extends AbstractType
{
    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                Mode::MODE_STANDARD => 'Standard',
                Mode::MODE_INCLUDE=> 'Inclure prédédentes',
                Mode::MODE_EXCLUDE=> 'Exclure prédédentes',
            ),
            'mapped'=>false,
            'required'=>true,
            'label'=>'Mode de recherche'

        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'mode_search';
    }
}