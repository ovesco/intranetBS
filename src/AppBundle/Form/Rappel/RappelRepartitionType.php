<?php

namespace AppBundle\Form\Rappel;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;


class RappelRepartitionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montantEmis', HiddenType::class, array('label' => false))
            ->add('montantRecu', NumberType::class, array('label' => false));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Rappel'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_rappel_repartition';
    }

}
