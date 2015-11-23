<?php

namespace AppBundle\Form\Famille;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Form\Pere\PereType;
use AppBundle\Form\Mere\MereType;
use AppBundle\Form\Contact\ContactType;

class FamilleDisabledNomType extends FamilleType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
        $builder->remove('nom')
            ->add('nom','text', array('required'	=> false, 'label' => 'Nom de famille','disabled'=>true));
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Famille',
        ));
    }

    public function getName()
    {
        return 'AppBundle_famille_disabled_nom';
    }
}
