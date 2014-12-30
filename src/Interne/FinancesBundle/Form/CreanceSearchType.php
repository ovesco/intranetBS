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
            ->add('montantEmis','number',array('label' => 'Montant émis', 'required' => false))
            ->add('montantRecu','number',array('label' => 'Montant reçu', 'required' => false))
            ->add('dateCreation','date',array('label' => 'Date de création','data'=> null,'required' => false))
            ->add('datePayement','date',array('label' => 'Date de payement','data'=> null,'required' => false))


            /*
             * l'option "mapped (false)" permet d'ajouter des champs qui n'appartiennent
             * pas à l'entité.
             */
            ->add('montantEmisMinimum','number',array('required' => false,'mapped' => false))
            ->add('montantEmisMaximum','number', array('required' => false,'mapped' => false))
            ->add('montantRecuMinimum','number',array('required' => false,'mapped' => false))
            ->add('montantRecuMaximum','number', array('required' => false,'mapped' => false))

            ->add('dateCreationMaximum','date',array('data'=> null,'required' => false,'mapped' => false))
            ->add('dateCreationMinimum','date',array('data'=> null,'required' => false,'mapped' => false))
            ->add('datePayementMaximum','date',array('data'=> null,'required' => false,'mapped' => false))
            ->add('datePayementMinimum','date',array('data'=> null,'required' => false,'mapped' => false))

            ->add('isLinkedToFacture','choice',array('label' => 'Lien avec facture ','required' => false, 'mapped' => false,'choices' => array('no' =>'En attente de facturation', 'yes' =>'Faturée')))

            ;//fin de la fonction




    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FinancesBundle\Entity\Creance'
        ));
    }


    public function getName()
    {
        return 'InterneFinancesBundleCreanceSearchType';
    }

}