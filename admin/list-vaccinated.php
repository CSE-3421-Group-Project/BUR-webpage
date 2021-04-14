<!DOCTYPE html>
<html>
    <head>
        <title>List vaccinated patients</title>
        <meta charset="UTF-8" />
        <link href="main_styles.css" type="text/css" rel="stylesheet" />
    <style>
        table {
            cell-spacing: 20px;
            border-collapse: collapse; 
        }   
        td, th {
            text-align: center;
            padding: 5px;
            border: 1px solid black;
        } 
    </style>  
    </head>
    <body>
        <div class="main-content">
            <a href="../admin.php">Go back</a>
            <h2>Vaccinated patients</h2>

            <table>
                <tr>
                    <th>Name</th>
                    <th>SSN</th>
                    <th>Date vaccinated</th>
                    <th>Dose manufacturer</th>
                </tr>

<?php
function connectToDatabase() {
    //TODO: Is this database name correct?
    $conn = new mysqli("localhost", "bur", "bur", "BUR_webpage");
    if($conn->connect_error) die($conn->connect_error);
    if($conn === false) die("Error: Could not connect".mysqli_connect_error());
    return $conn;

}

function listVaccinated($db){
    $sql = <<<EOD
        select p.name as name, p.Ssn as ssn, a.Date as date, b.Manufacturer as manufacturer
        from PATIENT as p, APPOINTMENT as a, DOSE as d, BATCH as b
        where p.Ssn=a.P_Ssn
        and a.Tracking_no=d.Tracking_no
        and d.Batch_no=b.Batch_no
        and d.Status='used'
        order by p.name
EOD;
    $result = $db->query($sql);
    while ($patient = $result->fetch_array()){
        $name = $patient['name'];
        $ssn = $patient['ssn'];
        $date = $patient['date'];
        $manufacturer = $patient['manufacturer'];
        echo "<tr>";
        echo "<td>".$name."</td>";
        echo "<td>".$ssn."</td>";
        echo "<td>".$date."</td>";
        echo "<td>".$manufacturer."</td>";
        echo "</tr>";
    }
}

$db = connectToDatabase();
listVaccinated($db);
?>
    </table>
    </div>
    </body>
    </html>
