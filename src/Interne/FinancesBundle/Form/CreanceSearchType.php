<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CreanceSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre','text',array('label' => 'Titre', 'required' => false))
            ->add('remarque','textarea',array('label' => 'Remarque', 'required' => false))
            ->add('fromDateCreation','date',array('label' => 'from', 'required' => false))
            ->add('toDateCreation','date',array('label' => 'To', 'required' => false))
        ;






    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\SearchClass\CreanceSearch'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundle_CreanceSearch_Type';
    }

}