<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PayementAddType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('idFacture','integer',array('label' => 'N°Facture', 'required' => false))
            ->add('montantRecu','number',array('label' => 'Montant reçu', 'required' => false))

        ;//fin de la fonction




    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Entity\Payement'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundlePayementAddType';
    }

}