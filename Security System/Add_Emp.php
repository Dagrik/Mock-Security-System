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

<?php

	if (!empty($_POST['fname']) && !empty($_POST['lname']) && !empty($_POST['job']) && !empty($_POST['dept'])) { //if there were no blanks

		//Check that entered employee name is not already in table
		$x = False;
		if(!($stmt = $mysqli->prepare("SELECT fname, lname FROM employee"))) {
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($fname, $lname)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
			if($fname === $_POST['fname'] && $lname === $_POST['lname']) {
				$x = True;
			}
		}
		$stmt->close();
	
		if($x === False) {  //Add information into employee table
			if(!($stmt = $mysqli->prepare("INSERT INTO employee(fname, lname, job, dept) VALUES (?, ?, ?, ?)"))) {
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!($stmt->bind_param("ssss",$_POST['fname'], $_POST['lname'], $_POST['job'], $_POST['depName']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			} else {
				echo "<p>Added " . $stmt->affected_rows . " row to employee.</p>";
				$id = $mysqli->insert_id;
			}
			$stmt->close();

			//handling of checkbox data from http://stackoverflow.com/questions/20176673
		
			//Add access permissions to emp_access for the above new employee
			if(!($stmt = $mysqli->prepare("INSERT INTO emp_access(EID, AID) VALUES ((SELECT employee.id FROM employee WHERE employee.fname = ? AND employee.lname = ?),
			?)"))) {
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!($stmt->bind_param("ssi",$_POST['fname'], $_POST['lname'], $val))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}

			foreach($_POST['dept'] as $val){
				if(!$stmt->execute()){
					echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
				} else {
					echo "<p>Added " . $stmt->affected_rows . " row to emp_access.</p>";
				}
			}
			$stmt->close();
		}
		else {  //if Employee already exists in database
			echo "<p>Add Employee Failed:  Employee already exists.</p>"; 
		}

	}

	else { //if anything was left blank
		echo "<p>Add Employee Failed:  Please enter data for all fields.</p>";
	}

	?>

	<form action="home.php">
		<Input Type="submit" value="Home Page" />
	</form>
</body>
</html>


