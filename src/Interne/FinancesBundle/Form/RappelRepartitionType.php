<?php

namespace Interne\FinancesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class RappelRepartitionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('montantEmis','hidden',array('label' => false))
            ->add('montantRecu','number',array('label' => false))
            ;


    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Entity\Rappel'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundleRappelRepartitionType';
    }

}
