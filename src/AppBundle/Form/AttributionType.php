<?php

namespace AppBundle\Form;

use AppBundle\Entity\Attribution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AttributionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('dateDebut', 'datepicker')
            ->add('dateFin', 'datepicker')

            ->add('groupe', 'entity', array(
                'class'		=> 'AppBundle:Groupe'
            ))
            ->add('fonction', 'entity', array(
                'class'		=> 'AppBundle:Fonction'
            ))
            ->add('remarques', 'textarea', array(
                'required'	=> false,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Attribution'
        ));
    }

    public function getName()
    {
        return 'appbundle_attributiontype';
    }
}
