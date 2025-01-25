<?php

ini_set('display_errors', 1);  // Enable error display
error_reporting(E_ALL);  // Show all types of errors

echo "PHP script is running";

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "Starting PHPMailer setup"; 

include("login.php"); 
if($_SESSION['name']==''){
	header("location: signin.php");
}
// include("login.php"); 
$emailid= $_SESSION['email'];
$connection=mysqli_connect("localhost","root","Ram1234*");
$db=mysqli_select_db($connection,'fooddonation');
if(isset($_POST['submit']))
{
    $foodname=mysqli_real_escape_string($connection, $_POST['foodname']);
    $meal=mysqli_real_escape_string($connection, $_POST['meal']);
    $category=$_POST['image-choice'];
    $quantity=mysqli_real_escape_string($connection, $_POST['quantity']);
    // $email=$_POST['email'];
    $phoneno=mysqli_real_escape_string($connection, $_POST['phoneno']);
    $district=mysqli_real_escape_string($connection, $_POST['district']);
    $address=mysqli_real_escape_string($connection, $_POST['address']);
    $name=mysqli_real_escape_string($connection, $_POST['name']);
    $expirydate=mysqli_real_escape_string($connection, $_POST['expirydate']);
  

 



    $query="insert into food_donations(email,food,type,category,phoneno,location,address,name,quantity,expiry) values('$emailid','$foodname','$meal','$category','$phoneno','$district','$address','$name','$quantity','$expirydate')";
    $query_run= mysqli_query($connection, $query);
    if($query_run)
    {
        echo 'Data saved successfully!';  // This will confirm if the data is saved

        echo '<script type="text/javascript">alert("data saved")</script>';
        header("location:delivery.html");
    // Add the email notification code here
    echo 'Attempting to send email...';  // Check if email block is reached

    $adminQuery = "SELECT email FROM admin";
    $adminResult = mysqli_query($connection, $adminQuery);

    if(mysqli_num_rows($adminResult) > 0) {
        

        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 2;  // Enable debug output

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Use Gmail's SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'sustainbite.donatefood@gmail.com';  // Your email
            $mail->Password = 'yiqv kbbc ktyh niss';  // Your email password (app password if 2FA enabled)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587;

            $mail->setFrom('sustainbite.donatefood@gmail.com', 'Food Donation System');
            
            // Add all admin emails
            while ($row = mysqli_fetch_assoc($adminResult)) {
                $mail->addAddress($row['email']);
            }

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'New Food Donation Received';
            $mail->Body    = "
                <h3>New Food Donation Details</h3>
                <p><b>Name:</b> $name</p>
                <p><b>Food:</b> $foodname</p>
                <p><b>Quantity:</b> $quantity</p>
                <p><b>Address:</b> $address</p>
                <p><b>Expiry Date:</b> $expirydate</p>
                <br>
                <p>Please log in to assign it.</p>
            ";

            $mail->send();
            echo "Donation added and notification sent to admins!";
        } catch (Exception $e) {
            echo "Donation added, but email could not be sent. Error: {$mail->ErrorInfo}";
        }
    }
} else {
    echo '<script type="text/javascript">alert("data not saved")</script>';
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SustainBite</title>
    <link rel="stylesheet" href="loginstyle.css">
</head>
<body style="    background-color: #06C167;">
    <div class="container">
        <div class="regformf" >
    <form action="" method="post">
    <p class="logo"><b style="color: #06C167; ">SustainBite</b></p>
        
       <div class="input">
        <label for="foodname"  > Food Name:</label>
        <input type="text" id="foodname" name="foodname" required/>
        </div>
      
      
        <div class="radio">
        <label for="meal" >Meal type :</label> 
        <br><br>

        <input type="radio" name="meal" id="veg" value="veg" required/>
        <label for="veg" style="padding-right: 40px;">Veg</label>
        <input type="radio" name="meal" id="Non-veg" value="Non-veg" >
        <label for="Non-veg">Non-veg</label>
    
        </div>
        <br>
        <div class="input">
        <label for="food">Select the Category:</label>
        <br><br>
        <div class="image-radio-group">
            <input type="radio" id="raw-food" name="image-choice" value="raw-food">
            <label for="raw-food">
              <img src="img/raw-food.png" alt="raw-food" >
            </label>
            <input type="radio" id="cooked-food" name="image-choice" value="cooked-food"checked>
            <label for="cooked-food">
              <img src="img/cooked-food.png" alt="cooked-food" >
            </label>
            <input type="radio" id="packed-food" name="image-choice" value="packed-food">
            <label for="packed-food">
              <img src="img/packed-food.png" alt="packed-food" >
            </label>
          </div>
          <br>
        <!-- <input type="text" id="food" name="food"> -->
        </div>
        <div class="input" style="display: flex; flex-direction: column; align-items: flex-start; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <label for="quantity" style="margin-right: 10px;">Quantity:</label>
            <label><input type="radio" name="option" value="option1"> Persons</label>
            <label><input type="radio" name="option" value="option2"> Kgs</label>
        </div>
        <input type="text" id="quantity" name="quantity" required style="margin-top: 5px;" />
    </div>
    <div class="input">
    <label for="expirydate">Expiry Date:</label>
    <input type="date" id="expirydate" name="expirydate" required />
</div>
       <b><p style="text-align: center;">Contact Details</p></b>
        <div class="input">
          <!-- <div>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email">
          </div> -->
      <div>
      <label for="name">Name:</label>
      <input type="text" id="name" name="name"value="<?php echo"". $_SESSION['name'] ;?>" required/>
      </div>
      <div>
        <label for="phoneno" >PhoneNo:</label>
      <input type="text" id="phoneno" name="phoneno" maxlength="10" pattern="[0-9]{10}" required />
        
      </div>
      </div>
        <div class="input">
        <label for="location"></label>
        <label for="district">Place:</label>
<select id="district" name="district" style="padding:10px;">
<option value="" selected disabled>Select a location</option>
  <option value="santacruz">Santacruz</option>
  <option value="vileparle">Vileparle</option>
  <option value="andheri">Andheri</option>
  <option value="khar">Khar</option>
  <option value="bandra">Bandra</option>
  <option value="mahim">Mahim</option>
  <option value="dadar">Dadar</option>

</select> 
<div>
  <label for="address" style="padding-left: 0px;">Address:</label>
  <input type="text" id="address" name="address" required/><br>

  </div>        


 
        </div>   
        <div class="btn">
            <button type="submit" name="submit"> Submit</button>
     
        </div>
     </form>
     </div>
   </div>
   
</body>
</html>