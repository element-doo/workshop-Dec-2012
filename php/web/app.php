<?php
require_once __DIR__.'/../platform/Bootstrap.php';
require_once __DIR__.'/helpers.php';

use Workshop\Person;

$tester = new Person(array(
    'firstName' => 'Tester',
    'lastName' => 'Bester',
    'birthdate' => '1987-6-5'
));

// $tester->persist();

$randomPersons = array();
for($i = 0; $i < 50; $i++) {
    $randomPersons[] = generateRandomPerson();
}

use NGS\Client\StandardProxy;
$proxy = new StandardProxy();
// $proxy->insert($randomPersons);

$everyone = Person::findAll();

foreach($everyone as $person) {
    echo 'My name is ', $person->name, ' and was born on ', $person->birthdate, '<br />';
}

/*
$proxy->delete($everyone);

// Query again - all people should be gone
$everyone = Person::findAll();
*/

echo 'There are '.count($everyone).' people available.';

?>

