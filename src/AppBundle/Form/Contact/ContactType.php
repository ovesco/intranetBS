<?php

namespace AppBundle\Form\Contact;

use AppBundle\Field\DynamicCollectionType;
use AppBundle\Form\Adresse\AdresseType;
use AppBundle\Form\Email\EmailType;
use AppBundle\Form\Telephone\TelephoneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adresse', AdresseType::class)
            ->add('emails', DynamicCollectionType::class, array(
                // chaque item du tableau sera un champ « email »
                'entry_type' => EmailType::class))
            ->add('telephones', DynamicCollectionType::class, array(
                // chaque item du tableau sera un champ « email »
                'entry_type' => TelephoneType::class))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contact'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_contact';
    }
}
