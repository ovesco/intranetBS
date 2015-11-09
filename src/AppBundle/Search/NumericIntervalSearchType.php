<?php

namespace AppBundle\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NumericIntervalSearchType extends AbstractType
{

    /**
     * Formulaire pour ajouter un membre, gestion automatique de la détection de famille
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lower', 'number', array('required'=>false,'label'=>'De'))
            ->add('higher', 'number', array('required'=>false,'label'=>'à'))
            ;

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\NumericIntervalSearch'
        ));
    }


    public function getName()
    {
        return 'AppBundle_numeric_interval_search';
    }

}
