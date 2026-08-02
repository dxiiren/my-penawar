<!DOCTYPE html>
<html lang="en">
	<head>
<?php $pageTitle = 'Book an Appointment — Poliklinik Penawar'; include 'partials/head.php'; ?>

		<!-- jQuery + jQuery UI datepicker -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/humanity/jquery-ui.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#calender").datepicker({minDate:0});
			});
		</script>
	</head>

	<body class="bg-white font-sans text-slate-700 antialiased">

<?php $active = ''; $profileHref = 'patient profile.php'; include 'partials/nav.php'; ?>

		<section class="bg-gradient-to-b from-teal-50/70 to-white">
			<div class="mx-auto max-w-3xl px-4 pb-8 pt-16 text-center sm:px-6">
				<p class="eyebrow">Patient portal</p>
				<h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900">Appointment Request Form</h1>
				<p class="mt-3 text-sm leading-6 text-slate-600">Pick a date and time slot, tell us what you need, and our team will confirm your booking.</p>
			</div>
		</section>

		<div class="mx-auto max-w-4xl px-4 sm:px-6">
			<div class="card p-6 sm:p-10">
				<form method="post">

					<!-- appointment details-->
					<h2 class="text-xl font-extrabold tracking-tight text-slate-900">Appointment Information</h2>
					<hr class="mt-4 border-slate-200">

					<!-- date-->
					<div class="mt-6">
						<label for="calender" class="mb-1.5 block text-sm font-semibold text-slate-700">Appointment Date</label>
						<input type="text" id="calender" name="date" class="field max-w-xs" placeholder="mm/dd/yy" required>
					</div>

					<!-- table information for patient to choose time slot-->
					<div class="mt-8 overflow-x-auto">
						<table class="styled-table" align="center" >

							<tr>
								<th> TIME INTERVAL </th>
								<th> <center>MORNING</center></th>
								<th> <center>AFTERNOON</center></th>
								<th> <center>	EVENING</center></th>

							</tr>
							<tr>
								<th>TIME</th>
								<td>9:00 A.M. - 11:59 A.M.</td>
								<td> 12:00 P.M. - 4:59 P.M. </td>
								<td> 5:00 P.M. - 11:00 A.M.</td>
							</tr>

							<tr class="active-row">
								<th>DOCTOR ON DUTY</th>
								<td>Anis Idyana binti Zaki </td>
								<td> Muhammad Azwan bin Adha <br> Priscilla Rose Moses </td>
								<td> Kayalvili a/p Vinavaran<br> Desmond Soo </td>
							</tr>

							<!-- this one kena ada kaitkan ngan doc approove so that kita leh calculate slot-->
							<?php

									$hostname = "localhost:3307";
									$username = "root";
									$password = "";
									$dbname = "mypenawar";

									//Create Connection
									$connect = mysqli_connect($hostname, $username, $password, $dbname) OR DIE("Connection Failed");


										 $sql = "SELECT bookingStatus
												FROM booking
												WHERE bookingStatus='Approved'
												";

										$result=mysqli_query($connect,$sql);


										// to count and display current slot
										if($result)
										{
											if (mysqli_num_rows($result)>0)
											{
												$count1=0;
												$count2=0;
												$count3=0;

												$bookingTime1="SELECT bookingTime
														FROM booking
														WHERE bookingTime='Morning'
														";

												$result1=mysqli_query($connect,$bookingTime1);

												if (mysqli_num_rows($result1)>0)
													$count1++;

												$bookingTime2="SELECT bookingTime
														FROM booking
														WHERE bookingTime='Afternoon'
														";

												$result2=mysqli_query($connect,$bookingTime2);

												if (mysqli_num_rows($result2)>0)
													$count2++;


												$bookingTime3="SELECT bookingTime
														FROM booking
														WHERE bookingTime='Evening'
														";

												$result3=mysqli_query($connect,$bookingTime3);

												if (mysqli_num_rows($result3)>0)
													$count3++;


												echo "<tr>";
													echo "<th>CURRENT SLOT</th>";
													echo "<td>" .$count1. "</td>";
													echo "<td>" .$count2. "</td>";
													echo "<td>" .$count3. "</td>";
												echo "</tr>";
											}

											else
												echo "Query Failed";

										}

							?>


							<tr class="active-row">
								<th>SELECT TIME:</th>
								<td>
									<input type="radio" id="morning" name="time" value="Morning" class="accent-teal-600" required>
									<label for="morning">Morning</label>
								</td>
								<td>
									<input type="radio" id="afternoon" name="time" value="Afternoon" class="accent-teal-600">
									<label for="afternoon">Afternoon</label>
								</td>
								<td>
									<input type="radio" id="evening" name="time" value="Evening" class="accent-teal-600">
									<label for="evening">Evening</label>
								</td>
							</tr>

						</table>
					</div>

					<!-- patient IC-->
					<div class="mt-6">
						<label for="ic" class="mb-1.5 block text-sm font-semibold text-slate-700">IC / PASSPORT NUMBER</label>
						<input type="text" class="field max-w-xs" name="ic" placeholder="IC Number" >
					</div>

					<!-- description-->
					<div class="mt-6">
						<label for="message" class="mb-1.5 block text-sm font-semibold text-slate-700">Appointment Descriptions</label>
						<textarea id="message" name="message" rows="4" class="field" required></textarea>
					</div>

					<!-- submit button-->
					<div class="mt-8 text-center">
						<input type="submit" value="Submit" name="submit" class="btn-primary cursor-pointer px-8 py-3">
					</div>

				</form>

				<?php

						//connection
						$hostname = "localhost:3307";
						$username = "root";
						$password = "";
						$dbname = "mypenawar";

						//Create Connection
						$connect = mysqli_connect($hostname, $username, $password, $dbname) OR DIE("Connection Failed");



						if(isset($_POST["submit"]))
						{
								$date = date('Y-m-d', strtotime($_POST["date"]));
								$time= $_POST["time"];
								$message = $_POST["message"];
								$empID= 'D1036';
								$pIC=$_POST["ic"];
								$booking = "Pending";

								$sql= "INSERT INTO booking( bookingDate, bookingTime, bookingDesc, empID, patientIC , bookingStatus)
									   VALUES  ('$date', '$time', '$message', '$empID', '$pIC' , '$booking')
									   ";

								$sendsql = mysqli_query($connect,$sql);

								if($sendsql){

									echo "<script>alert('Your appointment is in progress');</script>";
								}

								else
									echo "<script>alert('Your appointment is failed to be progress');</script>";


							}


				?>

			</div>

			<div class="mt-8 text-center">
				<a href="patient profile.php" class="btn-outline px-6 py-3"><i class="fa-solid fa-arrow-left"></i> Back To Profile</a>
			</div>
		</div>

<?php include 'partials/footer.php'; ?>

	</body>
</html>
