<?php

namespace App\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class StringToArrayTransformer implements DataTransformerInterface
{

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        return $value[0];
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        return array($value);
    }
}