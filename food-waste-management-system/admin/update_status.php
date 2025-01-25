<?php
include("connect.php");

if(isset($_POST['update_status'])) {
    $fid = $_POST['fid'];
    $new_status = $_POST['donation_status'];

    $update_query = "UPDATE food_donations SET donation_status = '$new_status' WHERE Fid = $fid";
    if(mysqli_query($connection, $update_query)) {
        echo "<script>alert('Donation status updated successfully');</script>";
    } else {
        echo "<script>alert('Failed to update status');</script>";
    }
    
    // Redirect back to admin dashboard
    header("Location: admin.php");
    exit();
}
?>
