<?php
    $user_id = $this->session->userdata('user_id');
    $user_em = $this->session->userdata('user_email');
    $user_role = $this->session->userdata('user_role');
    $user_outlet = $this->session->userdata('user_outlet');

    if (empty($user_id)) {
        redirect(base_url(), 'refresh');
    }

    $tk_c = $this->router->class;
    $tk_m = $this->router->method;

    $alert_msg = $this->session->flashdata('alert_msg');

    $settingResult = $this->db->get_where('site_setting');
    $settingData = $settingResult->row();

    $setting_site_name = $settingData->site_name;
    $setting_pagination = $settingData->pagination;
    $setting_tax = $settingData->tax;
    $setting_currency = $settingData->currency;
    $setting_date = $settingData->datetime_format;
    $setting_product = $settingData->display_product;
    $setting_keyboard = $settingData->display_keyboard;
    $setting_customer_id = $settingData->default_customer_id;
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title><?php echo $setting_site_name; ?></title>

		<link href="<?=base_url()?>assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/css/datepicker3.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/css/styles.css" rel="stylesheet">
		
	
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function(){
			    $("#closeAlert").click(function(){
			        $("#notificationWrp").fadeToggle(1000);
			    });
			});
		</script>
	</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background: linear-gradient(90deg, #105C5B 0%, #10A225 100%);" >
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="<?=base_url()?>technoilahi">
					<?php echo $setting_site_name; ?>
				</a>
				<ul class="user-menu">
					<li class="dropdown pull-right">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><svg class="glyph stroked male-user"><use xlink:href="#stroked-male-user"></use></svg> Pengguna <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="<?=base_url()?>auth/logout"><i class="icono-power" style="color: #30a5ff;"></i> Keluar</a></li>
						</ul>
					</li>
					<li class="dropdown pull-right" style="margin-right: 10px;">
						<a href="<?=base_url()?>technoilahi"  style="text-decoration: none;">
							<div style="background: linear-gradient(90deg, #3EC1D4 20%, #C4D43E 80%); padding: 7px 6px; border-radius: 3px; margin-top: -5px;">
								&nbsp;Kembali Ke Dashboard
							</div>
						</a>
					</li>
					<?php
                        if ($user_role == '1') {
                            if (isset($_COOKIE['outlet'])) {
                                ?>
					<li class="dropdown pull-right" style="margin-right: 10px;">
						<a href="#openedBill" data-toggle="modal" style="text-decoration: none;">
							<div style="background: linear-gradient(90deg, #070707 0%, #319894 100%); padding: 7px 6px; border-radius: 3px; margin-top: -5px;">
								&nbsp;Buka Tangguhan
							</div>
						</a>
					</li>
					
					<li class="dropdown pull-right" style="margin-right: 10px;">
						<a href="#totalSales" data-toggle="modal" style="text-decoration: none;">
							<div style="background: linear-gradient(90deg, #0A0F50 0%, #721471 100%); padding: 7px 6px; border-radius: 3px; margin-top: -5px;">
								&nbsp;Penjualan Harian
							</div>
						</a>
					</li>
					
					<?php

                            }
                        } else {
                            ?>
					<li class="dropdown pull-right" style="margin-right: 10px;">
						<a href="#openedBill" data-toggle="modal" style="text-decoration: none;">
							<div style="background-color: #c72a25; color: #FFF; padding: 7px 6px; border-radius: 3px; margin-top: -5px;">
								&nbsp;Buka Tangguhan
							</div>
						</a>
					</li>
					
					<li class="dropdown pull-right" style="margin-right: 10px;">
						<a href="#totalSales" data-toggle="modal" style="text-decoration: none;">
							<div style="background-color: #3fb618; color: #FFF; padding: 7px 6px; border-radius: 3px; margin-top: -5px;">
								&nbsp;Penjualan Hari ini
							</div>
						</a>
					</li>
					
					<?php	
                        }
                    ?>
				</ul>
				
			</div>
		</div><!-- /.container-fluid -->
	</nav>