<?php

namespace AppBundle\Form\Membre;

use Symfony\Component\Form\FormBuilderInterface;


class MembreShowType extends MembreType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'app_bundle_membre_show';
    }

}
