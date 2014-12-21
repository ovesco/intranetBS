<?php

namespace Interne\FactureBundle\Form;

use Interne\FactureBundle\Controller\CreanceController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class FactureSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*
             * Elements appartenant aux factures
             */
            ->add('id','number',array('label' => 'Num. de référance','required' => false))
            ->add('montantRecu','number',array('label' => 'Montant reçu','required' => false,'data'=> null))
            ->add('statut','choice',array('label' => 'Statut','required' => false,'choices' => array('ouverte'=>'Ouverte', 'payee'=>'Payée'),'data' => null))
            ->add('dateCreation','date',array('label' => 'Date de création','data'=> null,'required' => false))
            ->add('datePayement','date',array('label' => 'Date de Payement','data'=> null,'required' => false))
            /*
             * l'option "mapped (false)" permet d'ajouter des champs qui n'appartiennent
             * pas à l'entité.
             */
            ->add('nombreRappel','number',array('label' => 'Nombre de Rappel','required' => false,'mapped' => false))

            ->add('montantRecuMinimum','number',array('required' => false,'mapped' => false))
            ->add('montantRecuMaximum','number',array('required' => false,'mapped' => false))
            ->add('montantFrais','number',array('required' => false,'mapped' => false))
            ->add('montantFraisMinimum','number',array('required' => false,'mapped' => false))
            ->add('montantFraisMaximum','number',array('required' => false,'mapped' => false))
            ->add('montantTotal','number',array('required' => false,'mapped' => false))
            ->add('montantTotalMinimum','number',array('required' => false,'mapped' => false))
            ->add('montantTotalMaximum','number',array('required' => false,'mapped' => false))

            ->add('datePayementMaximum','date',array('data'=> null,'required' => false,'mapped' => false))
            ->add('datePayementMinimum','date',array('data'=> null,'required' => false,'mapped' => false))


        ;//fin de la fonction builder



    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FactureBundle\Entity\Facture'
        ));
    }


    public function getName()
    {
        return 'InterneFactureBundleFactureSearchType';
    }

}
