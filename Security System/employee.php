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
    <h3>Employee Information Lookup</h3>

    <!--Navigation Menu -->
    <table border="1">
        <legend>Navigation Menu</legend>
        <tr>
            <td> <a href=home.php><button>Home Page</button></a> </td>
            <td> <a href=permissions.php><button>Permissions List</button></a> </td>
            <td> <a href=access_list.php><button>Access History</button></a> </td>
        </tr>
    </table>


<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Search By</legend>
		<span>Name:</span>
		<select name="Name">
			<option></option>
			<?php 
			//Get employee names
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
				<span>Department:</span>
		<select name="depName">
			<option></option>
			<?php 
			//List of Departments
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
			 echo "\n<option value=" . $val . ">" . $depName . "</option>";
			}
			$stmt->close();
			?>
		</select>
		<input type="submit" />
		</fieldset>
		</form>

<!--List of Access Permission by Employee-->

<table border=1 width=100%>
	<thead>
				<tr>
		<th width=25%>Employee Name</th>
		<th width=25%>Dept</th>
		<th width=25%>Job</th>
		</tr>
	</thead>
		<tbody align="center">

			<?php
			//List employee data for those in certain dept
				if(isset($_POST['depName']) && !empty($_POST['depName'])){
					if(!($stmt = $mysqli->prepare("SELECT DISTINCT CONCAT( fname, ' ', lname ) AS wname, depName, job
					FROM employee INNER JOIN department ON employee.dept = department.id WHERE department.id = ?
					ORDER BY wname DESC"))){
						echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
					}

					if(!($stmt->bind_param("i",$_POST['depName']))){
						echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
					}
			//Show employee info of certain employee
				} elseif(isset($_POST['Name']) && !empty($_POST['Name'])){
					if(!($stmt = $mysqli->prepare("SELECT DISTINCT CONCAT( fname, ' ', lname ) AS wname, depName, job
					FROM employee INNER JOIN department ON employee.dept = department.id WHERE employee.id = ?
					ORDER BY wname DESC"))){
						echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
					}

					if(!($stmt->bind_param("i",$_POST['Name']))){
						echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
					}
				} else {
					//Show all employee information
					if(!($stmt = $mysqli->prepare("SELECT DISTINCT CONCAT( fname, ' ', lname ) AS wname, depName, job
					FROM employee INNER JOIN department ON employee.dept = department.id
					ORDER BY wname DESC"))){
						echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
					}
				}

				if(!$stmt->execute()){
					echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				if(!$stmt->bind_result($wname, $depName, $job)){
					echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
				}
				while($stmt->fetch()){
				 echo "<tr>\n<td>\n" . $wname . "\n</td>\n<td>\n" . $depName . "\n</td>\n<td>\n" . $job . "\n</td>\n</tr>";
				}
				$stmt->close();
			?>
		</tbody>
</table>

</body>
</html>