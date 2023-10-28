
<!DOCTYPE html>
<html>
	<head>
		<title>BOOKING APPOINTMENT </title>
		
		<!-- icon stylesheet link-->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
		
		<!-- link with css-->
		<link rel="stylesheet" type="text/css" href="style.css">
		
		<!-- covers almost all of the characters and symbols in the world!-->
		<meta charset="UTF-8">
		
		<!-- viewport-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- datepicker link-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<link rel="stylesheet"
		href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/humanity/jquery-ui.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

		<script>
			$(document).ready(function(){
				$("#calender").datepicker({minDate:0});
				
			});
			
		</script>
		
		<style>
			.arr{
			  background:#ff0000;
			  display:block;
			  float:left;
			}



			.arr-prev{
			  .arrow(90,50px,2px,#fff);
			}

			
			.arrow(@direction,@size,@stroke,@bgcolor) {
			  width: @size;
			  height: @size;
			  margin:@size/2;
			  -webkit-transform: rotate((@direction + 45) * 1deg);
			  -moz-transform: rotate((@direction + 45) * 1deg);
			  transform: rotate((@direction + 45) * 1deg);
			  -o-transform: rotate((@direction + 45) * 1deg);

			  &:after {
				content: '';
				position: absolute;
				left: @stroke;
				top:  @stroke;
				width: @size;
				height: @size;  
				background: @bgcolor;
			  }
			}

			.demo{ margin:100px auto; width:400px;}
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
				<div class="content">		
					<center><h1 style="font-family: 'Playfair Display SC', serif;">Appointment Request Form</h1></center><br><br>
				  <div>
					<div class="container2">
					  <form method="post">
					  
						
							
							<!-- appointment details-->
							<h3 style="color:#000; font-family: 'Playfair Display SC', serif ;">Appointment Information</h3>
							<hr><br><br>
							
							<!-- date-->
							 <div>
								<div>
									<label for="date">Appointment Date</label>
									<input type="text" id="calender" name="date" placeholder="mm/dd/yy"  required>
								</div>
							</div>   
							
			
							
							<!-- table information for patient to choose time slot-->
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
											<input type="radio" id="morning" name="time" value="Morning" required>
											<label for="morning">Morning</label>
										</td>
										<td>
											<input type="radio" id="afternoon" name="time" value="Afternoon">
											<label for="afternoon">Afternoon</label>
										</td>
										<td>
											<input type="radio" id="evening" name="time" value="Evening">
											<label for="evening">Evening</label>
										</td>
									</tr>
									
							</table>
							
							<!-- patient IC-->
							<div>
								<label for="ic">IC / PASSPORT NUMBER</label>
								<input type="text" class="input-field" name="ic" placeholder="IC Number" > 
							</div>
							<br>
							
							<!-- description-->
							<label for="message">Appointment Descriptions</label>
							<textarea id="message" name="message" style="height:100px" required></textarea><br>
							<br>	
						  
							<!-- submit button-->
							<center><input type="submit" value="Submit" name="submit" class="btn"></center>
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
								
							
							<br><br>
							<center><a href="patient profile.php"><button class="btn2">Back To Profile</button></a></center>
					</div>
				</div>
		<br><br><br><br>
		
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

	</body>
</html>

