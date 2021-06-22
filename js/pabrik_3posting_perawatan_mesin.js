// JavaScript Document
function postThis(notrans)
{ 
	noTrans=notrans;
	param='noTrans='+noTrans+'&proses=postThis';
	tujuan='pabrik_slave_3posting_perawatan_mesin.php';
	//alert(param);
	  function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//document.getElementById('trans_no').value = con.responseText;
					displayList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	if(confirm("Apakah anda yakin untuk posting ?"))
	{
		post_response_text(tujuan, param, respon);
	}
	
	
}
function displayList()
{
	document.getElementById('txtsearch').value='';
	document.getElementById('tgl_cari').value='';
	document.getElementById('statusPosting').value='';
	loadNData();
}



function loadNData()
{
	param='proses=loadData';
	tujuan='pabrik_slave_3posting_perawatan_mesin.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					document.getElementById('contain').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function cariBast(num)
{
		param='proses=loadData';
		param+='&page='+num;
		tujuan = 'pabrik_slave_3posting_perawatan_mesin.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('contain').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

function cariTransaksi()
{
	txtSearch=document.getElementById('txtsearch').value;
	txtTgl=document.getElementById('tgl_cari').value;
	statpost=document.getElementById('statusPosting').options[document.getElementById('statusPosting').selectedIndex].value;
	
	param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cariTransaksi'+'&statPost='+statpost;
	//alert(param);
	tujuan='pabrik_slave_3posting_perawatan_mesin.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('contain').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function dataKePDF(notrans,ev)
{
	noTrans	= notrans;
	tujuan='vhc_DetailPenggantianKomponen_pdf.php';
	judul= noTrans;		
	param='noTrans='+noTrans;
	//alert(param);
	printFile(param,tujuan,judul,ev)		
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function upDate()
{
	//alert("masuk");
	noTrans=document.getElementById('trans_no').value;
	tglSp=document.getElementById('tglCek').value;
	jmAwal=document.getElementById('jmAwal').value;
	jmAkhir=document.getElementById('jmAkhir').value;
	kgtn=document.getElementById('kegtn').value;
	param='noTrans='+noTrans+'&proses=upDate'+'&tgl='+tglSp+'&jmAwal='+jmAwal+'&jmAkhir='+jmAkhir+'&kgtn='+kgtn;
	//alert(param);
	
	tujuan = 'pabrikPemeliharaanmesin_slave.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					//document.getElementById('contain').innerHTML=con.responseText;
					displayList();
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}
