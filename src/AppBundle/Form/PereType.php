<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Form\ContactType;
use AppBundle\Form\AdresseType;

class PereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', 'text', array('required' => false, 'label' => 'Prénom'))
            ->add('profession', 'text', array('required' => false, 'label' => 'Profession'))
            ->add('contact', new ContactType())
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Pere'
        ));
    }

    public function getName()
    {
        return 'appbundle_pere_type';
    }
}
