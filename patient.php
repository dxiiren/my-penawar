<?php
    session_start();
    require 'db_config.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- icon stylesheet link -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- CSS -->
	<link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' rel='stylesheet' type='text/css' >
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css">
	
	<!-- Script -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js' type='text/javascript'></script>
	

    <title>Admin | Appointment List</title>
	
	<style>
		.contentBookingList{
			width: 100%;
			position: absolute;
			padding:20px 200px 200px 200px;		
		}
		table {
			border-collapse: collapse;
			margin: 25px 0;
			font-size: 0.9em;
			font-family: sans-serif;
			box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
		    text-align:center;
		    margin-left:auto;
		    margin-right:auto;
		}
		.button {
		   background-color: #4CAF50; /* Green */
		   color: white;
		   padding: 11px 16px;
		   text-align: center;
		   display: inline-block;
		   font-size: 14px;
		   cursor: pointer;
		}
		.status {
			border: 1px solid #2EBF0B  ;
			border-radius: 8px;
			padding: 3px;
			margin-top:5px;
			color: #2EBF0B  ;    
		}
		.statusPopup {
			position: relative;
			text-align: center;
			width: 200%;
			height: 50%;
		 }
		th, td {
			text-align: center;
			padding: 8px;
			height: 10%;
			width: 15%;
		}
		tr:nth-child(even){background-color: #f5f5f5}
		tr:nth-child(odd){background-color: #ffffff}
		tr:hover {background-color: #ddd;}
		th {
			background-color: #ac663e;
			color: #ffffff;
		}
		tr {
			border-bottom: 1px solid #dddddd;
			font-weight:bold;
		    color: #000000;
		}
		th:nth-child(8){
			width: 20%;
		}
		
		#myInput {
			  display: inline-block;
			  width: auto;
			  margin-left: 78%;
		}

		#myTable {
		  border-collapse: collapse;
				margin: 25px 0;
				font-size: 0.9em;
				font-family: sans-serif;
				box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
				text-align:center;
				margin-left:auto;
				margin-right:auto;
		}

		#myTable th, #myTable td {
		  text-align: center;
		  padding: 12px;
		}

		#myTable tr {
		  border-bottom: 1px solid #ddd;
		}

		#myTable tr.header, #myTable tr:hover {
		  background-color: #f1f1f1;
		}
		</style>
		
	</head>
	<body>
		<div class="navbar">
			<img src="image/2.png" class="logo">
			<ul>
				<li><a href="index.html">Home</a></li>
				<li><a href="employee profile.php">Profile</a></li>
				<li><a href="aboutus.html">About Us</a></li>
				<li><a href="contact.html">Contact Us</a></li>
				<li><a href="member.html">Our Team</a></li>
			</ul>
		</div>
	  
		<div id="pagewrap">
			<?php include('message.php'); ?>
			<div class="contentBookingList">
				<div class="title">
					<h1 style="text-align:center; font-family: 'Playfair Display SC', serif;"> APPOINTMENT LIST </h1>
				</div>
				<br><br>
				<input type="search" placeholder="Search..." id="myInput" class="form-control search-input" data-table="app-list"/>
				<table class="app-list" id='myTable'>
					<thead>
						<tr>
							<th  onclick="sortTable(0)">APPOINTMENT ID</th>
							<th onclick="sortTable(1)">PATIENT IC</th>
							<th>APPOINTMENT DATE</th>
							<th>APPOINTMENT TIME</th>
							<th>APPOINTMENT DESC</th>
							<th onclick="sortTable(2)">DOCTOR ID</th>
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
										<td><button class="status"><?= $booking['bookingStatus']; ?></button></td>
										
										<td>
											<button data-id=<?= $booking['patientIC']; ?> class='btn btn-info btn-sm btn-popup' >View</button>
											<a href="patientedit.php?id=<?= $booking['patientIC']; ?>" class="btn btn-success btn-sm">Edit</a>
											
											<form action="code.php" method="POST" class="d-inline">
												<button type="submit" name="delete_student" value="<?=$booking['patientIC'];?>" class="btn btn-danger btn-sm">Delete</button>
											</form>
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
					<div class="modal fade" id="custModal" role="dialog">
					  <div class="modal-dialog">

						<!-- Modal content-->
						<div class="modal-content" id="viewpopup">
						  <div class="modal-header">
							<h4 class="modal-title">Patient Details</h4>
							<button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" >×</button>
						  </div>
						  <div class="modal-body">

						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal" data-bs-dismiss="modal" >Close</button>
						  </div>
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
								$('#custModal').modal('show');
							  }
							});
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

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	</body>
</html>