<?php

namespace AppBundle\Form\Payement;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PayementSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('idFacture','number',array('label' => 'N°Facture', 'required' => false))
            ->add('fromMontantRecu','number',array('label' => 'De', 'required' => false))
            ->add('toMontantRecu','number',array('label' => 'a', 'required' => false))
            ->add('fromDate','date',array('label' => 'De', 'required' => false))
            ->add('toDate','date',array('label' => 'à', 'required' => false))
            ->add('state','choice',array('label' => 'Lien avec facture ','required' => false,
                    'choices' => array( 'waiting' =>'En attente de validation',
                                        'not_found' =>'Facture inexistante',
                                        'found_valid'=>'Facture payée',
                                        'found_lower_valid'=>'Facture payée avec montant inférieur',
                                        'found_lower_new_facture'=>'Facture payée avec montant inférieur (et complément exigé)',
                                        'found_upper'=>'Facture payée avec montant supérieur'
                    )))







        ;//fin de la fonction




    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\SearchClass\PayementSearch'
        ));
    }


    public function getName()
    {
        return 'app_bundlePayementSearchType';
    }

}