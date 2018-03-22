<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
        
//Load Composer's autoloader
require 'vendor/autoload.php';
$servername = "localhost";
$username = "narut";
$password = "1234";
$dbname = "dreamhomedb";
$address = $_POST["email"];

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
        <?php $clientNameLabel = '<br>' . '<br>' . '<b>' . 'Client name: ' . '</b>'?>
        <?php
        if (isset($_POST["sendMail"]) AND isset($_POST["email"])) {
            $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
            try {
                //Server settings
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = '	smtp.sendgrid.net';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'apikey';                 // SMTP username
                $mail->Password = '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';                           // SMTP password
                $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 587;                                    // TCP port to connect to
            
                //Recipients
                $mail->setFrom('narut.p@ku.th', 'Narut Poovorakit');
                $mail->addAddress($address);
                
                //Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Dream Home';
                $mail->Body    = 'This is the message from dream home</b>';
            
                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            }
        }
            if (isset($_POST['propertySelected'])) {
                $propertyResult = $_POST['propertySelected'];
                $clientQuery = $conn->prepare("SELECT clientno,viewdate FROM viewing WHERE propertyno=?");
                $clientQuery->execute(array($propertyResult));
                $clientno = $clientQuery->fetchAll();

                $staffQuery = $conn->prepare("SELECT fname,lname FROM Staff INNER JOIN PropertyForRent as P ON Staff.staffno = P.staffno
                WHERE propertyno=?");
                $staffQuery->execute(array($propertyResult));
                $staffName = $staffQuery->fetchAll();

                //staff name
                $staffFNameArr = [];
                $staffLNameArr = [];
                foreach($staffName as $staff) {
                    $staffFNameArr = $staff[0];
                    $staffLNameArr = $staff[1];
                    echo $staffNameLabel;
                    echo $staffFNameArr . ' ';
                    echo $staffLNameArr;
                };

                //client no
                if (sizeof($clientno) == 0) {
                    echo '<form method="post" id="form" action="index.php">
                    <div>
                        Enter your email: <input type="text" name="email"><br>
                    </div>
                    <button type="submit" name="sendMail">Send mail</button>
                    </form>';
                }
                $clientArr = [];
                $viewdateArr = [];
                foreach($clientno as $client) {
                    $clientArr = $client[0] . ' | ';
                    $viewdateArr = $client[1];
                    echo $breakline;
                    echo $clientLabel;
                    echo $clientArr;
                    echo $viewDateLabel;
                    echo $viewdateArr;
                };

                //client name
                $clientNameQuery = $conn->prepare("SELECT fname,lname FROM Client WHERE clientno=?");
                $clientFNameArr = [];
                $clientLNameArr = [];
                echo $clientNameLabel;
                foreach($clientno as $client) {
                    $clientNameQuery->execute(array($client[0]));
                    $clientName = $clientNameQuery->fetchAll();
                    
                    foreach($clientName as $cn) {
                        $clientFNameArr = $cn[0];
                        $clientLNameArr = $cn[1];
                        echo $clientFNameArr;
                        echo $clientLNameArr . '<br>';
                    };
                }

                $result = 'hello eiei';
            }
        ?>
        <button style="background-color: green; color: white" class="button primary" onClick="print()">Export to PDF</button>
    </div>
</body>
<style>
    .container {
        margin: 50px;
    }
</style>
</html>
