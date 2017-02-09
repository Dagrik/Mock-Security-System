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

<?php  //Delete specific employee from table
	if(!($stmt = $mysqli->prepare("DELETE FROM employee WHERE id = ?"))) {
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
	}
	if(!($stmt->bind_param("i",$_POST['emp']))){
		echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
	}
	if(!$stmt->execute()){
		echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
	} else {
		echo "<p>Deleted " . $stmt->affected_rows . " row from employee.</p>";
		echo "<p>All corresponding records for the employee have also been deleted.</p>";
	}
	$stmt->close();


	?>

	<form action="home.php">
		<Input Type="submit" value="Home Page" />
	</form>
</body>
</html>


