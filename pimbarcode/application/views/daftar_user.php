
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
	
<title>Pengaturan barcode</title>

<link rel="icon" type="image/png" href="<?=base_url()?>auth/login"/>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/vendor/animate/animate.css">	
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/vendor/css-hamburgers/hamburgers.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/vendor/animsition/css/animsition.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/vendor/select2/select2.min.css">	
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/vendor/daterangepicker/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/css/util.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/logindesign/css/main.css">
	
</head>

<body style="background-color: blue;">
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
			<form class="login100-form validate-form" action="<?php echo base_url(); ?>Auth/saveuser" method="post">
				<a class="btn btn-primary" href="http://pimws.anthesis-erp.online/pim.apk"> Klik Untuk Download App Android Disini</a>
  <div class="form-group" >
    <label for="exampleInputEmail1">NIK</label>
    <input type="text" name="nik" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter NIK">
   
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
   <div class="form-group">
    <label for="exampleInputPassword1">Ulangi Password</label>
    <input type="password" name="upassword" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
   <div class="form-group">
    <label for="exampleInputPassword1">Nomor Serial Perangkat</label>
    <input type="text" name="sn" class="form-control" id="exampleInputPassword1" placeholder="Nomor serial perangkat">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>

						<a class="btn btn-danger" href="<?php echo base_url();?>"> Batal</a>
<br>

					
</form>

				<div class="login100-more" style="background-image: url('<?=base_url()?>assets/logindesign/images/bg-01.jpg');">
				</div>
			</div>
		</div>
	</div>
	
	

	
	
<!--===============================================================================================-->
	<script src="<?=base_url()?>assets/logindesign/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="<?=base_url()?>assets/logindesign/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="<?=base_url()?>assets/logindesign/vendor/bootstrap/js/popper.js"></script>
	<script src="<?=base_url()?>assets/logindesign/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="<?=base_url()?>assets/logindesign/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="<?=base_url()?>assets/logindesign/vendor/daterangepicker/moment.min.js"></script>
	<script src="<?=base_url()?>assets/logindesign/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="<?=base_url()?>assets/logindesign/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="<?=base_url()?>assets/logindesign/js/main.js"></script>

</body>
</html>