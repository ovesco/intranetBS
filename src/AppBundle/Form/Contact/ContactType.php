<?php

namespace AppBundle\Form\Contact;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\Email\EmailType;
use AppBundle\Form\Telephone\TelephoneType;
use AppBundle\Form\Adresse\AdresseType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adresse',new AdresseType())
            ->add('emails', 'custom_collection', array(
                // chaque item du tableau sera un champ « email »
                'type'   => new EmailType()))

            ->add('telephones', 'custom_collection', array(
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
        return 'app_bundle_contact';
    }
}
