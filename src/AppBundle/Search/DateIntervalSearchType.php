<?php

namespace AppBundle\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Field\DatePickerType;

class DateIntervalSearchType extends AbstractType
{

    /**
     * Formulaire pour ajouter un membre, gestion automatique de la détection de famille
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lower', 'datepicker', array('required'=>false,'label'=>'De'))
            ->add('higher', 'datepicker', array('required'=>false,'label'=>'à'))
            ;

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\DateIntervalSearch'
        ));
    }


    public function getName()
    {
        return 'AppBundle_date_interval_search';
    }

}
