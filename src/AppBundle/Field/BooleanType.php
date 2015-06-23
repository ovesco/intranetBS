<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Transformer\BooleanToStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @package AppBundle\Form
 */
class BooleanType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                "1"   => "Oui",
                "0"   => "Non"
            )
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new BooleanToStringTransformer("Oui", "Non"));
    }


    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'boolean';
    }
}