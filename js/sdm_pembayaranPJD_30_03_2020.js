/**
 * @author antoniuslouis
 */
function simpanPJD()
{
	karyawanid	= document.getElementById('karyawanid');
	karyawanid	=karyawanid.options[karyawanid.selectedIndex].value;
	kodeorg		= document.getElementById('kodeorg');
	kodeorg		=kodeorg.options[kodeorg.selectedIndex].value;
	persetujuan	= document.getElementById('persetujuan');
	persetujuan	=persetujuan.options[persetujuan.selectedIndex].value;	
	hrd		= document.getElementById('hrd');
	hrd		=hrd.options[hrd.selectedIndex].value;
	tujuan3	= document.getElementById('tujuan3');
	tujuan3	=tujuan3.options[tujuan3.selectedIndex].value;
	tujuan2		= document.getElementById('tujuan2');
	tujuan2		=tujuan2.options[tujuan2.selectedIndex].value;
	tujuan1	= document.getElementById('tujuan1');
	tujuan1	=tujuan1.options[tujuan1.selectedIndex].value;
	tanggalperjalanan		= trim(document.getElementById('tanggalperjalanan').value);
	tanggalkembali	= trim(document.getElementById('tanggalkembali').value);
	uangmuka			= remove_comma(document.getElementById('uangmuka'));
	tugas1	= trim(document.getElementById('tugas1').value);
	tugas2		= document.getElementById('tugas2').value;
	tugas3		= document.getElementById('tugas3').value;
	tujuanlain		= document.getElementById('tujuanlain').value;
	tugaslain		= document.getElementById('tugaslain').value;
	notransaksi		=document.getElementById('notransaksi').value;
    method		= document.getElementById('method').value;
	if(document.getElementById('pesawat').checked==true)
	   pesawat=1;
	else
	   pesawat=0;   
	if(document.getElementById('darat').checked==true)
	   darat=1;
	else
	   darat=0; 
	if(document.getElementById('laut').checked==true)
	   laut=1;
	else
	   laut=0;
	if(document.getElementById('mess').checked==true)
	   mess=1;
	else
	   mess=0;
	if(document.getElementById('hotel').checked==true)
	   hotel=1;
	else
	   hotel=0;
	
 
		if (karyawanid == '' || kodeorg == '' || persetujuan == '' || hrd == '' || tujuan1 == '' || tanggalperjalanan=='') {
			alert(' Employee, Org.Code, Traveling date, Approval, first destination are obligatory');
		}
		else {
			param ='karyawanid='+karyawanid+'&kodeorg='+kodeorg;
			param +='&persetujuan='+persetujuan+'&hrd='+hrd; 
			param +='&tujuan3='+tujuan3+'&tujuan2='+tujuan2;	
			param +='&tujuan1='+tujuan1+'&tanggalperjalanan='+tanggalperjalanan;
			param +='&tanggalkembali='+tanggalkembali+'&uangmuka='+uangmuka;
			param +='&tugas1='+tugas1+'&tugas2='+tugas2;
			param +='&tugas3='+tugas3+'&tujuanlain='+tujuanlain;
			param +='&tugaslain='+tugaslain+'&pesawat='+pesawat;
			param +='&darat='+darat+'&laut='+laut;
			param +='&mess='+mess+'&hotel='+hotel;		
			param += '&method='+method+'&notransaksi='+notransaksi;
			if (confirm('Saving, are you sure..?')) {
				tujuan = 'sdm_slave_savePJDinas.php';
				post_response_text(tujuan, param, respog);
			}
		}
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					alert('Saved');
					clearForm();
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
		tujuan = 'sdm_getPJDinasPembayaranList.php';
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
		tujuan = 'sdm_getPJDinasPembayaranList.php';
		
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
function previewPJD(nosk,ev)
{
   	param='notransaksi='+nosk;
	tujuan = 'sdm_slave_printPJD_pdf.php?'+param;	
 //display window
   title=nosk;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
   
}

function saveBayarPJD(no,notransaksi)
{
	bayar=remove_comma(document.getElementById('bayar'+no));
	tglbayar=document.getElementById('tglbayar'+no).value;
	if(bayar=='' || trim(tglbayar).length==0)
	{
		alert('Value and date are obligatory');
	}
	else
	{
		param='bayar='+bayar+'&tglbayar='+tglbayar+'&notransaksi='+notransaksi;
		tujuan='sdm_slave_save_uangmukaPJD.php';
	}
	if(confirm('Saving, are you sure..?'))
	{
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
						alert('Saved');
						document.getElementById('bayar' + no).style.backgroundColor = '#dedede';
						document.getElementById('tglbayar' + no).style.backgroundColor = '#dedede';

						document.getElementById('bayar' + no).disabled = true;
						document.getElementById('tglbayar' + no).disabled = true;
						document.getElementById('btnSave').style.display = 'none';
						document.getElementById('btnGet').style.display = 'none';
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}			
}  
