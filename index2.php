<html>

<head>
		<!-- icon stylesheet link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
	
	<!-- link with css -->
	<link rel="stylesheet" type="text/css" href="style.css">
	
	<!-- covers almost all of the characters and symbols in the world!-->
    <meta charset="UTF-8">
	
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!------ Include the above in your HEAD tag ---------->
	
    <title>Login | Register</title>
	
	<style>
      * {
        box-sizing: border-box;
      }
      .openBtn {
        display: flex;
        justify-content: left;
      }
      .openButton {
        background-color: #fff;
        color: red;
		
		width: 85%; 
		padding: 10px 30px; 
		cursor: pointer; 
		display: block; 
		margin: auto; 
		border: 0; 
		outline: none; 
		border-radius: 30px;
      }
      .loginPopup {
        position: relative;
        text-align: center;
        width: 100%;
      }
      .formPopup {
        display: none;
        position: fixed;
        left: 50%;
        top: 12%;
        transform: translate(-50%, 5%);
        border: 3px solid #999999;
        z-index: 9;
      }
      .formContainer {
        max-width: 300px;
        padding: 40px;
        background-color: #fff;
      }
      .formContainer input[type=text],
      .formContainer input[type=password] {
        width: 100%;
        padding: 15px;
        margin: 5px 0 20px 0;
        border: none;
        background: #eee;
      }
      .formContainer input[type=text]:focus,
      .formContainer input[type=password]:focus {
        background-color: #ddd;
        outline: none;
      }
      .formContainer .btn {
        padding: 12px 20px;
        border: none;
        background-color: #8ebf42;
        color: #fff;
        cursor: pointer;
        width: 100%;
        margin-bottom: 15px;
        opacity: 0.8;
      }
      .formContainer .cancel {
        background-color: #cc0000;
      }
      .formContainer .btn:hover,
      .openButton:hover {
        opacity: 1;
      }
    </style>
	
</head>

<body>
	<div class="navbar">
		<img src="image/2.png" class="logo">
		<ul>
			<li><a href="index.html">Home</a></li>
			<li><a href="#">Profile</a></li>
			<li><a href="aboutus.html">About Us</a></li>
			<li><a href="contact.html">Contact Us</a></li>
			<li><a href="member.html">Our Member</a></li>
		</ul>
	</div>
	
	<div id="pagecontainer">
	<div id="pagewrap">
	<div class="contentLogin">
			<div class="form-box">
				<div class="button-box">
					<div id="btn"></div>
					<button type="button" class="toggle-btn" onclick="login()" >Log In</button>
					<button type="button" class="toggle-btn" onclick="register()" >Register</button>
				</div>
				
				<form  id="login" class="input-group" method="post"> 
					<input type="text" name="id" class="input-field" placeholder="Username" required> 
					<input type="password" name="pass" class="input-field" placeholder="Enter Password" required> <br><br>
					
					<input type="radio" name="user" value="patient"> Patient &nbsp;
					<input type="radio" name="user" value="staff"> Staff

					<br><br><br>
					<button type="submit" name="login" class="submit-btn">Log in</button>
					<br>
					<div class="col-md-6 offset-md-4">
                                <a href="recover_psw.php" class="btn btn-link">
                                    Forgot Your Password?
                                </a>
                            </div>
				</form>
				
				<form id="register" class="input-group" method="post"> 
				
					<input type="text" class="input-field" name="name" placeholder="Full Name" > 
					<input type="text" class="input-field" name="ic" placeholder="IC Number" > 
					<input type="text" class="input-field" name="user" placeholder="Username" >
					<input type="password" class="input-field" name="pw" placeholder="Password" > 
					<input type="email" class="input-field" name="email" placeholder="Email" > 
					<input type="text" class="input-field" name="phone" placeholder="Phone Num" > 
					<br><br>
					<textarea name="add" id="message" cols="28" rows="1" placeholder="Enter yout address here . . . "></textarea>
					<br><br>
					<input type="radio" name="user1" value="patient"> Patient &nbsp;
					<input type="radio" name="user1" value="staff"> Staff
					<br><br>
					<button type="submit" name="register" class="reg-btn">Register</button> 
				</form>
				
			<div class="loginPopup">
			  <div class="formPopup" id="popupForm">
				<form id="forgotpassword" class="formContainer" method="post">
				  <h2>Forgot Password</h2><br><br>
				  <input type="email" class="email1" placeholder="Email" required> <br>
				  <input type="password" class="pw1" placeholder="New Password" required> 
				  
				  <!--<br><br>
				  <input type="radio" name="user2" value="patient"> Patient &nbsp;
				  <input type="radio" name="user2" value="staff"> Staff
				  <br><br>-->
				  
				  <input type="submit" value="Login" name="login">
                  <a href="recover_psw.php" class="btn btn-link">
				  <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
				</form>
			  </div>
			</div>
		</div>

	</div>
	
	</div>
	
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
				<h4> +60 - 31234567 </h4>
			</div>
			
			<div class="columnindex">
				<div class="linkfooter">
				<h3>Links <div class="under"><span></span></div> </h3>
					<ul>
						<li><a href="index.html">Home</a></li>
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
	
	<!-- 
	<div class="footer">
		<p style="color:white">Copyright &copy; 2022 MyPenawar Sdn Bhd</p>
	</div>-->
	
	<script>
		var x = document.getElementById("login");
		var y = document.getElementById("register");
		var z = document.getElementById("btn");
	
		function register(){
			x.style.left ="-400px";
			y.style.left ="50px";
			z.style.left ="110px";
		}
		
		function login(){
			x.style.left ="50px";
			y.style.left ="450px";
			z.style.left ="0px";
		}
		
		 function openForm() {
        document.getElementById("popupForm").style.display = "block";
      }
      function closeForm() {
        document.getElementById("popupForm").style.display = "none";
      }
	</script>
	<?php
	
		//connection
			$hostname = "localhost:3307";
			$username = "root";
			$password = "";
			$dbname = "mypenawar";
			
			//Create Connection
			$connect = mysqli_connect($hostname, $username, $password, $dbname) OR DIE("Connection Failed");
			
			//login
				if(isset($_POST["login"]))
				{
					$id = $_POST["id"];
					$pass = $_POST["pass"];
					$user = $_POST["user"];
					
					//write sql
					
					if($user=="patient")
					{	
						$sql= "SELECT *
							   FROM patient 
							   WHERE username = '$id' AND password='$pass'
							   ";
					}
					else
					{
						$sql= "SELECT *
							   FROM staff
							   WHERE username = '$id' AND password='$pass'
							   ";
					}
					
					$sendsql = mysqli_query($connect,$sql);
					
					if($sendsql)
					{
						if(mysqli_num_rows($sendsql)>0)
						{
							
							echo "<script>alert('Suceessfully Login  !');</script> " ;
							echo '<meta http-equiv="refresh" content="0; URL=aboutus.html">';
						}
						else
						{
							echo "<script>alert('Unsuceessfully Login  !');</script> " ;
							echo '<meta http-equiv="refresh" content="0; URL=login.php">';
						}
							
					}
					else
						echo "Query Failed!";
				}
		            
				
			
			//register
				if(isset($_POST["register"]))
				{
					
					$username = $_POST["user"];
					$pw = $_POST["pw"];
					$name = $_POST["name"];
					$ic = $_POST["ic"];
					$email = $_POST["email"];
					$phone = $_POST["phone"];
					$address = $_POST["add"];
					$user = $_POST["user1"];
					
					
					echo "<script>alert('Register button push up');</script> " ;

					//write sql
					if($user=="patient")
					{	
						$sql= "INSERT INTO patient( username, password)
						   VALUES  ('$username' , '$pw' )";
						   
						  echo "<script>alert('Patient  !');</script> " ;
					}
					else
					{
						$sql= "INSERT INTO staff( username, password)
						   VALUES  ('$user' , '$pw' )";
						   
						   echo "<script>alert('Staff !');</script> " ;
					}
						
					$sendsql = mysqli_query($connect,$sql);
					
					if($sendsql)
					{
						echo "<script>alert('Your detail has been successfully registered <br> Thank you!');</script> " ;
						echo '<meta http-equiv="refresh" content="0; URL=aboutus.html">';
					}
					else
						echo "<script>alert('Query Failed!');</script> " ;
					
				}
		?>
</body>

</html>