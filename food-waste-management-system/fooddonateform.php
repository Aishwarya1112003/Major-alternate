<?php
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

        echo '<script type="text/javascript">alert("data saved")</script>';
        header("location:delivery.html");
    }
    else{
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