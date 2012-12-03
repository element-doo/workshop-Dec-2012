<?php
namespace NGS\ModelBundle\Form\Type;

use NGS\ModelBundle\Form\DataTransformer\IdentifiableToUriTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extended reference field with custom Transformer for handling Identifiable type
 */
class LookupType extends ReferenceType
{
    private $container;
    private $level = true;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array('controller'));
        $resolver->setOptional(array('actions'));

        $resolver->setDefaults(array(
            'actions'    => true,
            'compound'   => false,
        ));
    }

    public function getName()
    {
        return 'ngs_lookup';
    }

    public function getParent()
    {
        return 'text';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $controller = new $options['controller'];
        $controller->setContainer($this->container);

        if ($this->level) {
            $this->level = false;
                $view->vars['lookup_form'] = $controller->buildForm()->createView();
            $this->level = true;
        } 
        // @todo getRoutePrefix needs fixing
        $chunks = explode('\\', get_class($controller));
        $routePrefix = strtolower(implode('_', $chunks));
        $routePrefix = str_replace(array('controller_', 'controller', 'bundle'), '', $routePrefix);

        $view->vars['route_prefix'] = $routePrefix.'_';
    }
}
