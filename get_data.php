<?php
require 'db_config.php';

if(isset($_POST['patientIC'])){
	$ic = $_POST['patientIC'];

	$query = "select * from patient where patientIC=".$ic;
	$result = mysqli_query($connect, $query);

	$response = "<table border='0' width='100%'>";
	while( $row = mysqli_fetch_array($result) ){
		$ic= $row['patientIC'];
		$name = $row['patientName'];
		$address = $row['patientAddress'];
		$email = $row['patientEmail'];
		$phone = $row['patientPhoneNum'];

		$response .= "<tr style='width:70%'>";
		$response .= "<td> Name : </td><td>".$name."</td>";
		$response .= "</tr>";

		$response .= "<tr  >";
		$response .= "<td style='width:20%' > Address : </td><td >".$address."</td>";
		$response .= "</tr>";

		$response .= "<tr>";
		$response .= "<td> Email : </td><td>".$email."</td>";
		$response .= "</tr>";

		$response .= "<tr>";
		$response .= "<td> Phone : </td><td>".$phone."</td>";
		$response .= "</tr>";
	}
	$response .= "</table>";

	echo $response;
	exit;
}
?>