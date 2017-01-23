<?php

namespace AppBundle\Field;

use AppBundle\Transformer\BooleanToIntegerTransformer;
use AppBundle\Transformer\BooleanToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType as SymfonyDateType;

/**
 * @package AppBundle\Form
 */
class DateType extends SymfonyDateType
{

    /** @var string */
    private $format;

    public function __construct($format)
    {
        $this->format = $format;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => $this->format,
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_date';
    }
}