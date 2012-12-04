<?php
require_once __DIR__.'/../platform/Bootstrap.php';

use Workshop\Person;

$marko = new Person(array(
	'firstName' => 'Tester',
	'lastName' => 'Bester',
	'birthdate' => '1982-5-3'
));

$marko->persist();

$everyone = Person::findAll();

foreach($everyone as $person) {
	echo 'I am ', $person->firstName, ' and I\'m born in ', $person->birthdate, '<br />';
}

// ------------------------------------------

function randomWord() {
	$randomWord = '';
	$n = rand(3,7);
	for($i = 0; $i < $n; $i++)
		$randomWord .= chr(rand(ord('a'), ord('z')));
	$randomWord = ucfirst($randomWord);

	return $randomWord;
}

function generateRandomPerson() {
	$randomDate = new DateTime();
	$start = new DateTime('1970-1-1');
	$end = new DateTime('2000-12-31');
	$randomSecond = rand($start->getTimestamp(), $end->getTimestamp());
	$randomDate = $randomDate->setTimestamp($randomSecond);

	$p = new Person();
  $p->firstName = randomWord();
  $p->lastName = randomWord();
  $p->birthdate = $randomDate;
  return $p;
}

?>

