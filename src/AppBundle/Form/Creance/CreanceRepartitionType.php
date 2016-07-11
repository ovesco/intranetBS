<?php

namespace AppBundle\Form\Creance;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;


class CreanceRepartitionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', HiddenType::class, array('label' => false))
            ->add('montantEmis', HiddenType::class, array('label' => false))
            ->add('montantRecu', NumberType::class, array('label' => false))

        ;//fin de fonction
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Creance'
        ));
    }


    public function getName()
    {
        return 'app_bundle_creance_repartition';
    }

}