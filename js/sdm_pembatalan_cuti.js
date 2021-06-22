// START PROCESS DATA CSV //
function PilihData(){
	var method 		 = 'PilihData'; 
	var PeriodeAbsen = document.getElementById('periodeabsen').options[document.getElementById('periodeabsen').selectedIndex].value; 
	var Perusahaan	 = document.getElementById('perusahaan').options[document.getElementById('perusahaan').selectedIndex].value; 
	var Bagian		 = document.getElementById('bagian').options[document.getElementById('bagian').selectedIndex].value; 
	var Karyawan	 = document.getElementById('karyawan').options[document.getElementById('karyawan').selectedIndex].value; 

	var param	= 'method='+method+'&PeriodeAbsen='+PeriodeAbsen+'&Perusahaan='+Perusahaan+'&Bagian='+Bagian+'&Karyawan='+Karyawan;
	var tujuan 	= 'sdm_slave_pembatalan_cuti.php';
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

function AjukanBatalCuti(No){
	var karyawanid 	 = document.getElementById('karyawanid-'+No).value;
	var tanggal 	 = document.getElementById('tanggal-'+No).value;
	var persetujuan1 = document.getElementById('persetujuan1-'+No).value;
	var hrd 		 = document.getElementById('hrd-'+No).value;
	var namakaryawan = document.getElementById('namakaryawan-'+No).value;
	//tambah id untuk tabel sdm_ijin ==Jo 16-05-2017==
	var ids = document.getElementById('ids-'+No).value;
	var method		 = 'insert';
	
	param = 'karyawanid='+karyawanid+'&tanggal='+tanggal+'&persetujuan1='+persetujuan1+'&hrd='+hrd+'&namakaryawan='+namakaryawan+'&ids='+ids+'&method='+method;
    tujuan ='sdm_slave_save_pembatalan_cuti.php';
	
	if(confirm('Ajukan Pembatalan Cuti atas nama '+namakaryawan+', Pada tanggal '+tanggal+' ?')) {post_response_text(tujuan, param, respog);}
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
							alert('Pengajuan Pembatalan Cuti Berhasil');
							location.reload();
					}
			}
			else {
					busy_off();
					error_catch(con.status);
			}
		}
	}
}

function CancelAjuanBatalCuti(Id, No){
	var method		 = 'delete';
	var namakaryawan = document.getElementById('namakaryawan-'+No).value;
	var tanggal 	 = document.getElementById('tanggal-'+No).value;
	
	param = 'Id='+Id+'&method='+method;
    tujuan ='sdm_slave_save_pembatalan_cuti.php';

	if(confirm('Batalkan Permohonan atas nama '+namakaryawan+', Pada tanggal '+tanggal+' ?')) {post_response_text(tujuan, param, respog);}
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
							alert('Pembatalan Permohonan Berhasil');
							location.reload();
					}
			}
			else {
					busy_off();
					error_catch(con.status);
			}
		}
	}
}

function SetujuiAjuanBatalCuti(Id, No){
	var method		 = 'update';
	var namakaryawan = document.getElementById('namakaryawan-'+No).value;
	var tanggal 	 = document.getElementById('tanggal-'+No).value;
	var persetujuan1 = document.getElementById('persetujuan1-'+No).value;
	var hrd 		 = document.getElementById('hrd-'+No).value;
	var kodeorg 	 = document.getElementById('kodeorg-'+No).value;
	var karyawanid 	 = document.getElementById('karyawanid-'+No).value;
	var ids 	 = document.getElementById('ids-'+No).value;
	
	param = 'Id='+Id+'&method='+method+'&persetujuan1='+persetujuan1+'&hrd='+hrd+'&kodeorg='+kodeorg+'&karyawanid='+karyawanid+'&tanggal='+tanggal+'&ids='+ids;
 
   tujuan ='sdm_slave_save_pembatalan_cuti.php';
	
	if(confirm('Setujui Pembatalan Cuti atas nama '+namakaryawan+', Pada tanggal '+tanggal+' ?')) {post_response_text(tujuan, param, respog);}
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
							alert('Pengembalian Hak Cuti Berhasil');
							location.reload();
					}
			}
			else {
					busy_off();
					error_catch(con.status);
			}
		}
	}
}


function DownloadExcel(ev){
    var tgl01	= document.getElementById('tgl01').value;
    var tgl02  = document.getElementById('tgl02').value;
    var karyawanid = document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].value;
	var type = 'excel';
	var module = 'laporankehadiran';
	
	if(tgl01 == '' || tgl01 == null) {
		alert('Tanggal Mulai cannot empty');
		return;
	}
	
	if(tgl02 == '' || tgl02 == null) {
		alert('Tanggal Selesai cannot empty');
		return;
	}
	
	judul	= 'Laporan Absensi '+tgl01+'/'+tgl02;
	tujuan 	= 'sdm_slave_print.php';
	param	= 'type='+type+'&module='+module+'&tgl01='+tgl01+'&tgl02='+tgl02+'&karyawanid='+karyawanid;
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