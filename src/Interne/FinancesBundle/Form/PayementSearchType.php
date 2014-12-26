<?php

namespace Interne\FinancesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PayementSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('idFacture','number',array('label' => 'N°Facture', 'required' => false))
            ->add('montantRecu','number',array('label' => 'Montant reçu', 'required' => false))
            ->add('datePayement','date',array('label' => 'Date de payement','data'=> null,'required' => false))
            ->add('state','choice',array('label' => 'Lien avec facture ','required' => false,
                    'choices' => array( 'waiting' =>'En attente de validation',
                                        'not_found' =>'Facture inexistante',
                                        'found_valid'=>'Validé')))




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
        return 'InterneFinancesBundlePayementSearchType';
    }

}