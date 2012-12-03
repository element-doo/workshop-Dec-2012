<?php
require_once __DIR__.'/../platform/Bootstrap.php';

use School\Student;


$marko = new Student(array(
	'firstName' => 'Tester',
	'lastName' => 'Bester',
	'birthdate' => '1980-1-1'
));

//$marko->persist();


// ------------------------------------------

function randomWord() {
	$randomWord = "";
	$n = rand(3,7);
	for($i = 0; $i < $n; $i++)
		$randomWord .= chr(rand(ord('a'), ord('z')));
	$randomWord = ucfirst($randomWord);

	return $randomWord;
}

function generateRandomPerson() {
	$randomDate = new DateTime();
	$start = new DateTime("1970-1-1");
	$end = new DateTime("2000-12-31");
	$randomSecond = rand($start->getTimestamp(), $end->getTimestamp());
	$randomDate = $randomDate->setTimestamp($randomSecond);

	return new Student(array(
		"firstName" => randomWord(),
		'lastName' => randomWord(),
		"birthdate" => $randomDate
	));
}

// ------------------------------------------

//generateRandomPerson()->persist();



$randomPersons = array();
for($i = 0; $i < 50; $i++)
	$randomPersons[] = generateRandomPerson();



use NGS\Client\StandardProxy;
$proxy = new StandardProxy();
//$proxy->insert($randomPersons);



$allPersons = Student::findAll();

/*
foreach($allPersons as $person) {
	echo 'Ja sam ', $person->name, ' i rođen sam ', $person->birthdate, "<br />";
}
*/
foreach (Student::getShortPeople (10) as $person)
	echo $person->name, "<br />";

use School\Demographic;
$demo = new Demographic();
$content = $demo->createPdf();

file_put_contents("minors.pdf", $content);

?>

