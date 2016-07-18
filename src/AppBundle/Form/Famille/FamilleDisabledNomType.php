<?php

namespace AppBundle\Form\Famille;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FamilleDisabledNomType extends FamilleType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('nom')
            ->add('nom', TextType::class, array('required' => false, 'label' => 'Nom de famille', 'disabled' => true));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Famille',
        ));
    }

    public function getBlockPrefix()
    {
        return 'AppBundle_famille_disabled_nom';
    }
}
