<?php
session_start();
require 'db_config.php';

if(isset($_POST['delete_student']))
{
    $patient_ic = mysqli_real_escape_string($connect, $_POST['delete_student']);
	echo "<script type='text/javascript'>alert('$patient_ic');</script>";
	
	$query = "DELETE FROM booking WHERE patientIC='$patient_ic' ";
    $query_run = mysqli_query($connect, $query);

    if($query_run)
    {
		echo "<script>alert('Data Deleted Successfully !');</script> " ;
		echo '<meta http-equiv="refresh" content="0; URL=patient.php">';        
    }
    else
    {
		echo "<script>alert('Data Not Deleted !');</script> " ;
        echo '<meta http-equiv="refresh" content="0; URL=patient.php">'; 
    }
}

if(isset($_POST['update_student']))
{
    $patient_ic = mysqli_real_escape_string($connect, $_POST['patientIC']);
    $bookingStatus = mysqli_real_escape_string($connect, $_POST['bookingStatus']);
    $serviceID = mysqli_real_escape_string($connect, $_POST['bookingService']);
   

	
		$query = "UPDATE booking 
				  SET bookingStatus = '$bookingStatus', booking.serviceID = '$serviceID', 
				  booking.empID = (SELECT service.empID FROM service, booking WHERE service.serviceID = '$serviceID' AND booking.patientIC='$patient_ic')
				   WHERE booking.patientIC='$patient_ic'";
			  
    $query_run = mysqli_query($connect, $query);

    if($query_run)
    {
		echo "<script>alert('Appointment Status Updated Successfully  !');</script> " ;
        echo '<meta http-equiv="refresh" content="0; URL=patient.php">'; 
    }
    else
    {
		echo "<script>alert('Appointment Status Not Updated  !');</script> ";
        echo '<meta http-equiv="refresh" content="0; URL=patient.php">'; 
    }

}

if(isset($_POST['update_completed']))
{
    $patient_ic = mysqli_real_escape_string($connect, $_POST['patientIC']);
    $bookingStatus = mysqli_real_escape_string($connect, $_POST['bookingStatus']);
	$payAmount = mysqli_real_escape_string($connect, $_POST['payamount']);
	$payMethod = mysqli_real_escape_string($connect, $_POST['paymethod']);
	$payDate = mysqli_real_escape_string($connect, $_POST['paydate']);
	
    $serviceID = mysqli_real_escape_string($connect, $_POST['bookingService']);
   
	if ($bookingStatus == 'Completed')
	{
		$query = "INSERT payment (paymentAmount, paymentMethod, paymentDate) 
				  VALUES ('$payAmount', '$payMethod', '$payDate')";
	}
		  
    $query_run = mysqli_query($connect, $query);

    if($query_run)
    {
		echo "<script>alert('Payment Updated Successfully  !');</script> " ;
        echo '<meta http-equiv="refresh" content="0; URL=patient.php">'; 
    }
    else
    {
		echo "<script>alert('Payment Not Updated  !');</script> ";
        echo '<meta http-equiv="refresh" content="0; URL=patient.php">'; 
    }

}


?>