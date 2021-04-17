<!DOCTYPE html>
<html>
    <head>
        <title>List Waiting Patients</title>
        <meta charset="UTF-8" />
        <link href="../main_styles.css" type="text/css" rel="stylesheet" />
    </head>
    <body>
        <nav>
            <h1>BUR Admin Dashboard</h1>
        </nav>
        <div class="main-content">
            <a href="../admin.php">Go back</a>
            <h2>Waiting Patients</h2>

            <table>
                <tr>
                    <th>Name</th>
                    <th>Priority</th>
                    <th>SSN</th>
                </tr>

<?php
function connectToDatabase() {
    $conn = new mysqli("localhost", "bur", "bur", "BUR_webpage");
    if($conn->connect_error) die($conn->connect_error);
    if($conn === false) die("Error: Could not connect".mysqli_connect_error());
    return $conn;

}

function listWaiting($db){
    $sql = <<<EOD
        select p.name as name, p.Priority as priority, p.Ssn as ssn
        from PATIENT as p
        where p.Waitlist = true
        order by p.Priority
EOD;
    $result = $db->query($sql);
    while ($patient = $result->fetch_array()){
        $name = $patient['name'];
        $priority = $patient['priority'];
        $ssn = $patient['ssn'];
        echo "<tr>";
        echo "<td>".$name."</td>";
        echo "<td>".$priority."</td>";
        echo "<td>".$ssn."</td>";
        echo "</tr>";
    }
}

$db = connectToDatabase();
listWaiting($db);
?>
    </table>
    </div>
    </body>
    </html>