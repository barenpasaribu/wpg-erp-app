
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>System Percetakan Barcode</title>

		<link href="<?=base_url()?>assets/css/datepicker3.css" rel="stylesheet">

		<link href="<?=base_url()?>assets/belakang/dist/design/libs/flot/css/float-chart.css" rel="stylesheet">
    	<link href="<?=base_url()?>assets/belakang/dist/css/style.min.css" rel="stylesheet">

		<link href="<?=base_url()?>assets/css/icono.min.css" rel="stylesheet">
		
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


	<div id="main-wrapper">

       <!-- Yang ini Bagian Header Atas, Meliputi Menu Pintasan, Untuk Mensett Bagian Header Maka Di bagian Sini Lah -->
        <header class="topbar"  data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">

            	<!-- Adapun Bagian ini Meliputi Bagian Logo Header | Itu loh logo yang berada di bagian pojok kiri atas -->
                <div class="navbar-header" data-logobg="skin5">
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <a class="navbar-brand" href="<?=base_url()?>technoilahi">
                        <b class="logo-icon p-l-10">
                            <img style="width: 40px;" src="<?=base_url()?>assets/belakang/design/images/logo-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <span class="logo-text">
                             <h4 style="padding-top: 10px;">ANTHESIS ERP</h4>
                        </span>
                    </a>
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <!-- Bagian Logo Selesai -->


                <div  class="navbar-collapse collapse"  id="navbarSupportedContent" data-navbarbg="skin5">
                	<!-- Adapun yang ini adalah Bagian Menu yang buat Mengaktifkan Opsi JS, Biar Si Sidebarnya bisa di kecil dan di besarkan hehe -->
                    <ul class="navbar-nav float-left mr-auto" >
                        <li class="nav-item d-none d-md-block">
                        	<a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar">
                        		<i class="mdi mdi-menu font-24"></i>
                        	</a>
                        </li>
                    </ul>
                  
                </div>
            </nav>
        </header>
        <!-- Bagian Menu Atas Selesai | Jika Tidak Suka Dengan Menu Atas Tingal Hapus Saja-->

        <!-- Adapun ini Bagian Menu Sidebar | Yang Mana Sih ? Itu Log Menu Yang bagian Samping -->
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">

                    <ul id="sidebarnav" class="p-t-30">

                        <li class="sidebar-item "> 
                        	<a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?=base_url()?>technoilahi" aria-expanded="false"><i class="fa fa-dashboard"></i>
                        		<span class="hide-menu">Dashboard</span>
                        	</a>
                        </li>


                       
                        <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="<?=base_url()?>datakaryawan/data_tampil" aria-expanded="false">
                                <i class="fa fa-users"></i><span class="hide-menu"> Data Karyawan </span>
                            </a>
                          
                        </li>

                          <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="<?=base_url()?>kodeblok/data_tampil" aria-expanded="false">
                                <i class="mdi mdi-cube"></i><span class="hide-menu"> Data kode blok </span>
                            </a>
                         
                        </li>

                         <li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="<?=base_url()?>kodevhc/data_tampil" aria-expanded="false">
                                <i class="fa fa-list"></i><span class="hide-menu"> Data kode Vhc </span>
                            </a>
                        
                        </li>


                        <li class="sidebar-item "> 
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="<?php echo base_url();?>job/data_tampil" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i>
                                <span class="hide-menu">Data Job</span>
                            </a>
                        </li>

                       
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>