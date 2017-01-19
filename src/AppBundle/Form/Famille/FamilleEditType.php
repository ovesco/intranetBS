<?php

namespace AppBundle\Form\Famille;

use Symfony\Component\Form\FormBuilderInterface;

class FamilleEditType extends FamilleType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
    }

    public function getBlockPrefix()
    {
        return 'AppBundle_famille_edit';
    }
}
