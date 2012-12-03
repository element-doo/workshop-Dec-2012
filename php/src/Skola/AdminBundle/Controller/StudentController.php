<?php

namespace Skola\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Skola\AdminBundle\Form\Type\StudentType;

use School\Student;
use School\StudentArrayConverter;

/**
 * @Route("/student")
 */
class StudentController extends Controller
{

	protected static function randomWord() {
		$randomWord = "";
		$n = rand(3,7);
		for($i = 0; $i < $n; $i++)
			$randomWord .= chr(rand(ord('a'), ord('z')));
		$randomWord = ucfirst($randomWord);

		return $randomWord;
	}

	protected static function generateRandomStudent() {
		$randomDate = new \DateTime();
		$start = new \DateTime("1970-1-1");
		$end = new \DateTime("2000-12-31");
		$randomSecond = rand($start->getTimestamp(), $end->getTimestamp());
		$randomDate = $randomDate->setTimestamp($randomSecond);

		return new Student(array(
			"firstName" => self::randomWord(),
			'lastName' => self::randomWord(),
			"birthdate" => $randomDate
		));
	}

	/**
	 * @Route("/")
	 * @Method({"GET"})
	 * @Template()
	 */
    public function indexAction()
    {
    	return array("items" => Student::findAll());
    }

    /**
     * @Route("/add")
     * @Method({"GET"})
     * @Template("SkolaAdminBundle:Student:edit.html.twig")
     */
    public function addAction()
    {
    	$form = $this->createForm (new StudentType ());

    	return array(
    		"form" => $form->createView()
    	);
    }

    /**
     * @Route("/addRandom")
     * @Method({"GET"})
     */
    public function addRandomAction() {
		$student = self::generateRandomStudent();
        $student->persist();

        return $this->redirect ("/student");
    }

    /**
     * @Route("/edit/{uri}")
     * @Method({"GET"})
     * @Template()
     */
    public function editAction($uri) 
    {
        try {
        	$items = Student::find ($uri);
        	$form = $this->createForm (new StudentType (), $items);

            $form->get('URI')->setData($uri);

        	return array (
        		"form" => $form->createView()
        	);
        } catch (\Exception $e) {
            return $this->render ('SkolaAdminBundle:Student:index.html.twig', array(
                "error" => $e->getMessage(),
                "items" => Student::findAll()            
            ));
        }
    }

    /**
     * @Route("/save")
     * @Method({"POST"})
     */
    public function saveAction (Request $request) 
    {
        try {
        	$item = null;
            $formType = new StudentType();

        	$post = $request->get ($formType->getName());
            $uri = $post['URI'];

        	if ($uri)
    	    	$item = Student::find ($uri);

        	$form = $this->createForm ($formType, $item);
        	$form->bind ($request);

        	$modelData = $form->getData();
        	if ($item) {
        		$modelArray = $modelData->toArray();	
        		$modelArray['URI'] = $uri;
        		$item = StudentArrayConverter::fromArray ($modelArray);
        	} else
        		$item = $modelData;

        	if ($form->isValid()) {
        		$item->persist();
        		return $this->redirect ('/student');
        	}

            throw new \Exception ("Invalid values in form.");
        } catch (\Exception $e) {
        	return $this->render ('SkolaAdminBundle:Student:edit.html.twig', array(
                "form" => $form->createView(),
                "error" => $e->getMessage(),
        	));
        }
    }

    /**
     * @Route("/delete")
     * @Method({"POST"})
     */
    public function deleteAction () 
    {
        try {
        	$uri = $this->getRequest()->get('URI');
            $item = Student::find ($uri);

       		$item->delete();

        	return $this->redirect ('/student');
        } catch (\Exception $e) {
            return $this->render ('SkolaAdminBundle:Student:index.html.twig', array(
                "error" => $e->getMessage(),
                "items" => Student::findAll()
            ));
        }
    }
}
