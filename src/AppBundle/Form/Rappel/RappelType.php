<?php

namespace AppBundle\Form\Rappel;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;


class RappelType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'dateCreation',
                DateType::class,
                array(
                    'label' => 'Date du rappel',
                    'data' => new \DateTime()
                )
            )
            ->add(
                'montantEmis',
                NumberType::class,
                array(
                    'label' => 'Frais de rappel',
                    'required' => false
                )
            );
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Rappel'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_rappelType';
    }

}
