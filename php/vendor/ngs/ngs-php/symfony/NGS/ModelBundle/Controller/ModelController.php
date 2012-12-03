<?php
namespace NGS\ModelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use NGS\ModelBundle\Util\Grid;
use NGS\ModelBundle\Util\AggregateCache;
use NGS\Patterns\Specification;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstract controller with basic CRUD actions
 */
abstract class ModelController extends BaseController
{
    /**
     * Get custom form type instance
     * @return \Symfony\Component\Form\AbstractType
     */
    public abstract function getFormType();

    /**
     * Get object class name as string
     * @return string
     */
    public abstract function getClass();


    public function getEditTemplate(){
        return ':Model:edit.html.twig';
    }

    public function getAddTemplate(){
        return ':Model:edit.html.twig';
    }

    /**
     * Get sensible defaults for constructing new object
     * @return array
     */
    public function getDefaults()
    {
        return array();
    }

    public function getSubmitted($formType=null)
    {
        if($formType===null)
            $formType = $this->getFormType();
        $data = $this->getRequest()->get($formType->getName());
        unset($data['_token']);
        return $data;
    }

    /**
     * Convert submitted form values
     *
     * @todo replace with SF2 transformers?
     *
     * @return array
     */
    public function transformPost(array $values)
    {
        return $values;
    }

    /**
     * Helper for extracting errors from form child elements
     */
    public function getFormErrors(FormInterface $form)
    {

        $errors = array();
        foreach($form->all() as $field) {
            $childErrors = $this->getFormErrors($field);
            if($childErrors)
                $errors[$field->getName()] = $childErrors;
        }
        foreach($form->getErrors() as $error) {
            $errors['errors'] = $error->getMessage();
        }
        return $errors;
    }

    /**
     * Builds form of custom type using default values
     */
    public function buildForm($item=null)
    {
        $form = $this->createForm($this->getFormType(), $item);
        return $form;
    }

    // @todo needs fixing: this->getRequest depends on global request
    // returns wrong route if controller is separatly instantiated
    public function getBaseRoutePrefix()
    {
        $currentRoute = $this->getRequest()->get('_route');
        $chunks = explode('_', $currentRoute);
        if(isset($chunks[1]))
            return $chunks[0].'_'.$chunks[1].'_';
        else
            return $chunks[0];
    }

    public function getRoutePrefix()
    {

        $class = explode('\\', get_class($this));
        $name = strtolower(str_replace('Controller', '', end($class)));
        // @todo remove hc
        return $this->getBaseRoutePrefix().$name.'_';
    }

    public function getBundleName()
    {
        $class = get_class($this);
        $chunks = explode('\\', $class);

        if(isset($chunks[1]))
            return $chunks[0].$chunks[1];
        else
            return $chunks[0];
    }

    /**
     * Shorthand for redirect
     */
    /*public function redirect($route_sufix, $status = 302)
    {
        return parent::redirect(
            $this->generateUrl($this->getRoutePrefix().$route_sufix),
            $status
        );
    }*/

    /**
     * Shorthand for json errors
     */
    protected function jsonError ($message) 
    {
        return new Response ('{ "error": ' . json_encode ($message) . ' }', 200,
            array("Content-Type: application/json"));
    }

    /**
     * @Route("/")
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {
        $item = null;
        $class = $this->getClass();
        $formType = $this->getFormType();
        $post = $request->get($formType->getName());

        $uri = isset($post['URI']) && trim($post['URI']) ? $post['URI'] : null;

        if($uri) {
            try {
                $item = $class::find($uri);
            } catch(\Exception $ex) {
                if($request->isXmlHttpRequest())
                    return $this->jsonError ($ex->getMessage());

                $this->get('messenger')->error($ex->getMessage());
            }
        }

        try {
            $form = $this->buildForm($item);
            $form->bind($request);

            $modelData = $form->getData();
            if($item) {
                $modelArray = $modelData->toArray();
                $modelArray['URI'] = $uri;
                $converter = $class.'ArrayConverter';
                $item = $converter::fromArray($modelArray);
            }
            else {
                $item = $modelData;
            }
        }
        catch(\Exception $e) {
            if($request->isXmlHttpRequest())
                return $this->jsonError ($e->getMessage());

            $this->get('messenger')->error($e->getMessage());

            if(class_exists('FB'))
                \FB::log('invalid build error');

            return $this->render($this->getBundleName().($uri === null ? $this->getAddTemplate():$this->getEditTemplate()), array(
                'route_prefix' => $this->getRoutePrefix(),
                'form'         => $form->createView()
            ));
        }

        if($form->isValid()) {
            try {
                $item->persist();
                $this->get('messenger')->info('Data saved');
                if($request->isXmlHttpRequest()) {
                    $json = '{"data": {"item": ' . $item->toJson() . '}}';
                    return new Response ($json, 200, array(
                                'Content-Type: application/json'
                    ));
                } else
                    return $this->redirect($this->generateUrl($this->getRoutePrefix().'index'));
            }
            catch (\Exception $e) {
                if($request->isXmlHttpRequest())
                    return $this->jsonError ($e->getMessage());
                $this->get('messenger')->error($e->getMessage());
            }
        }
        else {
            if(class_exists('FB'))
                \FB::log('invalid form error');

            if($request->isXmlHttpRequest())
                return $this->jsonError ('Invalid values in form');

            $this->get('messenger')->error('Invalid values in form');
            foreach($this->getFormErrors($form) as $field=>$msg) {
                // $this->get('messenger')->error($field.': '.$msg);
            }
        }

        return $this->render($this->getBundleName().($uri === null ? $this->getAddTemplate():$this->getEditTemplate()), array(
            'route_prefix' => $this->getRoutePrefix(),
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/delete")
     * @Method({"POST"})
     */
    public function deleteAction()
    {
        $uri = $this->getRequest()->get ('uri');
        $class = $this->getClass();
        $item = $class::find($uri);
        if($item !== null) {
            try {
                $item->delete();
                $this->get('messenger')->info('Item deleted');
            } catch (\Exception $e) {
                if($this->getRequest()->isXmlHttpRequest())
                    return $this->jsonError ($e->getMessage());

                $this->get('messenger')->error ($e->getMessage());
            }
        } else {
            if($this->getRequest()->isXmlHttpRequest())
                return $this->jsonError ('Unknown object: '.$uri);

            $this->get('messenger')->info('Unknown object: '.$uri);
        }

        $location = $this->getRequest()->get('location', 
            $this->generateUrl($this->getRoutePrefix().'index'));

        return $this->redirect($location);
    }

    /**
     * @Route("/add")
     * @Method({"GET"})
     */
    public function addAction()
    {
        $form = $this->buildForm();
        /*
        if($form->has('URI'))
            $form->remove('URI');
        if($form->has('ID'))
            $form->remove('ID');
*/

        return $this->render($this->getBundleName().$this->getAddTemplate(), array(
            'route_prefix' => $this->getRoutePrefix(),
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/edit/{uri}")
     * @Method({"GET"})
     */
    public function editAction($uri)
    {
        $class = $this->getClass();

        $item = $class::find($uri);

        if(!$item) {
            $this->get('messenger')->error('Could not find object with uri "'.$uri.'"');
            return $this->redirect($this->generateUrl($this->getRoutePrefix().'index'));
        }

        $form = $this->buildForm($item);

        // $form should not write property URI (property path set to false)
        // because URI property is not settable, so we have to manually set URI
        $form->get('URI')->setData($uri);

        return $this->render($this->getBundleName().$this->getEditTemplate(), array(
            'route_prefix' => $this->getRoutePrefix(),
            'item' => $item,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/")
     * @Method({"GET"})
     * @Template()
     */
    public function indexAction()
    {
        $class = $this->getClass();
        $items = $class::findAll();
        AggregateCache::save($class, $items);

        return array(
            'items' => $items,
            'route_prefix' => $this->getRoutePrefix()
        );
    }

    public function search(Request $request, Specification $specification)
    {
        $result = Grid::fromSpecification($specification, $request);
        $result['route_prefix'] = $this->getRoutePrefix();
        return $result;
    }
}
