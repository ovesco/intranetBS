<?php

namespace AppBundle\Form\Email;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','text',array('required' => false, 'label' => 'Email'))
            ->add('remarques','remarque_accordion',array('required' => false))
            ->add('expediable', 'boolean', array('required' => true, 'label' => 'Expediable'))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Email'
        ));
    }

    public function getName()
    {
        return 'app_bundle_email';
    }
}