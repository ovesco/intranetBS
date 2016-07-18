<?php

namespace AppBundle\Search;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AttributionSearchType extends AbstractType
{

    /**
     * Formulaire pour ajouter un membre, gestion automatique de la détection de famille
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groupe', EntityType::class, array(
                'class' => 'AppBundle:Groupe',
                'required' => false
            ))
            ->add('fonction', EntityType::class, array(
                'class' => 'AppBundle:Fonction',
                'required' => false
            ));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\AttributionSearch'
        ));
    }


    public function getBlockPrefix()
    {
        return 'AppBundle_attribution_search';
    }

}
