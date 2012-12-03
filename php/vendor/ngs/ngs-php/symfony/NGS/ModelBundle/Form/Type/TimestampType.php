<?php
namespace NGS\ModelBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use NGS\ModelBundle\Form\DataTransformer\TimestampToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extended timestamp field with custom Transformer for handling NGS\Timestamp type
 */
class TimestampType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new TimestampToStringTransformer())
        ;
    }

    public function getName()
    {
        return 'ngs_timestamp';
    }

    public function getParent()
    {
        return 'text';
    }
}