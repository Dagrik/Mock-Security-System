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
	if (!empty($_POST['depName'])) { //if department was not left blank
		
		//Check that entered department name is not already in table
		$x = False;
		if(!($stmt = $mysqli->prepare("SELECT depName FROM department"))) {
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($name)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
			if($name === $_POST['depName']) {
				$x = True;
			}
		}
		$stmt->close();

		if($x === False) {
			//Create new department
			if(!($stmt = $mysqli->prepare("INSERT INTO department(depName) VALUES (?)"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!($stmt->bind_param("s",$_POST['depName']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			} else {
				echo "<p>Added " . $stmt->affected_rows . " rows to department.</p>";
			}
			$stmt->close();

			//Create access level to correspond to new department
			if(!($stmt = $mysqli->prepare("INSERT INTO access_level (deptID) VALUES ((SELECT id FROM department WHERE depName = ?))"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!($stmt->bind_param("s",$_POST['depName']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			} else {
				echo "<p>Added " . $stmt->affected_rows . " rows to access_level.</p>";
			}
			$stmt->close();
		}
		else {  //if depName already exists in database
			echo "<p>Add Department Failed:  Department already exists.</p>"; 
		}
	}
	else { //if department was left blank
		echo "<p>Add Department Failed:  Please specify a department name.</p>"; 
	}
	?>

	<form action="home.php">
		<Input Type="submit" value="Home Page" />
	</form>
</body>
</html>


