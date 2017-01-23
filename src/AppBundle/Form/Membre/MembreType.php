<?php

namespace AppBundle\Form\Membre;

use AppBundle\Field\DatePickerType;
use AppBundle\Field\RemarqueAccordionType;
use AppBundle\Form\Attribution\AttributionType;
use AppBundle\Form\Famille\FamilleType;
use AppBundle\Form\ObtentionDistinction\ObtentionDistinctionType;
use AppBundle\Form\Personne\PersonneType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Field\BooleanType;
use AppBundle\Field\DateType as AppDateType;

class MembreType extends PersonneType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('famille', FamilleType::class)
            //->add('attributions', CollectionType::class, array('entry_type' => AttributionType::class))
            ->add('distinctions', CollectionType::class, array('entry_type' => ObtentionDistinctionType::class))

            ->add('naissance', AppDateType::class, array('label' => 'Date de naissance'))
            ->add('numeroAvs', NumberType::class,
                array(
                    'label' => 'Numéro AVS',
                    'required' => false,
                    'attr' => array(
                        'data-formatter' => 'true',
                        'data-pattern' => '{{9999999999999}}'
                    )
                )
            )
            ->add(
                'iban',
                TextType::class,
                array(
                    'label' => 'IBAN',
                    'required' => false,
                )
            )
            ->add(
                'numeroBs',
                NumberType::class,
                array(
                    'label' => 'Numéro BS',
                    'required' => false,
                )
            )
            ->add('remarques', RemarqueAccordionType::class,
                array('required' => false)
            )
            ->add(
                'envoiFacture',
                ChoiceType::class,
                array(
                    'choices' => array(
                        'Membre' => 'Membre',
                        'Famille' => 'Famille'),
                    'choices_as_values' => true
                )
            )
            ->add('inscriptionDate', AppDateType::class, array( 'label' => 'Inscription'))
            ->add('desinscriptionDate', AppDateType::class, array('label' => 'Désinscription'))
            ->add('decede', BooleanType::class, array('label' => 'Décédé'))
            ->add('id', HiddenType::class);
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_membre';
    }
}
