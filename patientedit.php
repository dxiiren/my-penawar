<?php
session_start();
require 'db_config.php';
?>

<!doctype html>
<html lang="en">
  <head>
<?php $pageTitle = 'Admin | Edit Appointment Status — Poliklinik Penawar'; include 'partials/head.php'; ?>
</head>
<body class="bg-white font-sans text-slate-700 antialiased">

<?php $active = ''; $profileHref = 'employee profile.php'; include 'partials/nav.php'; ?>

    <section class="bg-gradient-to-b from-teal-50/70 to-white">
        <div class="mx-auto max-w-2xl px-4 pb-8 pt-16 text-center sm:px-6">
            <p class="eyebrow">Staff portal</p>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Edit Patient's Appointment Status</h1>
        </div>
    </section>

    <div class="mx-auto max-w-2xl px-4 sm:px-6">

        <?php include('message.php'); ?>

        <div class="card p-6 sm:p-8">
            <div class="mb-6 flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-slate-900">Appointment details</h2>
                <a href="patient.php" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> BACK</a>
            </div>

                        <?php
                        if(isset($_GET['id']))
                        {
                            $patient_ic = mysqli_real_escape_string($connect, $_GET['id']);
                            $query = "SELECT * FROM booking WHERE booking.patientIC='$patient_ic' ";
                            $query_run = mysqli_query($connect, $query);
                            if(mysqli_num_rows($query_run) > 0)
                            {
                                $booking = mysqli_fetch_array($query_run);
                                ?>
                                <form action="code.php" method="POST" class="space-y-5">
                                    <input type="hidden" name="patientIC" value="<?= $booking['patientIC']; ?>">

                                    <div>
                                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Appointment Status</label>
										<select id="selectStatus" name="bookingStatus" value="<?=$booking['bookingStatus'];?>" class="field">
											<option value="Pending" selected>Pending</option>
											<option value="Approved">Approved</option>
											<option value="Completed">Completed</option>
										</select>
									</div>

									<div>
										<label class="mb-1.5 block text-sm font-semibold text-slate-700"> Service</label>
										<select  id="selectService" name="bookingService" value="<?=$booking['serviceID'];?>" class="field">
											<option value="selectS" selected>Choose</option>
											<option value="SV001">SV001 - Medical Check Up</option>
											<option value="SV002">SV002 - Laboratory Testing</option>
											<option value="SV003">SV003 - Screening & Treatment</option>
											<option value="SV004">SV004 - Prenatal Check Up</option>
											<option value="SV005">SV005 - Postnatal Check Up</option>
											<option value="SV006">SV006 - Minor Surgery</option>
											<option value="SV007">SV007 - Minor Symptom</option>
											<option value="SV008">SV008 - Common Illness</option>
											<option value="SV009">SV009 - Minor Injury</option>
											<option value="SV010">SV010 - Vaccination</option>
										</select>
                                    </div>

                                    <div class="pt-2">
                                        <button type="submit" name="update_student" class="btn-primary w-full py-3">
                                            Update Appointment
                                        </button>
                                    </div>
                                </form>
                                <?php
                            }
                            else
                            {
                                echo "<h4>No Such Id Found</h4>";
                            }
                        }
                        ?>
        </div>
    </div>

<?php include 'partials/footer.php'; ?>
</body>
</html>
