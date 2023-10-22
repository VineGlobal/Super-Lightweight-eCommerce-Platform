<?php include 'sendGrid.php';?>
<?php
 

$errorMSG = "";

// NAME
if (empty($_POST["name"])) {
    $errorMSG = "Name is required ";
} else {
    $name = $_POST["name"];
}

// EMAIL
if (empty($_POST["email"])) {
    $errorMSG .= "Email is required ";
} else {
    $email = $_POST["email"];
}

// MESSAGE
if (empty($_POST["message"])) {
    $errorMSG .= "Message is required ";
} else {
    $message = $_POST["message"];
}

if (empty($_POST["subject"])) {
    $errorMSG .= "subject is required ";
} else {
    $topic = $_POST["subject"];
}


$EmailTo = "mraygoza@landedcost.io";
$Subject = "***Landed Cost API Interest from Demo Store***";

// prepare email body text
$Body = "";
$Body .= "Name: ";
$Body .= $name;
$Body .= "\n";
$Body .= "Email: ";
$Body .= $email;
$Body .= "\n";
$Body .= "Message: ";
$Body .= $message;
$Body .= "\n";
$Body .= "Subject: ";
$Body .= $topic;
$Body .= "\n";

                                                       
$mail = new Mail();
 $mail->toEmail         = $EmailTo;
 $mail->toName          = 'Marc Raygoza (LandedCost.io)';
 $mail->fromEmail       = $email;
 $mail->text            = $Body;
 $mail->html            = $Body;
 $mail->subject         = $Subject;
 $success = $mail->send();

 


// redirect to success page
if ($success && $errorMSG == ""){
   echo "success";
}else{
    if($errorMSG == ""){
        echo "Something went wrong :(";
    } else {
        echo $errorMSG;
    }
}

?>
