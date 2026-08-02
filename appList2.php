<?php
	// Auth guard: this portal page needs a logged-in session. Redirect anonymous
	// direct hits to the login page BEFORE any output, and exit so nothing below
	// runs (no undefined-$id warnings, no DB query for a user that isn't there).
	session_start();
	if (!isset($_SESSION["user1"])) {
		header("Location: login.php");
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">

	<head>
<?php $pageTitle = 'Appointment History — Poliklinik Penawar'; include 'partials/head.php'; ?>
	</head>

	<body class="bg-white font-sans text-slate-700 antialiased">

<?php $active = ''; $profileHref = 'patient profile.php'; include 'partials/nav.php'; ?>

		<section class="bg-gradient-to-b from-teal-50/70 to-white">
			<div class="mx-auto max-w-4xl px-4 pb-8 pt-16 text-center sm:px-6">
				<p class="eyebrow">Patient portal</p>
				<h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900">Appointment History</h1>
				<p class="mt-3 text-sm leading-6 text-slate-600">Every appointment you've booked with us, at a glance.</p>
			</div>
		</section>

		<div class="mx-auto max-w-5xl px-4 sm:px-6">
			<div class="data-table overflow-x-auto">
						<?php
						$hostname = "localhost:3307";
						$username = "root";
						$password = "";
						$dbname = "mypenawar";

						$connect = mysqli_connect($hostname, $username, $password, $dbname) OR DIE("Connection Failed");

						if(isset($_SESSION["user1"]))
						$id = $_SESSION["user1"];

							$join="SELECT *
									FROM patient
									INNER JOIN booking
									ON patient.patientIC=booking.patientIC
									WHERE patient.patientUser='$id'
									";

						$sendsql = mysqli_query($connect, $join);

						if($sendsql)
						{
							echo "<table>
									<tr>
										<th>APPOINTMENT ID</th>
										<th>PATIENT IC</th>
										<th>APPOINTMENT DATE</th>
										<th>APPOINTMENT TIME</th>
										<th>DOCTOR ID</th>
										<th>STATUS</th>
									</tr>";

							foreach($sendsql as $row)
							{
								$bID = $row["bookingID"];
								$pIC = $row["patientIC"];
								$bDate = $row["bookingDate"];
								$bTime = $row["bookingTime"];
								$eID = $row["empID"];
								$bStatus=$row["bookingStatus"];

								echo "<tr>";
									echo "<td>" . $bID . "</td>";
									echo "<td>" . $pIC . "</td>";
									echo "<td>" . $bDate . "</td>";
									echo "<td>" . $bTime . "</td>";
									echo "<td>" . $eID . "</td>";
									echo "<td>".$bStatus. "</td>";?>

					<?php
								echo "</tr>";
							}

							echo "</table>";
						}

						else
							echo "query failed";
					?>
			</div>

			<div class="mt-8 text-center">
				<a href="patient profile.php" class="btn-outline px-6 py-3"><i class="fa-solid fa-arrow-left"></i> Back To Profile</a>
			</div>
		</div>

<?php include 'partials/footer.php'; ?>
	</body>
</html>
