<!DOCTYPE html>
<html lang="en">

	<head>
<?php $pageTitle = 'Staff Profile — Poliklinik Penawar'; include 'partials/head.php'; ?>

		<!-- jQuery + jQuery UI datepicker (used by the Edit Profile popup) -->
		<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
		<script>
			$( function() {
				$( "#datepicker" ).datepicker({
					changeMonth: true,
					changeYear: true,
					yearRange: "c-100:c+10"
				});
			} );
		</script>
	</head>

	<body class="bg-white font-sans text-slate-700 antialiased">

<?php $active = 'profile'; $profileHref = 'employee profile.php'; include 'partials/nav.php'; ?>

		<!-- content start-->
		<section class="bg-gradient-to-b from-teal-50/70 to-white">
			<div class="mx-auto max-w-3xl px-4 pb-8 pt-16 text-center sm:px-6">
				<p class="eyebrow">Staff portal</p>
				<h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900">Staff Profile</h1>
				<p class="mt-3 text-sm leading-6 text-slate-600">Your registered staff details on record.</p>
			</div>
		</section>

		<div class="mx-auto max-w-3xl px-4 sm:px-6">
				<?php

						$hostname = "localhost:3307";
						$username = "root";
						$password = "";
						$dbname = "mypenawar";

						//Create Connection
						$connect = mysqli_connect($hostname, $username, $password, $dbname) OR DIE("Connection Failed");

						session_start();
						if(isset($_SESSION["user1"]))
						$id = $_SESSION["user1"];


							 $sql = "SELECT *
									FROM employee
									WHERE empID='$id'
									"; // Fetch data from the table employee using id

							$result=mysqli_query($connect,$sql);

							if($result)
							{

								if(mysqli_num_rows($result)>0)
								{
									foreach($result as $row)
									{
										echo "<table class=\"styled-table\" align=\"center\">";
											echo "<tr>";
												echo "<th>FULL NAME</th>";
												echo "<td>" .$row['empName']. "</td>";
											echo "</tr>";


											echo "<tr class=\"active-row\">";
												echo "<th>BIRTH DATE</th>";
												echo "<td>" .$row['empBirthDate']. "</td>";
											echo "</tr>";

											echo "<tr>";
												echo "<th>ID</th>";
												echo "<td>" .$row['empID']. "</td>";
											echo "</tr>";


											echo "<tr class=\"active-row\">";
												echo "<th>PHONE NUMBER</th>";
												echo "<td>" .$row['empPhoneNum']. "</td>";
											echo "</tr>";

											echo "<tr>";
												echo "<th>E-MAIL</th>";
												echo "<td>" .$row['empEmail']. "</td>";
											echo "</tr>";


											echo "<tr class=\"active-row\">";
												echo "<th>HOME ADDRESS</th>";
												echo "<td>" .$row['empAddress']. "</td>";
											echo "</tr>";



										echo "</table>";

									}
								}

							}
							else
								echo "Query Failed!";

		?>

		<!-- Popup Form-->
			<div class="overlay" id="divOne">
				<div class="card relative w-full max-w-md p-6 sm:p-8">
					<a href='#' class="absolute right-4 top-3 text-2xl font-bold text-slate-400 transition hover:text-slate-600" aria-label="Close"> &times;</a>
					<h2 class="text-xl font-extrabold tracking-tight text-slate-900"> Edit Your Profile</h2>
					<form method="post" class="mt-5 space-y-4">
						<div>
							<label class="mb-1.5 block text-sm font-semibold text-slate-700"> Full Name</label>
							<input type="text" name="newName" class="field" placeholder="Your Full Name" required>
						</div>
						<div>
							<label for="date" class="mb-1.5 block text-sm font-semibold text-slate-700">Birth Date</label>
							<input type="text" id="datepicker" name="date" class="field" placeholder="mm/dd/yy" required>
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-semibold text-slate-700"> Phone Number</label>
							<input type="text" name="newPhone" class="field" placeholder="+60" required>
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-semibold text-slate-700"> E-mail</label>
							<input type="text" name="newEmail" class="field" placeholder="Your E-mail" >
						</div>
						<div>
							<label class="mb-1.5 block text-sm font-semibold text-slate-700"> Home Address</label>
							<input type="text" name="newAddress" class="field" placeholder="Your Home Address" required>
						</div>
						<input type="submit" class="btn-primary w-full cursor-pointer py-3" value="Submit" name="submit">
					</form>
				</div>
			</div>



			<!-- PHP for EDIT PROFILE-->
			<?php

            $hostname = "localhost:3307";
            $username = "root";
            $password = "";
            $dbname = "mypenawar";

            //Create Connection
            $connect = mysqli_connect($hostname, $username, $password, $dbname) OR DIE("Connection Failed");

             //PHP for EDIT PROFILE form

                 if(isset($_POST["submit"]))
				{
					  $newName = $_POST["newName"];
					  $newEmail = $_POST["newEmail"];
					  $newPhone = $_POST["newPhone"];
					  $newAddress = $_POST["newAddress"];
					  $newBdate = date('Y-m-d', strtotime($_POST["date"]));

					  $sql= "UPDATE employee
						 SET empName = '$newName', empBirthDate='$newBdate', empPhoneNum='$newPhone', empAddress='$newAddress', empEmail = '$newEmail'
						 WHERE empID='$id'
						  ";

					  $sendsql = mysqli_query($connect,$sql);

					  if($sendsql)
					  {

						if(mysqli_affected_rows($connect) == 0)
						{
						  echo "<script>alert('Profile Unsuccessfully Updated !');</script> " ;
						}
						else
						{
						  echo "<script>alert('Profile Successfully Updated !');</script> " ;
						}

					  }
					   else
						echo "Query Failed!";
				}


				?>

		<!-- button options-->
		<div class="mt-10 flex flex-wrap justify-center gap-3">
			<a href="#divOne" class="btn-outline px-6 py-3"><i class="fa-regular fa-pen-to-square"></i> Edit Profile</a>
			<a href="patient.php" class="btn-primary px-6 py-3"><i class="fa-solid fa-list-check"></i> View Appointment List</a>
			<a href="monthly report.php" class="btn-outline px-6 py-3"><i class="fa-solid fa-chart-column"></i> Monthly Report</a>
		</div>
		</div>

<?php include 'partials/footer.php'; ?>
</body>
</html>
