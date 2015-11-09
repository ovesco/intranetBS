<?php

namespace AppBundle\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Transformer\BooleanToStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Transformer\BooleanToIntegerTransformer;

/**
 * @package AppBundle\Form
 */
class BooleanType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                true   => "Oui",
                false   => "Non"
            )
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new BooleanToIntegerTransformer());
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