<?php
require_once __DIR__.'/../platform/Bootstrap.php';
require_once __DIR__.'/helpers.php';

use Workshop\Person;

$marko = new Person(array(
	'firstName' => 'Tester',
	'lastName' => 'Bester',
	'birthdate' => '1987-6-5'
));

// $marko->persist();

for($i = 0; $i < 10; $i++) {
  $p = generateRandomPerson();
  $p->persist();
}

$everyone = Person::findAll();

foreach($everyone as $person) {
	echo 'My name is ', $person->name, ' and was born on ', $person->birthdate, '<br />';
}
?>

