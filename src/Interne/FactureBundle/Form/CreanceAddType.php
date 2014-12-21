<?php

namespace Interne\FactureBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class CreanceAddType extends AbstractType
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
                array('label' => 'Remarque', 'required' => false, 'data' => '')
            )
            ->add(
                'montantEmis',
                'number',
                array('label' => 'Montant')
            )
            ->add(
                'idOwner',
                'hidden',
                array(  'required' => false,
                        'mapped' => false)
            )
            ->add(
                'classOwner',
                'hidden',
                array(  'required' => false,
                        'mapped' => false)
            )
            ->add(
                'idsMembre',
                'hidden',
                array(  'required' => false,
                        'mapped' => false)
            )
            /*
            ->add(
                'model',
                'entity',
                array(
                    'mapped' => false,
                    'required' => false,
                    'class' => 'InterneFactureBundle:Model',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('model');
                    },
        ))
            */
            ;//fin de fonction


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FactureBundle\Entity\Creance'
        ));
    }


    public function getName()
    {
        return 'InterneFactureBundle_creanceAddType';
    }

}