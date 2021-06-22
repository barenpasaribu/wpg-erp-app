<?php
session_start();//to make sure all session is destroyed
	        //turn on Addtype application/x-php	.html on your apache config
session_destroy();
require_once('config/connection.php');
//echo $dbname;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Anthesis - Plantation ERP</title>
</head>
<!--link rel=stylesheet type='text/css' href='style/generic.css'-->
<script language='javascript' src='js/jquery-3.2.1.min.js'></script>
<script language='javascript' src='js/generic.js'></script>
<script language='javascript' src='js/drag.js'></script>
<script language='javascript' src='js/login.js'></script>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="bootstrap/css/style.css" rel="stylesheet">
<link href="bootstrap/css/login.css" rel="stylesheet">
<style>
.panel-heading {
    color: #333;
    background-color: #3D49C0;
}
.footversion{
	text-align:center;
	font-size:11px;
}
</style>
<body>

<div class="col-md-4"></div> 
<form id="frmLogin" name="frmLogin" action="#" method="POST" class="form-horizontal">   
<div class="col-md-4 form-login">
    <div class="panel panel-default">
        <div class="panel-heading" style="background-color: #3D49C0; color:#fff;">
            <span class="glyphicon glyphicon-user"></span>  WILIAN PERKASA - ANTHESIS ERP
        </div>
        <div align="center">
            <img src="images/WPG logo.png" height="150">
        </div>
        <div id="msg" align="center"></div>
        <div class="panel-body">
            <div class="form-group">                        
                <label class="col-sm-2 control-label" for="inputEmail3">Nama</label>
                <div class="col-sm-10">
                    <input type="text" id="username" name="login_name" autofocus="autofocus" autocomplete="off" size="20" onKeyPress="return enter(event);" class="form-control">
                </div>
            </div>
                        
            <div class="form-group">
					<label class="col-sm-2 control-label" for="inputEmail3">Password</label>
                <div class="col-sm-10">
                    <input type="password" id="password" name="login_password" autocomplete="off" size="20" onKeyPress="return enter(event);" class="form-control">  
                </div>
            </div>
            
            <div class="form-group">
					<label class="col-sm-2 control-label" for="inputEmail3">Bahasa</label>
                <div class="col-sm-10">
<select style="width: 145px" id='language' class="form-control">
<?php
$str="select * from ".$dbname.".namabahasa order by code";
$res=mysql_query($str);

echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
  echo "<option value='".$bar->code."'";
  # Default Language
  if($bar->code=='ID') {
	echo " selected";
  }
  echo ">".$bar->name."</option>";
}

?>

	</select>                      
                </div>
            </div>
			
        <div class="form-group last">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type='button' value='Log In' onclick='login()' class="btn btn-primary">
                </div>
            </div>                              
        </div>
        
        <div class="footversion">
           Copyright ANTHESIS-ERP &copy; <?php echo date('Y');?>. All rights reserved. 
        </div>
    </div>
</div>
</form>
<div class="col-md-4"></div>

<div id='progress' style='display:none;border:orange solid 1px;width:150px;position:fixed;right:20px;top:100px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
Sedang Proses...! <br>
<img src='images/progress.gif'>
</div>
<div id='screenlocker' style='display:none; width:100%;height:0px;color:#666666;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
</div>
<div id='locker' style='display:none; width:100%;height:0px;color:#666666;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>
</div>
</body>
</html>
