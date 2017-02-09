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
	if (!empty($_POST['doorName'])) { //if department was not left blank
		
		//Check that entered door name is not already in table
		$x = False;
		if(!($stmt = $mysqli->prepare("SELECT doorName FROM doors"))) {
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($name)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
			if($name === $_POST['doorName']) {
				$x = True;
			}
		}
		$stmt->close();

		if($x === False) {
			if(!($stmt = $mysqli->prepare("INSERT INTO doors(doorName, reqAccess) VALUES (?, 
				(SELECT al.id FROM access_level al INNER JOIN
				department d ON al.deptID = d.id WHERE
				d.id = ?))"))) {
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!($stmt->bind_param("ss",$_POST['doorName'], $_POST['depName']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			} else {
				echo "<p>Added " . $stmt->affected_rows . " rows to doors.</p>";
			}
			$stmt->close();

		}
		else {  //if doorName already exists in database
			echo "<p>Add Door Failed:  Door already exists.</p>"; 
		}
	}
	else { //if door was left blank
		echo "<p>Add Door Failed:  Please specify a door name.</p>"; 
	}

	?>

	<form action="home.php">
		<Input Type="submit" value="Home Page" />
	</form>
</body>
</html>


