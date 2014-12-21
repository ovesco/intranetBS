<?php

namespace Interne\FactureBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ModelType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'titre',
                'text',
                array('label' => 'Titre')
            )

            ->add(
                'remarque',
                'textarea',
                array('label' => 'Remarque')
            )
            ->add(
                'montantEmis',
                'number',
                array('label' => 'Montant')
            );


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FactureBundle\Entity\Model'
        ));
    }


    public function getName()
    {
        return 'InterneFactureBundle_modelType';
    }

}