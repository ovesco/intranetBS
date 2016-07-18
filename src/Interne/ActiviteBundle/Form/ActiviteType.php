<?php

namespace Interne\ActiviteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActiviteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateDebut', DatePickerType::class, array('label' => "Début de l'activité"))
            ->add('dateFin', DatePickerType::class, array('label' => "Fin de l'activité"))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\ActiviteBundle\Entity\Activite'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'interne_activitebundle_activite';
    }
}
