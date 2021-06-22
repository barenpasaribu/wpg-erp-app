// START PROCESS DATA CSV //
function PilihData(){
	var method 		 = 'PilihData'; 
	var PeriodeAbsen = document.getElementById('periodeabsen').options[document.getElementById('periodeabsen').selectedIndex].value; 
	var Perusahaan	 = document.getElementById('perusahaan').options[document.getElementById('perusahaan').selectedIndex].value; 
	var Bagian		 = document.getElementById('bagian').options[document.getElementById('bagian').selectedIndex].value; 
	var Karyawan	 = document.getElementById('karyawan').options[document.getElementById('karyawan').selectedIndex].value; 

	var param	= 'method='+method+'&PeriodeAbsen='+PeriodeAbsen+'&Perusahaan='+Perusahaan+'&Bagian='+Bagian+'&Karyawan='+Karyawan;
	var tujuan 	= 'sdm_slave_2rekapabsen2.php';
	post_response_text(tujuan, param, respog);

	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					} else {
						var res = document.getElementById('printContainer');
						res.innerHTML = con.responseText;
					}
			}
			else {
				alert('ERROR TRANSACTION,\n' + con.responseText);
				error_catch(con.status);
				busy_off();
			}
		}
	}
}

function KaryawanByDepartemen(){
	Perusahaan = document.getElementById('perusahaan').options[document.getElementById('perusahaan').selectedIndex].value;
    Departemen = document.getElementById('bagian').options[document.getElementById('bagian').selectedIndex].value;
	Proses = 'KaryawanByDepartemen';
	
	param = 'Perusahaan='+Perusahaan+'&Departemen='+Departemen+'&Proses='+Proses;
    tujuan ='sdm_slave_get_data.php';
    post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
							document.getElementById('karyawan').innerHTML=con.responseText;
					}
			}
			else {
					busy_off();
					error_catch(con.status);
			}
		}
	}
}

// END PROCESS DATA CSV //

function ListKaryawan(title,ev){
	
	param	= 'type=ListKaryawan';
	tujuan 	= 'sdm_slave_cari.php';
	post_response_text(tujuan, param, respog);	
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					content=  "<div>";
					content+= "<fieldset>Nama Karyawan:<input type=text id=NamaKaryawan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25>";
					content+= "<button class=mybutton onclick=CariKaryawan()>Go</button> </fieldset>";
					content+= "<div id=containercari style=\"height:400px;width:600px;overflow:scroll;\">";
					content+= "</div></div>";
					title=title+' Nama Karyawan:';
					width='600';
					height='400';
					showDialog1(title,content,width,height,ev);	
					document.getElementById('containercari').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	   
}

function CariKaryawan(){
	var NamaKaryawan = trim(document.getElementById('NamaKaryawan').value);
	param	= 'type=CariKaryawan&NamaKaryawan='+NamaKaryawan;
	tujuan 	= 'sdm_slave_cari.php';
	post_response_text(tujuan, param, respog);	
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('containercari').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	   
}

function PickKaryawan(id){
	document.getElementById('karyawanid').value=id;
	closeDialog();
}

function DownloadExcel(ev){
    var PeriodeAbsen = document.getElementById('periodeabsen').options[document.getElementById('periodeabsen').selectedIndex].value; 
	var Perusahaan	 = document.getElementById('perusahaan').options[document.getElementById('perusahaan').selectedIndex].value; 
	var Bagian		 = document.getElementById('bagian').options[document.getElementById('bagian').selectedIndex].value; 
	var Karyawan	 = document.getElementById('karyawan').options[document.getElementById('karyawan').selectedIndex].value; 
	var type = 'excel';
	var method = 'DownloadExcel';
	
	judul	= 'Laporan Absensi '+PeriodeAbsen;
	var param	= 'method='+method+'&PeriodeAbsen='+PeriodeAbsen+'&Perusahaan='+Perusahaan+'&Bagian='+Bagian+'&Karyawan='+Karyawan;
	var tujuan 	= 'sdm_slave_print_rekapabsen.php';
	printFile(param,tujuan,judul,ev);
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}