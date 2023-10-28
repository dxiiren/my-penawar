<?php
session_start();
require 'db_config.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Receipt</title>

		<style>
			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
		</style>
	</head>

	<body>
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
									<img src="image/2.png" style="width: 50%; max-width: 300px" />
								</td>
<?php
						// must pass booking id from appList2.php -->  $_SESSION["bookID"] = $_POST["bookingID"];
						//fetch payment data
						if(isset($_SESSION["bookID"])) //ic patient
						{	$id = $_SESSION["bookID"];
							
							 $sql = "SELECT *
									FROM payment 
									WHERE bookingID='$id'
									"; // Fetch data from the table customers using id 
							
							$result=mysqli_query($connect,$sql);
							
							while( $row = mysqli_fetch_array($result) ){
								$invoice = $row['paymentID'];
								$date = $row['paymentDate'];
								
								echo "<td>" . 
									"invoice" . $invoice . "<br />" .
									"Created :" . $date . "<br />" .
									"Due : " . $date . "<br />" .
								"</td>";
							}
						}
?>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="2">
						<table>
							<tr>
								<td>
									No 28G, Jalan Semenyih Sentral 4,<br />
									Taman Semenyih Sentral,<br />
									43500 Semenyih, Selangor<br />
									Malaysia<br />
								</td>
<?php
						//fetch patient data
						if(isset($_SESSION["user1"])) //ic patient
						{	$id = $_SESSION["user1"];
							
							 $sql = "SELECT *
									FROM patient 
									WHERE patientUser='$id'
									"; // Fetch data from the table customers using id 
							
							$result=mysqli_query($connect,$sql);
							
							while( $row = mysqli_fetch_array($result) ){
								$name = $row['patientName'];
								$email = $row['patientEmail'];
								$phone = $row['patientPhoneNum'];							
							
								
								echo "<td>" . 
									$name . "<br />" .
									$email .  "<br />" .
									$phone . "<br />" .
								"</td>";
							}
						}
?>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<td>Payment Method</td>

					<td>Status</td>
				</tr>
				
				<tr class="details">
<?php
						// must pass booking id from appList2.php -->  $_SESSION["bookID"] = $_POST["bookingID"];
						//fetch payment data
						if(isset($_SESSION["bookID"])) //ic patient
						{	$id = $_SESSION["bookID"];
							
							 $sql = "SELECT *
									FROM payment 
									WHERE bookingID='$id'
									"; // Fetch data from the table customers using id 
							
							$result=mysqli_query($connect,$sql);
							
							while( $row = mysqli_fetch_array($result) ){
								
								$method = $row['paymentMethod'];
								
								$date = $row['paymentDate'];
								$amount = $row['payment'];
								
								$_SESSION["date"] = $date;
								$_SESSION["amount"] = $amount;
								
								echo "<td>". $method . "</td>";
							}
						}
?>

					<td>Completed</td>
				</tr>

				<tr class="heading">
					<td>Item</td>
					<td>Price</td>
				</tr>

				<tr class="item">
					<?php
						echo "<td> Consultation </td>";
						if(isset($_SESSION["amount"]))
							echo "<td> RM". $_SESSION["amount"] . "</td>";
					?>
				</tr>

				<tr class="total">
					<td></td>
					<?php
						if(isset($_SESSION["amount"]))
							echo "<td> Total : RM". $_SESSION["amount"] . "</td>";
					?>
				</tr>
			</table>
		</div>
	</body>
</html>