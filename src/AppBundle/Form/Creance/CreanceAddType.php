<?php

namespace AppBundle\Form\Creance;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class CreanceAddType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'titre',
                TextType::class,
                array('label' => 'Titre')
            )

            ->add(
                'remarque',
                TextareaType::class,
                array('label' => 'Remarque', 'required' => false)
            )
            ->add(
                'montantEmis',
                NumberType::class,
                array('label' => 'Montant')
            )
            ->add(
                'idOwner',
                HiddenType::class,
                array(  'required' => false,
                        'mapped' => false)
            )
            ->add(
                'classOwner',
                HiddenType::class,
                array(  'required' => false,
                        'mapped' => false)
            )
            

            ;//fin de fonction


    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Creance'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_creance_add_type';
    }

}