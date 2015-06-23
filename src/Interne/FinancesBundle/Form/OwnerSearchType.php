<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class OwnerSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('membreNom','text',array('label' => 'Nom', 'required' => false))
            ->add('membrePrenom','text',array('label' => 'Prenom', 'required' => false))
            ->add('familleNom','text',array('label' => 'Nom de famille', 'required' => false))

        ;//fin de la fonction
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundleOwnerSearchType';
    }

}