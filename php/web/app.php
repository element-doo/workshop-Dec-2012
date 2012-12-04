<?php
<<<<<<< HEAD

use Symfony\Component\HttpFoundation\Request;

define('NGS_DSL_PATH', __DIR__.'/../../DSL');
if (getenv ("DEBUG")) 
	define ('NGS_DSL_PROJECT_INI_PATH', '../platform/project-debug.ini');

require_once(__DIR__.'/../platform/Bootstrap.php');

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array(
        '127.0.0.1',
        '::1',
    ))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

if (getenv ("DEBUG")) 
	$kernel = new AppKernel('dev', true);
else
	$kernel = new AppKernel('prod', true);

$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
=======
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

>>>>>>> step1
