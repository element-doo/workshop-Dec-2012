<?php
require_once __DIR__.'/../platform/Bootstrap.php';

use Workshop\Person;

$marko = new Person(array(
	'firstName' => 'Tester',
	'lastName' => 'Bester',
	'birthdate' => '1987-6-5'
));

$marko->persist();

$everyone = Person::findAll();

foreach($everyone as $person) {
	echo 'My name is ', $person->name, ' and was born on ', $person->birthdate, '<br />';
}

$shortNamed = Person::getShortPeople();

echo '<h1>Short ppl</h1>';
foreach($shortNamed as $person) {
	echo 'My name is ', $person->name, ' and I\'m a short-named person!<br />';
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

// ------------------------------------------

?>

