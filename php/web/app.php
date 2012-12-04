<?php
require_once __DIR__.'/../platform/Bootstrap.php';

use Workshop\Person;

$tester = new Person(array(
    'firstName' => 'Tester',
    'lastName' => 'Bester',
    'birthdate' => '1987-6-5'
));

$tester->persist();

$everyone = Person::findAll();

foreach($everyone as $person) {
    echo 'My name is ', $person->firstName, ' and was born on ', $person->birthdate, '<br />';
}
?>

