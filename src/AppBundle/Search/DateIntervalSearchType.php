<?php

namespace AppBundle\Search;

use AppBundle\Field\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DateIntervalSearchType extends AbstractType
{

    /**
     * Formulaire pour ajouter un membre, gestion automatique de la détection de famille
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lower', DatePickerType::class, array('required' => false, 'label' => 'De'))
            ->add('higher', DatePickerType::class, array('required' => false, 'label' => 'à'))
            ;

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\DateIntervalSearch'
        ));
    }


    public function getBlockPrefix()
    {
        return 'AppBundle_date_interval_search';
    }

}
