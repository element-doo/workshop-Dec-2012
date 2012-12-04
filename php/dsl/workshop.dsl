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

  report Demographic
  {
    Person[] minors 'it => it.birthdate.AddYears(18) >= DateTime.Today' order by birthdate;

    templater createXlsx 'People.xlsx';
    templater createPdf 'People.xlsx' pdf;
  }
}
