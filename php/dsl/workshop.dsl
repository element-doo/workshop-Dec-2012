module Workshop
{
  root Person
  {
    string firstName;
    string lastName;

    calculated string name from 'it => it.firstName + " " + it.lastName';

    date birthdate;
  }
}
