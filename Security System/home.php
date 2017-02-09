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
    <!--Script to all multiple boxes to get checked-->
    <script language="JavaScript">
        function toggle(source) {
            checkboxes = document.getElementsByName('dept[]');
            for(var i=0, n=checkboxes.length; i<n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</head>
<body>
    <h3>Weyland-Yutani Security Database</h3>

    <table id="summaryTable">
        <tr>
			<td>Security Summary</td>
		</tr>
        <?php
        //Show employee, dept, and door count
        if(!($stmt = $mysqli->prepare("SELECT 
        		(SELECT count(employee.id) FROM employee) as empCount,
        		(SELECT count(department.id) FROM department) as depCount,
        		(SELECT count(doors.id) FROM doors) as doorCount"))){
        	echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
        }
        if(!$stmt->execute()){
        	echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
        }
        if(!$stmt->bind_result($empCount, $depCount, $doorCount)){
        	echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
        }
        while($stmt->fetch()){
        	echo "<tr>\n<td>\nEmployee Count:\n</td>\n<td>\n" . $empCount . "\n</td>\n</tr>";
        	echo "<tr>\n<td>\nDepartment Count:\n</td>\n<td>\n" . $depCount . "\n</td>\n</tr>";
        	echo "<tr>\n<td>\nDoor Count:\n</td>\n<td>\n" . $doorCount . "\n</td>\n</tr>";
        }

        $stmt->close();
        ?>            
    </table>

    <p></p>
<!--Navigation Menu -->
    <table border="1">
        <legend>Navigation Menu</legend>
        <tr>
            <td> <a href=employee.php><button>Employee Data</button></a> </td>
            <td> <a href=permissions.php><button>Permissions List</button></a> </td>
            <td> <a href=access_list.php><button>Access History</button></a> </td>
        </tr>
    </table>

    <p></p>
<!--Add Department -->
    <form method="post" action="Add_Dept.php"> 
        <fieldset>
            <legend>Add Department</legend>
            <div>Dept. Name: <input type ="text" name="depName" /></div>
            <p><input type="submit" /></p>
        </fieldset>
    </form>

    <p></p>


<!--Add Door -->
<form method="post" action="Add_Door.php">
        <fieldset>
            <legend>Add Door</legend>
            <div>Door Name: <input type ="text" name="doorName" /></div>
            <div>Access Requirement: 
                <select name="depName"> 
                    <?php 
                    if(!($stmt = $mysqli->prepare("SELECT al.id, depName FROM access_level al
                        INNER JOIN department ON al.deptID = department.id"
                    ))){
                        echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                    }

                    if(!$stmt->execute()){
                        echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                    }
                    if(!$stmt->bind_result($id, $name)){
                        echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                    }

                    while($stmt->fetch()){
                     echo "\n<option value=" . $id . ">" . $name . "</option>";
                    }
                    $stmt->close();
                    ?>
                </select>
            </div>
            <p><input type="submit" /></p>
        </fieldset>
    </form>

    <p></p>


<!--Add Employee -->
    <form method="post" action="Add_Emp.php"> 
        <fieldset>
            <legend>Add Employee</legend>
            <div>First Name: <input type ="text" name="fname" /></div>
            <div>Last Name: <input type="text" name="lname" /></div>
            <div>Job:  <input type="text" name="job" /></div>
            <div>Department: 
                <select name="depName"> 
                    <?php 
                    if(!($stmt = $mysqli->prepare("SELECT id, depName FROM department"
                    ))){
                        echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                    }

                    if(!$stmt->execute()){
                        echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                    }
                    if(!$stmt->bind_result($id, $name)){
                        echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                    }

                    while($stmt->fetch()){
                     echo "\n<option value=" . $id . ">" . $name . "</option>";
                    }
                    $stmt->close();
                    ?>
                </select>
            </div>
            <p></p>

                <div>Access:</div> 
                <div>  <!--  Code from http://stackoverflow.com/questions/386281 -->
                    <label for="checkAll">Select All</label>
                    <input type="checkbox" onClick="toggle(this)" name="checkAll" />
                </div>
                <?php 
                if(!($stmt = $mysqli->prepare("SELECT al.id, depName FROM access_level al
                    INNER JOIN department ON al.deptID = department.id"
                ))){
                    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                }

                if(!$stmt->execute()){
                    echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }
                if(!$stmt->bind_result($id, $name)){
                    echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                }

                while($stmt->fetch()){
                 echo "<div><label for='dept[]'>" . $name . "</label><input type='checkbox' name='dept[]' value=" . $id . " /></div>";
                }
                $stmt->close();
                ?>

            <p><input type="submit" /></p>
        </fieldset>
    </form>

    <p></p>


<!--Delete Employee -->
    <form method="post" action="Del_Emp.php"> 
        <fieldset>
            <legend>Delete Employee</legend>
            <div>Name: 
                <select name="emp"> 
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
            </div>       
            <p><input type="submit" /></p>
        </fieldset>
    </form>

    <p></p>


<!--Update Employee -->
    <form method="post" action="update_emp.php"> 
        <fieldset>
            <legend>Update Employee</legend> 
            <div>Name: 
                <select name="id"> 
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
            </div>       
            <p><input type="submit" /></p>
        </fieldset>
    </form>

    <p></p>

<!--Access Door -->
    <form method="post" action="doorentry.php"> 
        <fieldset>
            <legend>Access Door</legend> 
            <div>Name: 
                <select  name='empID'> 
                    
                    <?php //Display droplist of employee names
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
            </div>       
            <div>Door: 
                <select name='doorID'> 
                    <?php   //Display door options
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
            </div>
            <p><input type="submit" /></p>
        </fieldset>
    </form>

</body>
</html>