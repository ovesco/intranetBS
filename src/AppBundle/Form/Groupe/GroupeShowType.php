<?php

namespace AppBundle\Form\Groupe;

use Symfony\Component\Form\FormBuilderInterface;

class GroupeShowType extends GroupeType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
        $builder
            ->remove('parent')
            ->remove('model')
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Groupe'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_groupe_show';
    }
}
