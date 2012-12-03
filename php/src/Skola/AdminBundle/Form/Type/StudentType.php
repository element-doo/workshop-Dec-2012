<?php
namespace Skola\AdminBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StudentType extends FormType {
	public function buildForm (FormBuilderInterface $builder, array $options) {
		$builder
			->add ('URI', 'hidden', array(
				'property_path' => false,
			))
			->add ('firstName', 'text', array(
				'label' => "Ime:"))
			->add ('lastName', 'text', array(
				'label' => "Prezime:"))
			->add ('birthdate', 'ngs_localdate', array(
				"format" => 'd.m.Y.',
				"label" => "Datum rođenja:"
			));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'compound'   => true,
            'data_class' => 'School\\Student',
        ));
    }

	public function getParent() {
		return 'form';
	}
}
