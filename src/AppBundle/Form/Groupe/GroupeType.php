<?php

namespace AppBundle\Form\Groupe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent','entity', array(
                'class'		=> 'AppBundle:Groupe',
                'property'	=> 'nom',
                'label' => 'Groupe parent',
                'required'=> false,
                'empty_value'  => 'Groupe racine'
            ))
            ->add('nom')
            ->add('active','hidden',array('data'=>true))
            ->add('model', 'entity', array(
                'class'		=> 'AppBundle:Model',
                'property'	=> 'nom'
            ))

        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Groupe'
        ));
    }

    public function getName()
    {
        return 'app_bundle_groupe';
    }
}