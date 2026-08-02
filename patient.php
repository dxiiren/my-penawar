<?php
    session_start();
    require 'db_config.php';
?>
<!doctype html>
<html lang="en">
  <head>
<?php $pageTitle = 'Admin | Appointment List — Poliklinik Penawar'; include 'partials/head.php'; ?>

	<!-- jQuery (AJAX patient-details popup) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	</head>
	<body class="bg-white font-sans text-slate-700 antialiased">

<?php $active = ''; $profileHref = 'employee profile.php'; include 'partials/nav.php'; ?>

		<section class="bg-gradient-to-b from-teal-50/70 to-white">
			<div class="mx-auto max-w-4xl px-4 pb-8 pt-16 text-center sm:px-6">
				<p class="eyebrow">Staff portal</p>
				<h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900">Appointment List</h1>
				<p class="mt-3 text-sm leading-6 text-slate-600">Review, update and manage every patient appointment.</p>
			</div>
		</section>

		<div class="mx-auto max-w-6xl px-4 sm:px-6">
			<?php include('message.php'); ?>
			<div class="mb-4 flex justify-end">
				<input type="search" placeholder="Search appointments..." id="myInput" class="field max-w-xs search-input" data-table="app-list"/>
			</div>
			<div class="data-table overflow-x-auto">
				<table class="app-list" id='myTable'>
					<thead>
						<tr>
							<th onclick="sortTable(0)" class="cursor-pointer">APPOINTMENT ID <i class="fa-solid fa-sort ml-1 text-teal-200"></i></th>
							<th onclick="sortTable(1)" class="cursor-pointer">PATIENT IC <i class="fa-solid fa-sort ml-1 text-teal-200"></i></th>
							<th>APPOINTMENT DATE</th>
							<th>APPOINTMENT TIME</th>
							<th>APPOINTMENT DESC</th>
							<th onclick="sortTable(2)" class="cursor-pointer">DOCTOR ID <i class="fa-solid fa-sort ml-1 text-teal-200"></i></th>
							<th>STATUS</th>
							<th>ACTION</th>
						</tr>
					<thead>
					<tbody>
						<?php
							$query = "SELECT booking.bookingID, booking.bookingDate, booking.bookingTime, booking.bookingDesc,
									  booking.bookingStatus, booking.empID, booking.patientIC, patient.patientName
									  FROM patient, booking
									  WHERE patient.patientIC = booking.patientIC	";;

							$query_run = mysqli_query($connect, $query);

							if(mysqli_num_rows($query_run) > 0)
							{
								foreach($query_run as $booking)
								{
									?>
									<tr>
										<td><?= $booking['bookingID']; ?></td>
										<td><?= $booking['patientIC']; ?></td>
										<td><?= $booking['bookingDate']; ?></td>
										<td><?= $booking['bookingTime']; ?></td>
										<td><?= $booking['bookingDesc']; ?></td>
										<td><?= $booking['empID']; ?></td>
										<td><span class="inline-flex rounded-full border border-teal-300 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-800"><?= $booking['bookingStatus']; ?></span></td>

										<td>
											<div class="flex items-center justify-center gap-1.5">
												<button data-id=<?= $booking['patientIC']; ?> class='btn-popup inline-flex items-center rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-200' >View</button>
												<a href="patientedit.php?id=<?= $booking['patientIC']; ?>" class="inline-flex items-center rounded-md bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-teal-700">Edit</a>

												<form action="code.php" method="POST" class="inline">
													<button type="submit" name="delete_student" value="<?=$booking['patientIC'];?>" class="inline-flex items-center rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-rose-700">Delete</button>
												</form>
											</div>
										</td>
									</tr>
									<?php
								}
							}
							else
							{
								echo "<h5> No Record Found </h5>";
							}
						?>
					</tbody>
				</table>
			</div>
		</div>

					<!-- Modal -->
					<div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4" id="custModal">
						<!-- Modal content-->
						<div class="card w-full max-w-lg" id="viewpopup">
							<div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
								<h4 class="text-lg font-bold text-slate-900">Patient Details</h4>
								<button type="button" class="modal-close text-2xl font-bold leading-none text-slate-400 transition hover:text-slate-600" aria-label="Close">×</button>
							</div>
							<div class="modal-body px-6 py-5 text-sm text-slate-700">

							</div>
							<div class="border-t border-slate-200 px-6 py-4 text-right">
								<button type="button" class="modal-close btn-outline">Close</button>
							</div>
						</div>
					</div>


				<script type="text/javascript">
						$(document).ready(function () {

						  $('.btn-popup').click(function () {
							var patientIC = $(this).data('id');
							$.ajax({
							  url: 'get_data.php',
							  type: 'post',
							  data: { patientIC: patientIC },
							  success: function (response) {
								$('.modal-body').html(response);
								$('#custModal').removeClass('hidden').addClass('flex');
							  }
							});
						  });

						  $('.modal-close').click(function () {
							$('#custModal').removeClass('flex').addClass('hidden');
						  });

						});


					//function sort table  asc/desc
					function sortTable(n) {
						var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
						table = document.getElementById("myTable");
						switching = true;

						dir = "asc";

						while (switching) {
							switching = false;
							rows = table.rows;

							for (i = 1; i < (rows.length - 1); i++) {
								shouldSwitch = false;

								x = rows[i].getElementsByTagName("TD")[n];
								y = rows[i + 1].getElementsByTagName("TD")[n];

								if (dir == "asc") {
									if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
										shouldSwitch= true;
										break;
									}
								}
								else if (dir == "desc") {
									if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
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


					//funtion search data in table
					 (function(document) {
							'use strict';

							var TableFilter = (function(myArray) {
								var search_input;

								function _onInputSearch(e) {
									search_input = e.target;
									var tables = document.getElementsByClassName(search_input.getAttribute('data-table'));
									myArray.forEach.call(tables, function(table) {
										myArray.forEach.call(table.tBodies, function(tbody) {
											myArray.forEach.call(tbody.rows, function(row) {
												var text_content = row.textContent.toLowerCase();
												var search_val = search_input.value.toLowerCase();
												row.style.display = text_content.indexOf(search_val) > -1 ? '' : 'none';
											});
										});
									});
								}

								return {
									init: function() {
										var inputs = document.getElementsByClassName('search-input');
										myArray.forEach.call(inputs, function(input) {
											input.oninput = _onInputSearch;
										});
									}
								};
							})(Array.prototype);

							document.addEventListener('readystatechange', function() {
								if (document.readyState === 'complete') {
									TableFilter.init();
								}
							});

						})(document);
				  </script>

<?php include 'partials/footer.php'; ?>
	</body>
</html>
