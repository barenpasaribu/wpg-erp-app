<?php
    require_once 'includes/header.php';
?>
 <div class="page-wrapper">

             <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Dashboard</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    

                     <!-- Column -->
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-primary text-center">
                            	<a href="<?=base_url()?>datakaryawan/data_tampil" >
                                <h1 class="font-light text-white"><i class="fa fa-users"></i></h1>
                                <h6 class="text-white">Daftar Karyawan</h6>
                            </a>
                            </div>
                        </div>
                    </div>

                     <div class="col-md-6 col-lg-2 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-warning text-center">
                                <a href="<?=base_url()?>kodeblok/data_tampil" >
                                <h1 class="font-light text-white"><i class="mdi mdi-cube"></i></h1>
                                <h6 class="text-white">Daftar Kodeblok</h6>
                            </a>
                            </div>
                        </div>
                    </div>

                     <div class="col-md-6 col-lg-2 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <a href="<?=base_url()?>kodevhc/data_tampil" >
                                <h1 class="font-light text-white"><i class="fa fa-list"></i></h1>
                                <h6 class="text-white">Daftar Kodevhc</h6>
                            </a>
                            </div>
                        </div>
                    </div>

                     <div class="col-md-6 col-lg-2 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <a href="<?=base_url()?>job/data_tampil" >
                                <h1 class="font-light text-white"><i class="fa fa-list"></i></h1>
                                <h6 class="text-white">Daftar Job</h6>
                            </a>
                            </div>
                        </div>
                    </div>
                   

                  
                </div>

<?php
    require_once 'includes/footer.php';
?>