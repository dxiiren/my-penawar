<html>
	<head>
			
		<!-- icon stylesheet link -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
		
		<!-- link with css -->
		<link rel="stylesheet" type="text/css" href="style.css">
		
		<!-- covers almost all of the characters and symbols in the world!-->
		<meta charset="UTF-8">
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Jquery and Javascript-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
		
		
		<!-- datepicker link-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<link rel="stylesheet"
		href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/humanity/jquery-ui.css">
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
		
	<title>Admin | Monthly Report</title>
		
	<style>
			body{
			background-color: #fff9e3;
			background-size: cover;
			}
			
			.styled-table2 {
				border-collapse: collapse;
				margin: 25px 0;
				font-size: 0.9em;
				font-family: sans-serif;
				min-width: 400px;
				box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
				text-align:center;
				margin-left:auto;
				margin-right:auto;
				width:70%;
			}
			.styled-table2  th {
				background-color: #ac663e ;
				color: #ffffff;
				text-align: left;
			}
			.styled-table2 th,
			.styled-table td {
				padding: 15px 20px;
			}
			.styled-table2 td{
				width:600px;
				padding: 15px 20px;

			}
			.styled-table2  tr {
				border-bottom: 1px solid #dddddd;
				font-weight:bold;
				color: #000000;
			}
			.styled-table2  tr:nth-of-type(even) {
				background-color: #f5f5f5;
			}
			.styled-table2  tr:nth-of-type(odd) {
				background-color: #ffffff;
			}
			.styled-table2  tr:last-of-type {
				border-bottom: 2px solid #ac663e ;
			}
			.styled-table2  tr.active-row {
				font-weight: bold;
				color: #000000 ;
			}
			input[type=text] {
			  width: 500px;
			  margin-bottom: 20px;
			  padding: 12px;
			  border: 1px solid #ccc;
			  border-radius: 3px;
			}
			label {
				margin-left:300px;
			}
			.ui-datepicker-calendar {
				display: none;
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
		
		
		<br><br><br><br><br>
		
		
		<!-- content start-->
		<div id="pagecontainer">
			<div id="pagewrap">
				<div class="title">
					<h1 style="text-align:center; font-family: 'Playfair Display SC', serif;"> MONTHLY REPORT </h1>
					<!--<hr style="width:20%; margin:auto;"><br>-->
				</div>
				
				<!-- date-->
				 <br><br><br><br>
						 <div>
							<div>
								<label for="date">Choose Month and Year</label>
								 <input type="text" id="calender" name="date" placeholder="mm/yy"  required>
							</div>
						</div> 
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
			
			<!-- footer-->
			<footer>
			<div class="rowindex">
				<div class="columnindex">
					<img src="image/2.png" class="logo">
					<p>
						The polyclinic provided services such as regular medical check-ups, as well as mental health services. 
						Contribute to the care of patients suffering from behavioural issues — especially those from low 
						socio-economic backgrounds.
					</p>
				</div>
				
				<div class="columnindex">
					<h3> Office <div class="under"><span></span></div> </h3>
					<p>No 28G, Jalan Semenyih Sentral 4,</p> 
					<p>Taman Semenyih Sentral, </p>
					<p>43500 Semenyih, Selangor</p>
					<p>Malaysia</p>
					<p class="email-id">poliklinikpenawar@gmail.com</p>
					<h4> +60 - 31234567
				</div>
				
				<div class="columnindex">
					<div class="linkfooter">
						<h3>Links <div class="under"><span></span></div> <h3/>
						<ul>
							<li><a href="#">Home</a></li>
							<li><a href="#">Profile</a></li>
							<li><a href="aboutus.html">About Us</a></li>
							<li><a href="contact.html">Contact Us</a></li>
							<li><a href="member.html">Our Member</a></li>
						</ul>
					</div>
				</div>
				
				<div class="columnindex">
					<h3>Newsletter <div class="under"><span></span></div>  </h3>
					<form>
						<i class="fa-regular fa-envelope"></i>
						<input type="email" placeholder=" enter your email id" required>
						<button type="submit"><i class="fa-solid fa-arrow-right"></i></button>
					</form>
					
					<div class="social-icons">
						<i class="fa-brands fa-facebook"></i>
						<i class="fa-brands fa-instagram"></i>
						<i class="fa-brands fa-twitter"></i>
						<i class="fa-brands fa-linkedin"></i>
					</div>
					
				</div>
				
				
			</div>
				
				<hr>
				<p class="copyright"> MyPenawar Sdn Bhd &copy; 2022 - All Rights Reserved</p>
			
			</footer>
		</div>
			
		<!-- end of footer-->
	</body>
</html>	
