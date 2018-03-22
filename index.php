<?php
$servername = "localhost";
$username = "narut";
$password = "1234";
$dbname = "dreamhomedb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT propertyno FROM propertyforrent"); 
    $stmt->execute();
    $propertyno = $stmt->fetchAll();

    $cityQuery = $conn->prepare("SELECT DISTINCT city FROM branch");
    $cityQuery->execute();
    $city = $cityQuery->fetchAll();
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
    <div class="container">
        <form method="post" id="form" action="index.php">
            <h1> Web application lab 5 </h1><hr>
            <h2> Property </h2>
            <select name="propertySelected">
                <?php foreach($propertyno as $property) {
                    echo '<option value=' . $property["propertyno"] . '>' . $property["propertyno"] . '</option>';
                } ?>
            </select>
            <h2> City </h2>
            <select name="citySelected">
                <?php foreach($city as $cty) {
                    echo '<option>' . $cty["city"] . '</option>';
                } ?>
            </select>

            <button type="submit">Submit</button>
        </form>
        <?php $breakline = '<br>' . '</br>'?>
        <?php $clientLabel = '<b>' . 'Client number: ' . '</b>'?>
        <?php $viewDateLabel = '<b>' . 'View date: ' . '</b>'?>
        <?php $staffNameLabel = '<br>' . '<b>' . 'Staff name: ' . '</b>'?>
        <?php
            if (isset($_POST['propertySelected'])) {
                $propertyResult = $_POST['propertySelected'];
                $clientQuery = $conn->prepare("SELECT clientno,viewdate FROM viewing WHERE propertyno=?");
                $clientQuery->execute(array($propertyResult));
                $clientno = $clientQuery->fetchAll();

                $staffQuery = $conn->prepare("SELECT fname,lname FROM Staff INNER JOIN PropertyForRent as P ON Staff.staffno = P.staffno
                WHERE propertyno=?");
                $staffQuery->execute(array($propertyResult));
                $staffName = $staffQuery->fetchAll();

                $clientNameQuery = $conn->prepare("SELECT fname,lname FROM Client WHERE clientno=?");
                $clientFNameArr = [];
                $clientLNameArr = [];
                foreach($clientno as $client) {
                    $clientNameQuery->execute(array($client[0]));
                    $clientName = $clientNameQuery->fetchAll();
                    
                    //client name
                    foreach($clientName as $cn) {
                        $clientFNameArr = $cn[0];
                        $clientLNameArr = $cn[1];
                        echo $clientFNameArr;
                        echo $clientLNameArr;
                    };
                }
                
                $clientArr = [];
                $viewdateArr = [];
                $staffFNameArr = [];
                $staffLNameArr = [];
                //staff name
                foreach($staffName as $staff) {
                    $staffFNameArr = $staff[0];
                    $staffLNameArr = $staff[1];
                    echo $staffNameLabel;
                    echo $staffFNameArr . ' ';
                    echo $staffLNameArr;
                };

                //client no
                foreach($clientno as $client) {
                    $clientArr = $client[0] . ' | ';
                    $viewdateArr = $client[1];
                    echo $breakline;
                    echo $clientLabel;
                    echo $clientArr;
                    echo $viewDateLabel;
                    echo $viewdateArr;
                };

            }
        ?>
    </div>
</body>
<style>
    .container {
        margin: 50px;
    }
</style>
</html>
