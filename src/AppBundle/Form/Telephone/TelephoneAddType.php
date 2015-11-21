<?php

namespace AppBundle\Form\Telephone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TelephoneAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('telephone','text',array('required' => true, 'label' => 'Numéro'))
            ->add('remarques','text',array('required' => false, 'label' => 'Remarque'))
            ->add('contact_id','hidden',array('mapped'=> false))
        ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Telephone'
        ));
    }

    public function getName()
    {
        return 'app_bundle_telephone_add';
    }
}