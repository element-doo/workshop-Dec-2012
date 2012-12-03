<?php
namespace NGS\ModelBundle\Form\DataTransformer;

use NGS\Money;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer as BaseTransformer;

/**
 * Transformer extended for handling NGS\Money type
 */
class MoneyToLocalizedStringTransformer extends BaseTransformer
{
    public function transform($value)
    {
        if($value === null || (is_string($value)&&trim($value)===''))
            return null;
        if ($value instanceof Money) {
            $value = (string)$value;
        }
        return parent::transform($value);
    }

    public function reverseTransform($value)
    {
        if($value === null || (is_string($value)&&trim($value)==='')) {
            // throw new TransformationFailedException('money cannot be null');
            return null;
        }
        try {
            return new Money($value);
        }
        catch(\InvalidArgumentException $ex) {
            throw new TransformationFailedException($ex->getMessage());
        }
    }
}