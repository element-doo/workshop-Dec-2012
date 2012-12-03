<?php
namespace NGS\ModelBundle\Form\DataTransformer;

use InvalidArgumentException;
use NGS\Timestamp;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TimestampToStringTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if($value instanceof Timestamp)
            return (string)$value;
      
        if(is_string($value))
            return $value;
        
        // By convention, transform() should return an empty string if NULL is passed.
        if($value === null)
            return ''; 
        
        throw new TransformationFailedException('Could not transform value to string, was expecting NGS\Timestamp, but given value was type :"'.\NGS\Utils::getType($value).'"');
    }

    public function reverseTransform($value)
    {
        // By convention, reverseTransform() should return NULL if an empty string is passed.
        if($value === null || $value==='')
            return null;
        
        try {
            return new Timestamp($value);
        }
        catch(InvalidArgumentException $ex) {
            throw new TransformationFailedException($ex->getMessage());
        }
    }
}
