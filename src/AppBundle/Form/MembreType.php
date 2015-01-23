<?php

namespace AppBundle\Form;

use AppBundle\Entity\Famille;
use AppBundle\Field\MembreFamilleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Field\SemanticType;
use AppBundle\Field\GenreType;
use AppBundle\Field\DatePickerType;


class MembreType extends AbstractType
{

    /**
     * Formulaire pour ajouter un membre, gestion automatique de la détection de famille
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add(
                'prenom',
                'text',
                array('label' => 'Prénom')
            )

            ->add(
                'naissance',
                'datepicker'
            )

            ->add(
                'sexe',
                'genre'
            )

            ->add(
                'numeroAvs',
                'text',
                array(
                    'label' => 'Numéro AVS',
                    'required' => false
                )
            )

            ->add(
                'iban',
                'text',
                array(
                    'label' => 'IBAN',
                    'required' => false
                )
            )

            ->add(
                'remarques',
                'textarea',
                array(
                    'required' => false
                )
            )

            ->add(
                'contact',
                new ContactType()
            )

            ->add('envoiFacture','choice', array('choices' => array('Membre' => 'Membre', 'Famille' => 'Famille')))

            ->add(
                'id',
                'hidden'
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }


    public function getName()
    {
        return 'AppBundle_membre';
    }

}
