<?php
namespace NGS\ModelBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

abstract class OlapController extends Controller
{
    abstract protected function getFacts();
    
    abstract protected function getDimensions();
    
    abstract protected function getCube();
    
    abstract protected function getFilter();
    
    /**
     * @Route("/")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function indexAction(Request $request)
    {
        $facts = $this->getFacts();
        $dimensions = $this->getDimensions();
        $cube = $this->getCube();
        
        $form = $this->createForm($this->getFilter());
        
        if($request->getMethod()==='POST') {
            $form->bind($request);
            $spec = $form->getData();
            $order = $request->get('_order') ? array($request->get('_order')) : array();
            $chosenDimensions = $request->get('_dimensions') ? array_keys($request->get('_dimensions')) : array();
            $chosenFacts = $request->get('_facts') ? array_keys($request->get('_facts')) : array();
        }
        else {
            $spec = null;
            $order = array();
            $chosenDimensions = $dimensions;
            $chosenFacts = $facts;
        }
        
        
        $items = $cube->analyze($dimensions, $chosenFacts, $order, $spec);
        
        return array(
            'items'             => $items,
            'dimensions'        => $dimensions,
            'facts'             => $facts,
            'filter'            => $form->createView(),
            'chosen_facts'      => $chosenFacts,
            'chosen_dimensions' => $chosenDimensions,
            'order'             => $order
        );
    }
}
