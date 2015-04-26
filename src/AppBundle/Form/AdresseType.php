<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('expediable', 'boolean',      array('label'    => 'expediable'))
            ->add('rue',        'text',         array('required' => false, 'label' => 'Rue'))
            ->add('npa',        'number',       array('required' => false, 'label' => 'NPA'))
            ->add('localite',   'text',         array('required' => false, 'label' => 'Localité'))
            ->add('remarques',  'textarea',     array('required' => false, 'label' => 'Remarques'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Adresse'
        ));
    }

    public function getName()
    {
        return 'appbundle_adressetype';
    }
}
