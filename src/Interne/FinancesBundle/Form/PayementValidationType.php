<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PayementValidationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('comment','textarea',array('label' => 'Remarque', 'required' => false,'attr'=>array('placeholder'=>'Une remarque qui pourrait aider dans le future...')))
            ->add('facture', new FactureRepartitionType())
            ->add('montantRecu','hidden')

        ;//fin de la fonction




    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Entity\Payement'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundlePayementValidationType';
    }

}