<?php

namespace AppBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DatetimeToTextTransformer implements DataTransformerInterface
{
    /**
     * Transforme une datetime en string
     */
    public function transform($date)
    {
        var_dump($date);
        return $date['day'] . '.' . $date['month'] . '.' . $date['year'];
    }

    /**
     * Transforme une string en Datetime
     */
    public function reverseTransform($date)
    {
        var_dump($date);
        $datetime = new \DateTime($date);
        return $datetime;
    }
}