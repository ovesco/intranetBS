<?php

namespace AppBundle\Search\Creance;


use AppBundle\Search\ModeSearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Search\NumericIntervalSearchType;
use AppBundle\Search\DateIntervalSearchType;

use AppBundle\Field\BooleanType;

class CreanceSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mode',new ModeSearchType())
            ->add('titre','text',array('label' => 'Titre', 'required' => false))
            ->add('remarque','textarea',array('label' => 'Remarque', 'required' => false))
            ->add('intervalDateCreation',new DateIntervalSearchType() ,array('label' => 'Date de création', 'required' => false))
            ->add('intervalDatePayement',new DateIntervalSearchType() ,array('label' => 'Date de payement', 'required' => false))
            ->add('intervalMontantEmis',new NumericIntervalSearchType(),array('label' => 'Montant emis', 'required' => false))
            ->add('intervalMontantRecu',new NumericIntervalSearchType(),array('label' => 'Montant reçu', 'required' => false))
            ->add('isFactured','boolean',array('label' => 'Facturée', 'required' => false))
            ->add('isPayed','boolean',array('label' => 'Payée', 'required' => false))
            ->add('debiteur','text',array('label' => 'Propriétaire', 'required' => false))
        ;

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\Creance\CreanceSearch'
        ));
    }


    public function getName()
    {
        return 'app_bundle_creance_search_type';
    }

}