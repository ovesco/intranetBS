<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','text',array('required' => false, 'label' => 'Email'))
            ->add('remarques','text',array('required' => false, 'label' => 'Remarque'))
            ->add('contact_id','hidden',array('mapped'=> false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Email'
        ));
    }

    public function getName()
    {
        return 'app_bundle_addemailtype';
    }
}