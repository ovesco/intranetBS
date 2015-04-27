<?php

namespace AppBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BooleanToStringTransformer implements DataTransformerInterface
{
    private $trueValue;
    private $falseValue;

    public function __construct($trueValue, $falseValue)
    {
        $this->trueValue  = $trueValue;
        $this->falseValue = $falseValue;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        return true === boolval($value) ? $this->trueValue : $this->falseValue;
    }

    public function reverseTransform($value)
    {
        return $value;
    }
}