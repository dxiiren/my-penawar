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
		  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
		  <link rel="stylesheet" href="/resources/demos/style.css">
		  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
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
		  
		  
		<title>Admin | Profile</title>
		
		<!-- Message for user-->
		<script>
				// Validating Empty Field
				function check_empty() {
				if (document.getElementById('name').value == "" || document.getElementById('email').value == "" || document.getElementById('msg').value == "") {
				alert("Fill All Fields !");
				} else {
				document.getElementById('form').submit();
				alert("Form Submitted Successfully...");
				}
				}
				//Function To Display Popup
				function div_show() {
				document.getElementById('abc').style.display = "block";
				}
				//Function to Hide Popup
				function div_hide(){
				document.getElementById('abc', 'close').style.display = "none";
				}
		</script>
		
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
				<h1 style="text-align:center; 	font-family: 'Playfair Display SC', serif;">  PROFILE </h1>
				<hr style="width:20%; margin:auto;"><br>
			
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
				
				
			<br><br><br>
		
		<!-- Popup Form-->
			<div class="overlay" id="divOne">
				<div class="wrapper">
					<h2> Edit Your Profile</h2>
					<a href='#' class="close"> &times;</a>
						<div class="container4">
							<form method="post">
								<!--<label> Employee ID <br><b style="color:red;">Please enter your registered username</b></label>
								<input type="text" name="newID" placeholder="Your ID">-->
								<label> Full Name</label>
								<input type="text" name="newName" placeholder="Your Full Name" required>
								<!-- date-->
								<div>
									<div>
										<label for="date">Birth Date</label>
										<input type="text" id="datepicker" name="date" placeholder="mm/dd/yy" required>
									</div>
								</div>
								<label> Phone Number</label>
								<input type="text" name="newPhone" placeholder="+60" required>
								<label> E-mail</label>
								<input type="text" name="newEmail" placeholder="Your E-mail" >
								<label> Home Address</label>
								<input type="text" name="newAddress" placeholder="Your Home Address" required>
								
								
								<center><input type="submit" class="button" value="Submit" name="submit"></center>
							</form>
						</div>
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
		<center>
			<a href="#divOne"><button class="btn3"><b style="font-family: 'Playfair Display SC', serif;"> Edit <br> Profile </b></button></a>
			<a href="patient.php"><button class="btn3"><b style="font-family: 'Playfair Display SC', serif;"> View Appointment List</b></button></a>		
			<a href="monthly report.php"><button class="btn3"><b style="font-family: 'Playfair Display SC', serif;"> Monthly <br>Report</b></button></a>		
		</center>
		</div>
		
		
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
