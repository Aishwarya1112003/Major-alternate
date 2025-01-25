<?php
include("login.php"); 
// if($_SESSION['loggedin']==true){
//     header("location:loginindex.html");
// }

if($_SESSION['name']==''){
    header("location: signup.php");
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<header>
    <div class="logo"><b style="color: #06C167;">SustainBite</b></div>
    <div class="hamburger">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
    <nav class="nav-bar">
        <ul>
            <li><a href="home.html">Home</a></li>
            <li><a href="about.html" >About</a></li>
            <li><a href="contact.html"  >Contact</a></li>
            <li><a href="profile.php"  class="active">Profile</a></li>
        </ul>
    </nav>
</header>

<script>
    hamburger = document.querySelector(".hamburger");
    hamburger.onclick = function() {
        navBar = document.querySelector(".nav-bar");
        navBar.classList.toggle("active");
    }
</script>

<div class="profile">
    <div class="profilebox">
        <p class="headingline" style="text-align: left;font-size:30px;"> <img src="" alt="" style="width:40px; height:  height: 25px;; padding-right: 10px; position: relative;" >Profile</p>
        <br>
        <div class="info" style="padding-left:10px;">
            <p>Name  :<?php echo"". $_SESSION['name'] ;?> </p><br>
            <p>Email :<?php echo"". $_SESSION['email'];?> </p><br>
            <p>Gender:<?php echo"". $_SESSION['gender'] ;?> </p><br>
            <a href="logout.php" style="float: left;margin-top: 6px ;border-radius:5px; background-color: #06C167; color: white;padding-left: 10px;padding-right: 10px;">Logout</a>
        </div>
        <br>
        <hr>
        <br>
        <p class="heading">Your donations</p>
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>FOOD</th>
                            <th>TYPE</th>
                            <th>CATEGORY</th>
                            <th>DATE/TIME</th>
                            <th>EXPIRY</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $email = $_SESSION['email'];
                        $query = "select * from food_donations where email='$email' order by Fid Desc";
                        $result = mysqli_query($connection, $query);
                        if($result == true) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $status = $row['donation_status'];
                                $statusClass = '';  // Default class

                                // Assign a class based on the status
                                switch($status) {
                                    case 'Pending':
                                        $statusClass = 'Pending'; // Dark yellow
                                        break;
                                    case 'Accepted by NGO':
                                        $statusClass = 'assigned'; // Dark green
                                        break;
                                    case 'canceled':
                                        $statusClass = 'canceled'; // Red
                                        break;
                                    case 'Pickup Today':
                                        $statusClass = 'pickup'; // Dark blue
                                        break;
                                    case 'Delivered the Food':
                                        $statusClass = 'Delivered'; // Purple
                                        break;
                                    default:
                                        // $statusClass = 'default'; // Default if status doesn't match
                                        break;
                                }

                                echo "<tr>
                                        <td>".$row['food']."</td>
                                        <td>".$row['type']."</td>
                                        <td>".$row['category']."</td>
                                        <td>".$row['date']."</td>
                                        <td>".$row['expiry']."</td>
                                        <td class='$statusClass'>".$row['donation_status']."</td>
                                      </tr>";
                            }
                        }
                    ?> 
                    </tbody>
                </table>
            </div>
        </div>  
    </div>
</div>

</body>
</html>
