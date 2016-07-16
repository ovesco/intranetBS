<?php

namespace AppBundle\Form\Email;

use AppBundle\Field\BooleanType;
use AppBundle\Field\RemarqueAccordionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, array('required' => false, 'label' => 'Email'))
            ->add('remarques', RemarqueAccordionType::class, array('required' => false))
            ->add('expediable', BooleanType::class, array('required' => true, 'label' => 'Expediable'));
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Email'
        ));
    }

    public function getName()
    {
        return 'app_bundle_email';
    }
}