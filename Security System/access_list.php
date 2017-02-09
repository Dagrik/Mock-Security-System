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
<html>
<head>
    <meta charset="utf-8" />
    <title>Access History</title>
</head>
<body>
<h3>Door Access History</h3>

<!-- Navigation Menu -->
<table border="1">
    <legend>Navigation Menu</legend>
    <tr>
    	<td> <a href=home.php><button>Home Page</button></a> </td>
        <td> <a href=employee.php><button>Employee Data</button></a> </td>
        <td> <a href=permissions.php><button>Permissions List</button></a> </td>
    </tr>
</table>

<!--Access History Organization Option form-->
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Organize By</legend>
		<span>Name:</span>
		<select name="Name">
			<option></option>
			<?php 
			if(!($stmt = $mysqli->prepare("SELECT CONCAT(fname, ' ', lname) AS wname, id FROM employee"
			))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!$stmt->execute()){
				echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}
			if(!$stmt->bind_result($wname, $val)){
				echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}

			while($stmt->fetch()){
			 echo "\n<option value=" . $val . ">" . $wname . "</option>";
			}
			$stmt->close();
			?>

			
		</select>
		<span>Door:</span>
		<select name="Door">
			<option></option>
			<?php 
			if(!($stmt = $mysqli->prepare("SELECT doorName, id FROM doors"
			))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!$stmt->execute()){
				echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}
			if(!$stmt->bind_result($door, $val)){
				echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}

			while($stmt->fetch()){
			 echo "\n<option value=" . $val . ">" . $door . "</option>";
			}
			$stmt->close();
			?>
		</select>
		<label>Date:</label><input type="date" name="Date" placeholder = "YYYY-MM-DD" ><br>

		<input type="submit" /> 

	</fieldset>
  
</form>

<!--Access History Table-->
<table border=1 width=100%>
	<thead>
				<tr>
		<th width=25%>Employee Name</th>
		<th width=25%>Door</th>
		<th width=25%>TimeStamp</th>
		</tr>
	</thead>
	<tbody align="center">

		<?php

		//POST conditions: http://stackoverflow.com/questions/3496971/check-if-post-exists
		//Show specific history for a door
		if(isset($_POST['Door']) && !empty($_POST['Door'])){
			if(!($stmt = $mysqli->prepare("SELECT CONCAT(fname, ' ', lname) AS wname, doorName, access_time FROM employee
			INNER JOIN access_list ON employee.id = access_list.eid
			INNER JOIN doors ON access_list.did = doors.id 
			WHERE doors.id = ?
			ORDER BY access_time DESC"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!($stmt->bind_param("i",$_POST['Door']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}
		//Show all history for specific employee
		} elseif(isset($_POST['Name']) && !empty($_POST['Name'])){
			if(!($stmt = $mysqli->prepare("SELECT CONCAT(fname, ' ', lname) AS wname, doorName, access_time FROM employee
			INNER JOIN access_list ON employee.id = access_list.eid
			INNER JOIN doors ON access_list.did = doors.id 
			WHERE employee.id = ?
			ORDER BY access_time DESC"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!($stmt->bind_param("i",$_POST['Name']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}

		//Search by date
		} elseif(isset($_POST['Date']) && !empty($_POST['Date'])){   //WHERE conditions: http://stackoverflow.com/questions/2758486/mysql-compare-date-string-with-string-from-datetime-field
			$newdate = date_format(new DateTime($_POST['Date']), "Y-m-d");
			if(!($stmt = $mysqli->prepare("SELECT CONCAT(fname, ' ', lname) AS wname, doorName, access_time FROM employee
			INNER JOIN access_list ON employee.id = access_list.eid
			INNER JOIN doors ON access_list.did = doors.id 
			WHERE (access_time >= ?) AND (access_time<(? + INTERVAL 1 DAY))
			ORDER BY access_time DESC"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!($stmt->bind_param("ss",$newdate, $newdate))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}


		}else{  //Show all access history
			if(!($stmt = $mysqli->prepare("SELECT CONCAT(fname, ' ', lname) AS wname, doorName, access_time FROM employee
			INNER JOIN access_list ON employee.id = access_list.eid
			INNER JOIN doors ON access_list.did = doors.id ORDER BY access_time DESC"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
		}
			


		if(!$stmt->execute()){
				echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}
			if(!$stmt->bind_result($wname, $doorName, $access_time)){
				echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}
			while($stmt->fetch()){
			 echo "<tr>\n<td>\n" . $wname . "\n</td>\n<td>\n" . $doorName . "\n</td>\n<td>\n" . $access_time . "\n</td>\n</tr>";
			}
			$stmt->close();
		?>
	
		
		
	</tbody>
</table>

</body>
</html>