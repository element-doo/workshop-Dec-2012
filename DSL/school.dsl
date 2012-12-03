module School
{
  root Student
  {
    string firstName;
    string lastName;

    calculated string name from 'it => it.firstName + " " + it.lastName';
    date birthdate;

    specification getShortPeople 'it => it.name.Length < nameLimit' 
    {
    	int nameLimit;
    }
  }

  report Demographic
  {
    Student[] minors 'it => it.birthdate.AddYears(18) >= DateTime.Today' order by birthdate;
    templater createPdf 'People.xlsx' pdf;
  }
}
