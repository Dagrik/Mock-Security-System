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
    <title>Permissions</title>
</head>
<body>
<h3>Security Permissions</h3>
<!--Navigation Menu -->
<table border="1">
    <legend>Navigation Menu</legend>
    <tr>
    	<td> <a href=home.php><button>Home Page</button></a> </td>
        <td> <a href=employee.php><button>Employee Data</button></a> </td>
        <td> <a href=access_list.php><button>Access History</button></a> </td>
    </tr>
</table>

<!-- Form to select sort conditions-->
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Organize By</legend>
		<span>Name:</span>
		<select name="Name">
			<option></option>
			<?php 
			//Display list of names of employees
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
			//Get department names for employees
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
		<span>Permissions:</span>
		<select name="perm">
			<option></option>
			<?php 
			//Obtain list of permissions
			if(!($stmt = $mysqli->prepare("SELECT depName FROM access_level INNER JOIN department ON access_level.deptID = department.id"
			))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!$stmt->execute()){
				echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}
			if(!$stmt->bind_result($depName)){
				echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}

			while($stmt->fetch()){
			 echo "\n<option value=" . $depName . ">" . $depName . "</option>";
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
		<th width=25%>Access Permissions</th>
		</tr>
	</thead>
	<tbody align="center">

		<?php

		//POST conditions: http://stackoverflow.com/questions/3496971/check-if-post-exists
		//Show list of employees belonging to certain dept
		if(isset($_POST['depName']) && !empty($_POST['depName'])){
			if(!($stmt = $mysqli->prepare("SELECT DISTINCT CONCAT(fname, ' ', lname) AS wname, depName, auths FROM employee
			INNER JOIN department ON employee.dept = department.id
			INNER JOIN (SELECT e2.id as 'ents', d2.depName AS 'auths' from employee e2
			INNER JOIN emp_access ON e2.id = emp_access.eid
			INNER JOIN access_level ON emp_access.aid = access_level.id
			INNER JOIN department d2 ON access_level.deptID = d2.id) as tbl
			WHERE employee.id = ents AND department.id = ?
			ORDER BY wname DESC"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!($stmt->bind_param("i",$_POST['depName']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}

		//List specific employee based on employee.id
		} elseif(isset($_POST['Name']) && !empty($_POST['Name'])){
			if(!($stmt = $mysqli->prepare("SELECT DISTINCT CONCAT(fname, ' ', lname) AS wname, depName, auths FROM employee
			INNER JOIN department ON employee.dept = department.id
			INNER JOIN (SELECT e2.id as 'ents', d2.depName AS 'auths' from employee e2
			INNER JOIN emp_access ON e2.id = emp_access.eid
			INNER JOIN access_level ON emp_access.aid = access_level.id
			INNER JOIN department d2 ON access_level.deptID = d2.id) as tbl
			WHERE employee.id = ents AND employee.id = ?
			ORDER BY wname DESC"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!($stmt->bind_param("i",$_POST['Name']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}

		//List all employees based off specific access level	
		} elseif(isset($_POST['perm']) && !empty($_POST['perm'])){
			if(!($stmt = $mysqli->prepare("SELECT DISTINCT CONCAT( fname, ' ', lname ) AS wname, depName, auths
			FROM employee
			INNER JOIN department ON employee.dept = department.id
			INNER JOIN (
				SELECT e2.id AS 'ents', d2.depName AS 'auths'
				FROM employee e2
				INNER JOIN emp_access ON e2.id = emp_access.eid
				INNER JOIN access_level ON emp_access.aid = access_level.id
				INNER JOIN department d2 ON access_level.deptID = d2.id
			) AS tbl
			WHERE employee.id = ents
			AND auths = ?"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!($stmt->bind_param("s",$_POST['perm']))){
				echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
			}

		//Show all employees and their access levels
		} else {
			if(!($stmt = $mysqli->prepare("SELECT DISTINCT CONCAT(fname, ' ', lname) AS wname, depName, auths FROM employee
			INNER JOIN department ON employee.dept = department.id
			INNER JOIN (SELECT e2.id as 'ents', d2.depName AS 'auths' from employee e2
			INNER JOIN emp_access ON e2.id = emp_access.eid
			INNER JOIN access_level ON emp_access.aid = access_level.id
			INNER JOIN department d2 ON access_level.deptID = d2.id) as tbl
			WHERE employee.id = ents
			ORDER BY wname DESC"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($wname, $depName, $auth)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($stmt->fetch()){
		 echo "<tr>\n<td>\n" . $wname . "\n</td>\n<td>\n" . $depName . "\n</td>\n<td>\n" . $auth . "\n</td>\n</tr>";
		}
		$stmt->close();
		?>
		
		
		
	</tbody>
</table>

</body>
</html>