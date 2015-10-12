<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adresse',new AdresseType())
            ->add('emails', 'collection', array(
                // chaque item du tableau sera un champ « email »
                'type'   => new EmailType()))

            ->add('telephones', 'collection', array(
                // chaque item du tableau sera un champ « email »
                'type'   => new TelephoneType()))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contact'
        ));
    }

    public function getName()
    {
        return 'appbundle_contacttype';
    }
}
