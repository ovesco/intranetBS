<?php

namespace AppBundle\Form\Membre;

use Symfony\Component\Form\FormBuilderInterface;


class MembreEditType extends MembreType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        /*
         * dans le cas ou on veut faire des modifications dans ce formulaire
         * par rapport à MembreType
         */
        $builder
            ->remove('famille')
            ->remove('distinctions');
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_membre_edit';
    }

}
