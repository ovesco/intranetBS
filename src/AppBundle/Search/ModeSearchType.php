<?php

namespace AppBundle\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'mode_search';
    }
}