<?php

namespace AppBundle\Search\Creance;


use AppBundle\Field\BooleanType;
use AppBundle\Search\DateIntervalSearchType;
use AppBundle\Search\ModeSearchType;
use AppBundle\Search\NumericIntervalSearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreanceSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mode', ModeSearchType::class)
            ->add('titre', TextType::class, array('label' => 'Titre', 'required' => false))
            ->add('remarque', TextareaType::class, array('label' => 'Remarque', 'required' => false))
            ->add('intervalDateCreation', DateIntervalSearchType::class, array('label' => 'Date de création', 'required' => false))
            ->add('intervalDatePayement', DateIntervalSearchType::class, array('label' => 'Date de payement', 'required' => false))
            ->add('intervalMontantEmis', NumericIntervalSearchType::class, array('label' => 'Montant emis', 'required' => false))
            ->add('intervalMontantRecu', NumericIntervalSearchType::class, array('label' => 'Montant reçu', 'required' => false))
            ->add('isFactured', BooleanType::class, array('label' => 'Facturée', 'required' => false))
            ->add('isPayed', BooleanType::class, array('label' => 'Payée', 'required' => false))
            ->add('debiteur', TextType::class, array('label' => 'Propriétaire', 'required' => false));

    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\Creance\CreanceSearch'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_bundle_creance_search_type';
    }

}