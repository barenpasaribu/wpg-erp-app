<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('','<b>'.$_SESSION['lang']['komponenpayroll'].'</b>');
		echo"<div id=EList>";
		//echo OPEN_THEME($_SESSION['lang']['list'].':');
		echo"<table><tr><td>";
		$str="select * from ".$dbname.".sdm_ho_component order by id";
		$res=mysql_query($str);
		$no=0;
		echo"<table class=sortable width=450px cellspacing=1 border=0>
		     <thead>
			   <tr class=rowheader><td>No.</td><td>".$_SESSION['lang']['nama']."</td>
			   <td>Status</td><td>".$_SESSION['lang']['tipe']."</td>
			   <td>".$_SESSION['lang']['sumber']."</td>
			   <td>".$_SESSION['lang']['action']."</td></tr>
			 </thead>
			 <tbody id=tablebody>
			 ";
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			echo "<tr class=rowcontent><td class=fisttd>".$no."</td>
			      <td>".$bar->name."</td>
			      <td>".($bar->plus==1?$_SESSION['lang']['penambah']:$_SESSION['lang']['pengurang'])."</td>
				  <td>".$bar->type."</td>
				  <td>".($bar->lock==1?$_SESSION['lang']['dikunci']:$_SESSION['lang']['inputbebas'])."</td>
				  <td align=center><img src=images/tool.png class=dellicon title=Edit height=11px onclick=\"editComp('".$bar->id."','".$bar->name."','".$bar->plus."','".$bar->type."','".$bar->lock."')\"> 
				  <img src=images/close.png  height=11px class=dellicon title=Delete  onclick=\"delComp('".$bar->id."','".$bar->name."')\"></td>
				  </tr>";
		}
		echo"</tbody>
		     <tfoot>
			 </tfoot>
			 </table>";
		echo"</td>
		     <td valign=top>
			     <fieldset style='text-align:left'>
				   <legend><b><img src=images/info.png align=left height=35px valign=asmiddle>[Info]</b></legend>
                   ".$_SESSION['lang']['komponendeskripsi']." 
				 </fieldset>
				 			 
			     <fieldset style='text-align:left'>
				   <legend id=legend><b>".$_SESSION['lang']['new']."</b></legend>
				   <table><tr><td>
				   <input type=hidden value='' id=compid>
				   ".$_SESSION['lang']['namakomponen']."</td><td><input class=myinputtext type=text id=comp size=30 onkeypress=\"return tanpa_kutip(event)\">
				   </td></tr>
				   <tr><td>
				   ".$_SESSION['lang']['status']."</td><td><select id=plus>
				     <option value=1>".$_SESSION['lang']['penambah']."</option>
					 <option value=0>".$_SESSION['lang']['pengurang']."</option>
				   </select>
				   </td></tr>
				   <tr><td>				   
				   ".$_SESSION['lang']['tipe']."</td><td><select id=type>
				     <option value=basic>Basic</option>
					 <option value=additional>Additional</option>
				   </select>
				   </td></tr>
				   <tr><td>	
				    ".$_SESSION['lang']['tipeinput']."</td><td><select id=lock>
				     <option value=0>Free Entry</option>
					 <option value=1>Value From Other Source</option>
				   </select>
				   </td></tr>
				   </table>
				   <button class=mybutton onclick=saveComp()>".$_SESSION['lang']['save']."</button>	 
				   <button class=mybutton onclick=cancelComp()>".$_SESSION['lang']['cancel']."</button>	
				 </fieldset>
			 </td>
			 </tr>
			 </table>
			 ";	 
	   //	echo CLOSE_THEME();
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();

?>