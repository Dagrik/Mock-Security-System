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
	//Obtain all access permission for a specific employee and store into an array
	if(!($stmt = $mysqli->prepare("SELECT AID, CONCAT(fname, ' ', lname) as wname from emp_access INNER JOIN employee ON emp_access.EID = employee.id WHERE EID=?"
	))){
	    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}

	if(!($stmt->bind_param("i",$_POST['empID']))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
		echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}
	if(!$stmt->bind_result($aid, $name)){
	    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}

	while($stmt->fetch()){
	 	//Store values into array
	 	$aList[]=$aid;
	 	
	}

	$stmt->close();
	?>

	<?php
	//Obtain requirement access for specific door
	if(!($stmt = $mysqli->prepare("SELECT reqAccess, doorName from doors WHERE id=?"
	))){
	    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}

	if(!($stmt->bind_param("i",$_POST['doorID']))){
			echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
		}

	if(!$stmt->execute()){
		echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}

	if(!$stmt->bind_result($areq, $dname)){
	    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
	}


	while($stmt->fetch()){

	}
	$stmt->close();

	?>

	<?php 
	$check = 0; 
	//Access each value in array
	foreach($aList as $val){  
		if($val == $areq){  //If door requirement matches an employee access permission
			$check = 1;
			if(!($stmt = $mysqli->prepare("INSERT INTO access_list(EID, DID, access_time) VALUES (?, ?, NOW())"
			))){
	    		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!($stmt->bind_param("ii",$_POST['empID'], $_POST['doorID']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
	    		echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}
			$stmt->close();
			echo "<h3>" . $name . " successfully entered " . $dname . ".</h3>";	
		} 
	}
	if($check == 0){
		echo "<h3> ACCESS DENIED </h3>";
	}
	?>

<form method="POST" action="home.php">
    <fieldset>
    	<input type="submit" value="Return to Home" />
    </fieldset>
</form>
</body>
</html>