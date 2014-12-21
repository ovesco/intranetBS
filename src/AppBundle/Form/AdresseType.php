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
            ->add('rue',        'text',         array('required' => false, 'label' => 'Rue'))
            ->add('npa',        'number',       array('required' => false, 'label' => 'NPA'))
            ->add('localite',   'text',         array('required' => false, 'label' => 'Localité'))
            ->add('facturable', 'checkbox',     array('required' => false, 'label' => 'Facturable'))
            ->add('remarques',  'textarea',     array('required' => false, 'label' => 'remarques'))
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
