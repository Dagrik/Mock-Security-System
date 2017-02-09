#Premade Entries
INSERT INTO department (`depName`) VALUES ("HR"), ("Finance"), ("Shipping"), ("Security");

INSERT INTO employee (`fname`, `lname`, `dept`, `job`) VALUES ("John", "Smith", "1", "Clerk"), ("Jerry", "Brockheimer", "2", "Secretary"), ("Trisha", "Hammerhands", "4", "Officer"), ("Tommy", "Hammerhands", "4", "Officer"), ("Luke", "Investor", "2", "Investments"), ("Gracie", "Softvoice", "1", "Relations"), ("Susan", "Lifter", "3", "ForkLift Operator");

INSERT INTO access_level (`deptID`) VALUES ("1"), ("2"), ("3"), ("4");

INSERT INTO doors (`doorName`, `reqAccess`) VALUES ("HR Entrance", "1"), ("Finance Entrance", "2"), ("Shipping Entrance", "3"),  ("Security Office", "4");

INSERT INTO emp_access (`EID`, `AID`) VALUES ("1", "1"), ("2", "2"), ("3", "4"), ("3", "3"), ("3", "2"), ("3", "1"), ("4", "4"), ("4", "3"), ("4", "2"), ("4", "1"), ("5", "2"), ("6", "1"), ("7", "3");
