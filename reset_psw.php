<?php session_start() ;
include('db_config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php $pageTitle = 'Reset Password — Poliklinik Penawar'; include 'partials/head.php'; ?>
</head>

<body class="bg-white font-sans text-slate-700 antialiased">

<?php include 'partials/nav.php'; ?>

<main class="bg-gradient-to-b from-teal-50/70 to-white">
    <div class="mx-auto w-full max-w-md px-4 pb-8 pt-16 sm:px-6">
        <div class="text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-teal-100 text-xl text-teal-700"><i class="fa-solid fa-lock"></i></span>
            <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-slate-900">Reset your password</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Choose a new password for your account.
            </p>
        </div>

        <div class="card mt-8 p-6 sm:p-8">
            <form action="#" method="POST" name="login" class="space-y-5">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" class="field pr-11" name="password" required autofocus>
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-teal-600" aria-label="Show password">
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
                <input type="submit" value="Reset" name="reset" class="btn-primary w-full cursor-pointer py-3">
            </form>
            <p class="mt-6 text-center">
                <a href="login.php" class="text-sm font-semibold text-teal-700 transition hover:text-teal-800 hover:underline"><i class="fa-solid fa-arrow-left mr-1"></i> Back to login</a>
            </p>
        </div>
    </div>
</main>

<?php include 'partials/footer.php'; ?>
</body>
</html>
<?php
    if(isset($_POST["reset"])){
        include('db_config.php');
        $psw = $_POST["password"];

        $token = $_SESSION['token'];
        $Email = $_SESSION['email'];

        //$hash = password_hash( $psw , PASSWORD_DEFAULT );

        $sql = mysqli_query($connect, "SELECT * FROM patient WHERE patientEmail='$Email'");
        $query = mysqli_num_rows($sql);
  	    $fetch = mysqli_fetch_assoc($sql);

        if($Email){
            //$new_pass = $hash;
            mysqli_query($connect, "UPDATE patient SET patientPassword='$psw' WHERE patientEmail='$Email'");
            ?>
            <script>
                window.location.replace("index2.php");
                alert("<?php echo "your password has been succesful reset"?>");
            </script>
            <?php
        }else{
            ?>
            <script>
                alert("<?php echo "Please try again"?>");
            </script>
            <?php
        }
    }

?>
<script>
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    toggle.addEventListener('click', function(){
        if(password.type === "password"){
            password.type = 'text';
        }else{
            password.type = 'password';
        }
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
</script>
