<html>

	<head>
			<!-- icon stylesheet link -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
		
		<!-- link with css -->
		<link rel="stylesheet" type="text/css" href="style.css">
		
		<!-- covers almost all of the characters and symbols in the world!-->
		<meta charset="UTF-8">
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>APPOINTMENT HISTORY LIST</title>
		
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
				width:75%;;
			}
			
			th, td {
				text-align: center;
				padding: 8px;
				height: 10%;
				width: 10%;
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
		</style>
	</head>

	<body>
		<div class="navbar">
			<img src="image/2.png" class="logo">
			<ul>
				<li><a href="index.html">Home</a></li>
				<li><a href="patient profile.php">Profile</a></li>
				<li><a href="aboutus.html">About Us</a></li>
				<li><a href="contact.html">Contact Us</a></li>
				<li><a href="member.html">Our Team</a></li>
			</ul>
		</div>
		
		<div id="pagecontainer">
			<div id="pagewrap">
			  <div class="contentBookingList">
					<div>
						<h1 class="title">Appointment History List</h1><br><br>
						<?php
						$hostname = "localhost:3307";
						$username = "root";
						$password = "";
						$dbname = "mypenawar";
									
						$connect = mysqli_connect($hostname, $username, $password, $dbname) OR DIE("Connection Failed");							
						
						session_start();
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
							echo "<table border='1'>
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
					
					
					
			  </div>
			</div>
			
			
			<!-- FOOTER-->
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
						<li><a href="index.html">Home</a></li>
							<ul>
								<li><a href="index.html">Home</a></li>
								<!--<li><a href="#">Profile</a></li>-->
								<li><a href="aboutus.html">About Us</a></li>
								<li><a href="contact.html">Contact Us</a></li>
								<li><a href="member.html">Our Team</a></li>
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
		</body>
</html>