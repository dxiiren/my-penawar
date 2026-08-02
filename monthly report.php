<!DOCTYPE html>
<html lang="en">
	<head>
<?php $pageTitle = 'Admin | Monthly Report — Poliklinik Penawar'; include 'partials/head.php'; ?>

		<!-- jQuery + jQuery UI month picker -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/humanity/jquery-ui.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<script type="text/javascript">
        $(function() {
            $('#calender').datepicker( {
			changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'MM yy',
            onClose: function(dateText, inst) {
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
            });
        });
    </script>

		<style>
			/* Month-only picker: hide the day grid */
			.ui-datepicker-calendar {
				display: none;
			}
		</style>
	</head>

	<body class="bg-white font-sans text-slate-700 antialiased">

<?php $active = ''; $profileHref = 'employee profile.php'; include 'partials/nav.php'; ?>

		<!-- content start-->
		<section class="bg-gradient-to-b from-teal-50/70 to-white">
			<div class="mx-auto max-w-4xl px-4 pb-8 pt-16 text-center sm:px-6">
				<p class="eyebrow">Staff portal</p>
				<h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900">Monthly Report</h1>
				<p class="mt-3 text-sm leading-6 text-slate-600">Completed bookings and total sales, grouped by service.</p>
			</div>
		</section>

		<div class="mx-auto max-w-4xl px-4 sm:px-6">
			<!-- date-->
			<div class="mb-6 flex flex-wrap items-center justify-center gap-3">
				<label for="calender" class="text-sm font-semibold text-slate-700">Choose Month and Year</label>
				<input type="text" id="calender" name="date" class="field max-w-[180px]" placeholder="mm/yy" required>
			</div>
			<div class="overflow-x-auto">
				<?php
					$hostname = "localhost:3307";
					$username = "root";
					$password = "";
					$dbname = "mypenawar";

					$connect = mysqli_connect($hostname, $username, $password, $dbname) OR DIE("Connection Failed");
					$sql = "SELECT service.serviceID , service.serviceName, COUNT(booking.bookingID) AS totBooking, SUM(paymentAmount) AS totPayment
							FROM  payment, service, booking
							WHERE booking.serviceID = service.serviceID
							AND booking.bookingID = payment.bookingID
							AND booking.bookingStatus = 'Completed'
							GROUP BY service.serviceID ; ";

					$sendsql = mysqli_query($connect, $sql);

					if($sendsql)
					{	?>

						<table class="styled-table2" id="myTable" align="center">
							<tr>
								<th onclick="sortTable(0)"><center>SERVICE ID</center></th>
								<th><center>SERVICE NAME</center></th>
								<th><center>TOTAL NUMBER OF BOOKING</center></th>
								<th><center>TOTAL SALES</center></th>

							</tr>
						<?php
							while($row=mysqli_fetch_assoc($sendsql))
							{
								$sID = $row["serviceID"];
								$sName = $row["serviceName"];
								$totBook = $row["totBooking"];
								$totPay = $row["totPayment"];?>

								<tr>
									<?php
									echo "<td>" . $sID . "</td>";
									echo "<td>" . $sName . "</td>";
									echo "<td>" . $totBook . "</td>";
									echo "<td>" . $totPay . "</td>";?>
								</tr>
					<?php 	}
						echo "</table>";
					}?>
			</div>

			<div class="mt-8 text-center">
				<a href="employee profile.php" class="btn-outline px-6 py-3"><i class="fa-solid fa-arrow-left"></i> Back To Profile</a>
			</div>
		</div>

			<script>
			function sortTable(n) {
				var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
				table = document.getElementById("myTable");
				switching = true;
				//Set the sorting direction to ascending:
				dir = "asc";
				/*Make a loop that will continue until
				no switching has been done:*/
				while (switching) {
					//start by saying: no switching is done:
					switching = false;
					rows = table.rows;
					/*Loop through all table rows (except the
					first, which contains table headers):*/
					for (i = 1; i < (rows.length - 1); i++) {
						//start by saying there should be no switching:
						shouldSwitch = false;
						/*Get the two elements you want to compare,
						one from current row and one from the next:*/
						x = rows[i].getElementsByTagName("TD")[n];
						y = rows[i + 1].getElementsByTagName("TD")[n];
						/*check if the two rows should switch place,
						based on the direction, asc or desc:*/
						if (dir == "asc") {
							if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
								//if so, mark as a switch and break the loop:
								shouldSwitch= true;
								break;
							}
						}
						else if (dir == "desc") {
							if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
								//if so, mark as a switch and break the loop:
								shouldSwitch = true;
								break;
							}
						}
					}
					if (shouldSwitch) {
						/*If a switch has been marked, make the switch
						and mark that a switch has been done:*/
						rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
						switching = true;
						//Each time a switch is done, increase this count by 1:
						switchcount ++;
					}
					else {
						/*If no switching has been done AND the direction is "asc",
						set the direction to "desc" and run the while loop again.*/
						if (switchcount == 0 && dir == "asc") {
							dir = "desc";
							switching = true;
						}
					}
				}
			}
			</script>

<?php include 'partials/footer.php'; ?>
	</body>
</html>
