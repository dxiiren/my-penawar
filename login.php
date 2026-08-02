<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php $pageTitle = 'Login | Register — Poliklinik Penawar'; include 'partials/head.php'; ?>
</head>

<body class="bg-white font-sans text-slate-700 antialiased">

<?php include 'partials/nav.php'; ?>

	<section class="bg-gradient-to-b from-teal-50/70 to-white">
		<div class="mx-auto w-full max-w-lg px-4 pb-8 pt-16 sm:px-6">
			<div class="text-center">
				<p class="eyebrow">Patient &amp; staff portal</p>
				<h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Welcome to MyPenawar</h1>
				<p class="mt-3 text-sm leading-6 text-slate-600">Log in to manage your appointments, or register as a new patient.</p>
			</div>

			<div class="card mt-8 p-6 sm:p-8">
				<!-- Tab switcher -->
				<div class="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1" role="tablist">
					<button type="button" id="tabLogin" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-teal-700 shadow-sm" onclick="showLogin()" role="tab" aria-selected="true">Log In</button>
					<button type="button" id="tabRegister" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-500 transition hover:text-slate-700" onclick="showRegister()" role="tab" aria-selected="false">Register</button>
				</div>

				<!-- Login form (ids/names are load-bearing: smoke tests pin id="login") -->
				<form id="login" class="mt-6 space-y-5" method="post">
					<div>
						<label class="mb-1.5 block text-sm font-semibold text-slate-700">Username</label>
						<input type="text" name="id" class="field" placeholder="Your username" required>
					</div>
					<div>
						<label class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
						<input type="password" name="pass" class="field" placeholder="Enter password" required>
					</div>
					<fieldset>
						<legend class="mb-2 text-sm font-semibold text-slate-700">I am a</legend>
						<div class="grid grid-cols-2 gap-3">
							<label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 has-[:checked]:text-teal-800">
								<input type="radio" name="user" value="patient" class="accent-teal-600"> Patient
							</label>
							<label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 has-[:checked]:text-teal-800">
								<input type="radio" name="user" value="staff" class="accent-teal-600"> Staff
							</label>
						</div>
					</fieldset>
					<button type="submit" name="login" class="btn-primary w-full py-3"><i class="fa-solid fa-right-to-bracket"></i> Log in</button>
					<p class="text-center">
						<a href="recover_psw.php" class="text-sm font-semibold text-teal-700 transition hover:text-teal-800 hover:underline">Forgot your password?</a>
					</p>
				</form>

				<!-- Registration form -->
				<form id="register" class="mt-6 hidden space-y-5" method="post">
					<div>
						<h2 class="text-sm font-bold uppercase tracking-wider text-teal-700">Account information</h2>
						<div class="mt-3 space-y-4">
							<input type="text" class="field" name="user" placeholder="Username">
							<input type="password" class="field" name="pw" placeholder="Password">
						</div>
					</div>
					<div>
						<h2 class="text-sm font-bold uppercase tracking-wider text-teal-700">Personal details</h2>
						<div class="mt-3 grid gap-4 sm:grid-cols-2">
							<input type="text" class="field sm:col-span-2" name="name" placeholder="Full name">
							<input type="text" class="field" name="ic" placeholder="IC number">
							<input type="number" class="field" name="age" placeholder="Age">
							<div class="sm:col-span-2">
								<label class="mb-1.5 block text-xs font-semibold text-slate-500">Birth date</label>
								<input type="date" class="field" name="birth">
							</div>
							<input type="email" class="field" name="email" placeholder="Email">
							<input type="text" class="field" name="phone" placeholder="Phone number">
							<textarea name="add" id="message" rows="2" class="field sm:col-span-2" placeholder="Enter your address here . . ."></textarea>
						</div>
					</div>
					<button type="submit" name="register" class="btn-primary w-full py-3"><i class="fa-solid fa-user-plus"></i> Register</button>
				</form>
			</div>
		</div>
	</section>

<?php include 'partials/footer.php'; ?>

	<script>
		var loginForm = document.getElementById("login");
		var registerForm = document.getElementById("register");
		var tabLogin = document.getElementById("tabLogin");
		var tabRegister = document.getElementById("tabRegister");

		function setTab(activeTab, inactiveTab) {
			activeTab.className = "rounded-lg bg-white px-4 py-2 text-sm font-semibold text-teal-700 shadow-sm";
			activeTab.setAttribute("aria-selected", "true");
			inactiveTab.className = "rounded-lg px-4 py-2 text-sm font-semibold text-slate-500 transition hover:text-slate-700";
			inactiveTab.setAttribute("aria-selected", "false");
		}

		function showLogin() {
			loginForm.classList.remove("hidden");
			registerForm.classList.add("hidden");
			setTab(tabLogin, tabRegister);
		}

		function showRegister() {
			registerForm.classList.remove("hidden");
			loginForm.classList.add("hidden");
			setTab(tabRegister, tabLogin);
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
					$_SESSION["user1"] = $_POST["id"];
					$pass = $_POST["pass"];
					$user = $_POST["user"];

					//write sql

					if($user=="patient")
					{
						$sql= "SELECT *
							   FROM patient
							   WHERE patientUser = '$id' AND patientPassword='$pass'
							   ";

						$sendsql = mysqli_query($connect,$sql);

						if($sendsql)
						{
							if(mysqli_num_rows($sendsql)>0)
							{
								echo "<script>alert('Welcome $id!');</script> " ;
								echo '<meta http-equiv="refresh" content="0; URL= patient profile.php">';
							}
							else
							{
								echo "<script>alert('Unsuceessfully Login  !');</script> " ;
								echo '<meta http-equiv="refresh" content="0; URL=login.php">';
							}
						}
						else
							echo "Query Failed!";
					}//patient

					else if ($user=="staff")
					{
						$sql= "SELECT *
							   FROM employee
							   WHERE empID = '$id' AND empPassword='$pass'
							   ";

						$sendsql = mysqli_query($connect,$sql);

						if($sendsql)
						{
							if(mysqli_num_rows($sendsql)>0)
							{

								echo "<script>alert('Welcome $id!');</script> " ;
								echo '<meta http-equiv="refresh" content="0; URL=employee profile.php">';
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
				}

			//register
				if(isset($_POST["register"]))
				{
					$name = $_POST["name"];
					$ic = $_POST["ic"];
					$age = $_POST["age"];
					$birth = $_POST["birth"];
					$user = $_POST["user"];
					$pw = $_POST["pw"];
					$email = $_POST["email"];
					$phone = $_POST["phone"];
					$address = $_POST["add"];

					//write sql

						//check whether acc have been created or not
						$sql= "SELECT *
							   FROM patient
							   WHERE patientIC = '$ic'
							   ";

						$sendsql = mysqli_query($connect,$sql);

						if($sendsql)
						{
							if(mysqli_num_rows($sendsql)>0)
							{

								echo "<script>alert('Account with that username or IC have been existed! Please Login using your username and passwword ');</script> " ;
								echo '<meta http-equiv="refresh" content="0; URL=login.php">';
							}
							else
							{
								$sql2= "INSERT INTO patient( patientName, patientIC, patientBirthDate, patientUser, patientPassword, patientEmail, patientPhoneNum, patientAddress )
										VALUES  ('$name', '$ic' , '$birth', '$user', '$pw', '$email', '$phone' , '$address' )";

								$sendsql2 = mysqli_query($connect,$sql2);

								if($sendsql2)
								{
									echo "<script>alert('Your detail has been successfully registered. Thank you!');</script> " ;
									echo '<meta http-equiv="refresh" content="0; URL=login.php">';
								}
								else
									echo "<script>alert('Query Failed!');</script> " ;
							}
						}
						else
							echo "Query Failed!";




				}

		?>
</body>

</html>
