<?php

namespace AppBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BooleanToIntegerTransformer implements DataTransformerInterface
{

    /**
     * @param boolean $boolean
     * @return null|boolean
     */
    public function transform($boolean)
    {
        if (null === $boolean) {
            return null;
        }

        return true === boolval($boolean) ? 1 : 0;
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }
        return boolval($value);
    }
}