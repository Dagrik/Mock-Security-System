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
	if (!empty($_POST['fname']) && !empty($_POST['lname']) && !empty($_POST['job']) && !empty($_POST['alid'])) { //if there were no blanks

		//Update employee name, job, and department
		if(!($stmt = $mysqli->prepare("UPDATE employee SET fname=?, lname=?, job=?, dept=? WHERE employee.id=?"
		))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!($stmt->bind_param("sssii",$_POST['fname'],$_POST['lname'],$_POST['job'],$_POST['dept'],$_POST['id']))){
		echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		
		$stmt->close();

		echo "<h3>Employee Update Successful</h3>";
		
		//Delete all access permissions for employee
	    if(!($stmt = $mysqli->prepare("DELETE FROM emp_access WHERE EID=?"
			))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

		if(!($stmt->bind_param("i", $_POST['id']))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
			
		$stmt->close();

		//Add access permissions to file for updated employee
		if(!($stmt = $mysqli->prepare("INSERT INTO emp_access(EID, AID) VALUES (?,
		?)"))) {
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!($stmt->bind_param("ii",$_POST['id'], $val))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}

		foreach($_POST['alid'] as $val){
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			}
		}
		$stmt->close();
	}
	else { //if anything was left blank
		echo "<p>Update Employee Failed:  Please enter data for all fields.</p>";
	}

		
?>
    <form method="POST" action="home.php">
    <fieldset>
    <input type="submit" value="Return to Home" />
    </fieldset>
    </form>
    </body>
    </html>