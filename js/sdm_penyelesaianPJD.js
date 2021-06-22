/**
 * @author antoniuslouis
 */

 function simpan_tanggal(notransaksi, nourut) {
	tanggal = document.getElementById('tanggal_penyelesaian_' + nourut).value;
	param ='notransaksi='+notransaksi+'&tanggal_penyelesaian='+tanggal+'&proses=simpanTanggalPenyelesaian';
	console.log(param);
	tujuan = 'sdm_slave_get_save_data.php';
	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						data = con.responseText;
						if (data == null || data == "0000-00-00") {
							document.getElementById('tanggal_penyelesaian_' + nourut).value = "";
							document.getElementById('tanggal_penyelesaian_' + nourut).disabled = false;
							document.getElementById('gambarsimpan').style.display = "block";
						}else{
							document.getElementById('tanggal_penyelesaian_' + nourut).value = data;
							document.getElementById('tanggal_penyelesaian_' + nourut).disabled = true;
							var myobj = document.getElementById("gambarsimpan");
  							myobj.remove();
							document.getElementById('gambarsimpan').style.display = "none";
						}
					}
				}
				else {
						busy_off();
						error_catch(con.status);
				}
		}
	}
 }

function previewPJD(nosk,ev)
{
   	param='notransaksi='+nosk;
	tujuan = 'sdm_slave_printPtjwbPJD_pdf.php?'+param;	
 //display window
   title=nosk;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
   
}

function savePenyelesaianPJD(notransaksi,sisa)
{
    bytiket=document.getElementById('t'+notransaksi).value;
    if(bytiket=='')
        bytiket=0;
    
    param ='notransaksi='+notransaksi+'&sisa='+sisa+'&bytiket='+bytiket;
    if (confirm('Saving, are you sure..?')) {
            tujuan = 'sdm_slave_savePenyelesaianPJD.php';
            post_response_text(tujuan, param, respog);
    }

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    loadList();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
		
}

function loadList()
{      num=0;
            param='&page='+num;
            tujuan = 'sdm_getPenyelesaianPJDList.php';
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
	tex=trim(document.getElementById('txtbabp').value);
		param='&page='+num;
		if(tex!='')
			param+='&tex='+tex;
		tujuan = 'sdm_getPenyelesaianPJDList.php';
		
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

