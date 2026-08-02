<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php $pageTitle = 'Password Recovery — Poliklinik Penawar'; include 'partials/head.php'; ?>
</head>

<body class="bg-white font-sans text-slate-700 antialiased">

<?php include 'partials/nav.php'; ?>

<main class="bg-gradient-to-b from-teal-50/70 to-white">
    <div class="mx-auto w-full max-w-md px-4 pb-8 pt-16 sm:px-6">
        <div class="text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-teal-100 text-xl text-teal-700"><i class="fa-solid fa-key"></i></span>
            <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-slate-900">Recover your password</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Enter the email address on your patient account and we'll send you a reset link.
            </p>
        </div>

        <div class="card mt-8 p-6 sm:p-8">
            <form action="#" method="POST" name="recover_psw" class="space-y-5">
                <div>
                    <label for="email_address" class="mb-1.5 block text-sm font-semibold text-slate-700">E-Mail Address</label>
                    <input type="text" id="email_address" class="field" name="email" placeholder="you@example.com" required autofocus>
                </div>
                <input type="submit" value="Recover" name="recover" class="btn-primary w-full cursor-pointer py-3">
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
    if(isset($_POST["recover"])){
        include('db_config.php');
        $email = $_POST["email"];

        $sql = mysqli_query($connect, "SELECT * FROM patient WHERE patientEmail='$email'");
        $query = mysqli_num_rows($sql);
  	    $fetch = mysqli_fetch_assoc($sql);

        if(mysqli_num_rows($sql) <= 0){
            ?>
            <script>
                alert("<?php  echo "Sorry, no emails exists "?>");
            </script>

           <?php
        }else{
            // generate token by binaryhexa
            $token = bin2hex(random_bytes(50));

            //session_start ();
            $_SESSION['token'] = $token;
            $_SESSION['email'] = $email;

            require "Mail/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host='smtp.elasticemail.com';
            $mail->Port=587;
            $mail->SMTPAuth=true;
            $mail->SMTPSecure='tls';

            // h-hotel account
            $mail->Username='2020479526@student.uitm.edu.my';
            $mail->Password='REPLACE_WITH_YOUR_ELASTICEMAIL_API_KEY';

            // send by h-hotel email
            $mail->setFrom('2020479526@student.uitm.edu.my', 'Password Reset');
            // get email from input
            $mail->addAddress($_POST["email"]);
            //$mail->addReplyTo('lamkaizhe16@gmail.com');

            // HTML body
            $mail->isHTML(true);
            $mail->Subject="Recover your password";
            $mail->Body="<b>Dear User</b>
            <h3>We received a request to reset your password.</h3>
            <p>Kindly click the below link to reset your password</p>
            http://localhost/MyPenawar/reset_psw.php
            <br><br>
            <p>With regrads,</p>
            <b>Programming with Lam</b>";

            if(!$mail->send()){
                ?>
                    <script>
                        alert("<?php echo " Invalid Email "?>");
                    </script>
                <?php
            }else{
                ?>
                    <script>
                        window.location.replace("notification.html");
                    </script>
                <?php
            }
        }
    }


?>
