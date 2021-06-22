/**
 * @author 
 */

function lihatpdf(ev,tujuan,kode,karyawanid)
{
    // ati2, ni tembak langsung ke Pengajuan Training lo tujuannya
    judul='Report PDF';	
    param='karyawanid='+karyawanid+'&kamar=pdf'+'&kodetraining='+kode;
        printFile(param,tujuan,judul,ev)	        
}

function previewPdf(id,ev)
{
        
        param='method=prevPdf'+'&ids='+id;
        tujuan = 'sdm_slave_daftarPengajuanMatriksTraining.php?'+param;
 //display window
   title='Print PDF';
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='600';
   height='500';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1(title,content,width,height,ev); 	
}

function loadList()
{      num=0;
    namakry =document.getElementById('namaKry').value;
	 	param='&page='+num+'&namakry='+namakry;
		tujuan = 'sdm_slave_daftarPengajuanMatriksTraining.php';
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

function loadListKry()
{      
	num=0;
    namakry =document.getElementById('namaKry').value;
	param='&page='+num+'&namakry='+namakry;
	tujuan = 'sdm_slave_daftarPengajuanMatriksTraining.php';
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
	 namaKry 	=document.getElementById('namaKry').value;
		param='&page='+num;
		if(namaKry!='')
			param+='&namaKry='+namaKry;
		tujuan = 'sdm_slave_daftarPengajuanMatriksTraining.php';
		
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
