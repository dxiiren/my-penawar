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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- Script -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js' type='text/javascript'></script>

    <title>Admin | Edit Appointment Status</title>
</head>
<body>
  
    <div class="container mt-5">

        <?php include('message.php'); ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Patient's Appointment Status
                            <a href="patient.php" class="btn btn-danger float-end">BACK</a>
                        </h4>
                    </div>
                    <div class="card-body">
					
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
                                <form action="code.php" method="POST">
                                    <input type="hidden" name="patientIC" value="<?= $booking['patientIC']; ?>">
										
                                    <div class="mb-3">
                                        <label>Appointment Status</label>
										<select id="selectStatus" name="bookingStatus" value="<?=$booking['bookingStatus'];?>" class="form-control">
											<option value="Pending" selected>Pending</option>
											<option value="Approved">Approved</option>
											<option value="Completed">Completed</option>
										</select>
									</div>
									
									<div class="mb-3">	
										<label> Service</label>
										<select  id="selectService" name="bookingService" value="<?=$booking['serviceID'];?>" class="form-control">
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
									
                                    <div class="mb-3">
                                        <button type="submit" name="update_student" class="btn btn-primary">
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
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>