/**
 * @author antoniuslouis
 */

function tolak(kode,karyawanid,sayaadalah,ev)
{
   param='kodetraining='+kode+'&karyawanid='+karyawanid+'&method=tolak&sayaadalah='+sayaadalah;
   tujuan='sdm_slave_daftarPengajuanTraining.php'+"?"+param;  
   width='600';
   height='250';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Penolakan '+sayaadalah,content,width,height,ev); 	
}

function setuju(kode,karyawanid,sayaadalah,ev)
{
   param='kodetraining='+kode+'&karyawanid='+karyawanid+'&method=setuju&sayaadalah='+sayaadalah;
   tujuan='sdm_slave_daftarPengajuanTraining.php'+"?"+param;  
   width='600';
   height='250';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Persetujuan '+sayaadalah,content,width,height,ev); 	
}

function lihatpdf(ev,tujuan,kode,karyawanid)
{
    // ati2, ni tembak langsung ke Pengajuan Training lo tujuannya
    judul='Report PDF';	
    param='karyawanid='+karyawanid+'&kamar=pdf'+'&kodetraining='+kode;
        printFile(param,tujuan,judul,ev)	        
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='600';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1(title,content,width,height,ev); 	
}

function alasanditolak(kodetraining,karyawanid,sayaadalah)
{
    alasannya=document.getElementById('alasannya').value;
	 	param='&kodetraining='+kodetraining+'&karyawanid='+karyawanid+'&sayaadalah='+sayaadalah+'&method=alasanditolak'+'&alasannya='+alasannya;
		tujuan = 'sdm_slave_daftarPengajuanTraining.php';
    if(confirm('Yakin?'))
parent.post_response_text(tujuan, param, respog);
	function respog(){
		if (parent.con.readyState == 4) {
			if (parent.con.status == 200) {
				parent.busy_off();
				if (!parent.isSaveResponse(parent.con.responseText)) {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
				}
				else {
					parent.document.getElementById('containerlist').innerHTML=parent.con.responseText;
                                        parent.closeDialog();
				}
			}
			else {
				parent.busy_off();
				parent.error_catch(parent.con.status);
			}
		}
	}				
}

function alasandisetujui(kodetraining,karyawanid,sayaadalah)
{
    alasannya=document.getElementById('alasannya').value;
	 	param='&kodetraining='+kodetraining+'&karyawanid='+karyawanid+'&sayaadalah='+sayaadalah+'&method=alasandisetujui'+'&alasannya='+alasannya;
		tujuan = 'sdm_slave_daftarPengajuanTraining.php';
    if(confirm('Yakin?'))
parent.post_response_text(tujuan, param, respog);
	function respog(){
		if (parent.con.readyState == 4) {
			if (parent.con.status == 200) {
				parent.busy_off();
				if (!parent.isSaveResponse(parent.con.responseText)) {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
				}
				else {
					parent.document.getElementById('containerlist').innerHTML=parent.con.responseText;
                                        parent.closeDialog();
				}
			}
			else {
				parent.busy_off();
				parent.error_catch(parent.con.status);
			}
		}
	}				
}


function loadList()
{      num=0;
    pilihkaryawan 	=document.getElementById('pilihkaryawan').options[document.getElementById('pilihkaryawan').selectedIndex].value;
	 	param='&page='+num+'&pilihkaryawan='+pilihkaryawan;
		tujuan = 'sdm_slave_daftarPengajuanTraining.php';
		post_response_text(tujuan, param, respog);
			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('containerlist').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}				
}
					
function cariPJD(num)
{
    pilihkaryawan 	=document.getElementById('pilihkaryawan').options[document.getElementById('pilihkaryawan').selectedIndex].value;
		param='&page='+num;
		if(pilihkaryawan!='')
			param+='&pilihkaryawan='+pilihkaryawan;
		tujuan = 'sdm_slave_daftarPengajuanTraining.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containerlist').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

//function previewPJD(nosk,ev)
//{
//   	param='notransaksi='+nosk;
//	tujuan = 'sdm_slave_printPJD_pdf.php?'+param;	
// //display window
//   title=nosk;
//   width='700';
//   height='400';
//   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   showDialog1(title,content,width,height,ev);
//   
//}
//
//function ganti(keuser,kolom,notransaksi){
//	
//        param='notransaksi='+notransaksi+'&keuser='+keuser+'&kolom='+kolom;
//		tujuan='sdm_slave_gantiPersetujuanPJDinas.php';
//		if(confirm('Change Approval for '+notransaksi+', are you sure..?'))
//		  post_response_text(tujuan, param, respog);	
//		function respog(){
//			if (con.readyState == 4) {
//				if (con.status == 200) {
//					busy_off();
//					if (!isSaveResponse(con.responseText)) {
//						alert('ERROR TRANSACTION,\n' + con.responseText);
//					}
//					else {
//					    alert('Changed');
//					}
//				}
//				else {
//					busy_off();
//					error_catch(con.status);
//				}
//			}
//		}	
//}
