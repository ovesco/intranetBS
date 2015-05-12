<?php

namespace Interne\FinancesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class RappelType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'dateCreation',
                'date',
                array('label' => 'Date du rappel',
                    'data' => new \DateTime())
            )
            ->add(
                'montantEmis',
                'number',
                array(  'label' => 'Frais de rappel',
                        'required' => false

                )
            )
            ->add('montantEmisMinimum','number',array('required' => false,'mapped' => false))
            ->add('montantEmisMaximum','number', array('required' => false,'mapped' => false))
            ->add('dateCreationMaximum','date',array('data'=> null,'required' => false,'mapped' => false))
            ->add('dateCreationMinimum','date',array('data'=> null,'required' => false,'mapped' => false))
        ;


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Entity\Rappel'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundle_rappelSearchType';
    }

}
