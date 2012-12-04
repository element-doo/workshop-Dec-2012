module Workshop
{
  root Person
  {
    string firstName;
    string lastName;

    calculated string name from 'it => it.firstName + " " + it.lastName';
    specification getShortPeople 'it => it.name.Length < nameLimit'
    {
      int nameLimit;
    }

    date birthdate;
  }
}
