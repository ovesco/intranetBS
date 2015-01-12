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
            ->add('validity', 'checkbox',     array('required' => false, 'label' => 'Validité'))
            ->add('adressable', 'checkbox',     array('required' => false, 'label' => 'Adressable'))
            ->add('rue',        'text',         array('required' => false, 'label' => 'Rue'))
            ->add('npa',        'number',       array('required' => false, 'label' => 'NPA'))
            ->add('localite',   'text',         array('required' => false, 'label' => 'Localité'))
            ->add('remarques',  'textarea',     array('required' => false, 'label' => 'Remarques'))
            ->add('email',  'text',     array('required' => false, 'label' => 'Email'))
            ->add('telephone',  'text',     array('required' => false, 'label' => 'Telephone'))
            ->add('methodeEnvoi',  'choice',     array('required' => false, 'label' => 'Méthode d\'envoi', 'choices'=> array('Email'=>'Email','Courrier'=>'Courrier')))
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
