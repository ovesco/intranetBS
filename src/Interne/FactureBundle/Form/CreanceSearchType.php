<?php

namespace Interne\FactureBundle\Form;


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


            ->add('isLinkedToFacture','choice',array('label' => 'Lien avec facture ','required' => true, 'mapped' => false,'choices' => array('no' =>'En attente de facturation', 'yes' =>'Faturée'),'data' => 'yes'))

            ->add('searchOption', 'choice',
                array(
                    'required' => true,
                    'mapped' => false,
                    'data' => 'new',
                    'choices' => array(
                        'new'   => 'Nouvelle recherche',
                        'add' => 'Ajouter à la recherche actuelle',
                        'substract'   => 'Soustraire à la recherche actuelle',
                )))



            ->add('membreNom','text', array('label' => 'Nom','required' => false,'mapped' => false))
            ->add('membrePrenom','text', array('label' => 'Prénom','required' => false,'mapped' => false))
            ->add('familleNom','text', array('label' => 'Famille','required' => false,'mapped' => false))
            ;//fin de la fonction




    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Interne\FactureBundle\Entity\Creance'
        ));
    }


    public function getName()
    {
        return 'InterneFactureBundleCreanceSearchType';
    }

}