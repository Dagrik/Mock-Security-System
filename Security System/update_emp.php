<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","searsjo-db","kAitSE0W4n7FUV6N","searsjo-db");
if($mysqli->connect_errno){
	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}
?>

<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>
    <h3>Update Employee Data</h3>

    <form method="post" action="emp_done.php">
	<fieldset>
	<span>First Name:</span>	
		
	
<?php
	//Query for fname, lname, job, and dept from employee
	//Display fname, lname, and job inside input boxes
	if(!($stmt = $mysqli->prepare("SELECT fname, lname, job, dept FROM employee INNER JOIN department ON employee.dept = department.id WHERE employee.id = ?"
	))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}

	if(!($stmt->bind_param("i",$_POST['id']))){
		echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
	}
	if(!$stmt->execute()){
		echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}

	if(!$stmt->bind_result($fname, $lname, $job, $dept )){
		echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}

	while($stmt->fetch()){
	 echo "<input name= 'fname' type='text' value=" . $fname . "></input>\n<span>Last Name:</span><input type='text' name='lname' value=" . $lname . "></input>\n<span>Job:</span><input name='job' type='text' value=" . $job . "></input>\n";
	}
	$stmt->close();
?>

<span>Department:</span>
		<select name="dept">
			
<?php 
//Display Department as dropdown list with assigned one pre-selected
if(!($stmt = $mysqli->prepare("SELECT depName, id FROM department"
))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
if(!$stmt->bind_result($depName, $val)){
	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

while($stmt->fetch()){
 echo "\n<option ";
 if($val == $dept) {
 	echo "selected = 'selected'";}
echo " value=" . $val . ">" . $depName . "</option>";
}
$stmt->close();
echo "</select>\n";

?>

<?php 

//List all access levels assigned to employee
if(!($stmt = $mysqli->prepare("SELECT al.id FROM access_level al
    INNER JOIN department ON al.deptID = department.id
    INNER JOIN emp_access ON al.id=emp_access.AID
    WHERE emp_access.eid = ?"
))){
    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!($stmt->bind_param("i",$_POST['id']))){
		echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
	}

if(!$stmt->execute()){
    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
if(!$stmt->bind_result($alid)){
    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
//Store values in array
while($stmt->fetch()){
 $accessArray[]=$alid;
}

$stmt->close();
?>

<p>
<?php 
//Display access levels as check boxes with assigned ones pre-checked
if(!($stmt = $mysqli->prepare("SELECT access_level.id, depName FROM access_level INNER JOIN department ON access_level.deptID = department.id"
))){
	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!$stmt->execute()){
	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
if(!$stmt->bind_result($alid2, $depName)){
	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
//Check value against array, if match, make prechecked
while($stmt->fetch()){
 echo "\n<input type='checkbox' name='alid[]' ";
 foreach($accessArray as $value){
 	if($value == $alid2){
 		echo "checked = 'checked' ";
 	}
 }
 echo "value=" . $alid2 . ">" . $depName . "</input>";
}

$stmt->close();
echo "<button type='submit' name='id' value=" . $_POST['id'] . ">Update</button>";
?>
</p>
</fieldset>
</form>
</body>
</html>