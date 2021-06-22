/**
 * @author antoniuslouis
 */

function editPJD(notran,karid)
{
	if(karid=='')
	{}
	else
	{
		param='karid='+karid+'&notransaksi='+notran;
		tujuan = 'sdm_slave_getPJDinasForEdit.php';
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
					//alert(con.responseText);
					parseDong(con.responseText);
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}

function parseDong(tex)
{
  	xml=tex.toString();
	xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");	

	karyawanid	=xmlobject.getElementsByTagName('karyawanid')[0].firstChild.nodeValue;
     	karyawanid=karyawanid.replace("*","");
	kodeorg=xmlobject.getElementsByTagName('kodeorg')[0].firstChild.nodeValue;
     	kodeorg=kodeorg.replace("*","");
		
    persetujuan=xmlobject.getElementsByTagName('persetujuan')[0].firstChild.nodeValue;
     	persetujuan=persetujuan.replace("*","");
		
	persetujuan2=xmlobject.getElementsByTagName('persetujuan2')[0].firstChild.nodeValue;
	persetujuan2=persetujuan2.replace("*","");	
	
    hrd=xmlobject.getElementsByTagName('hrd')[0].firstChild.nodeValue;
     	hrd=hrd.replace("*","");
    tujuan3=xmlobject.getElementsByTagName('tujuan3')[0].firstChild.nodeValue;
     	tujuan3=tujuan3.replace("*","");		
    tujuan2=xmlobject.getElementsByTagName('tujuan2')[0].firstChild.nodeValue;
     	tujuan2=tujuan2.replace("*","");
    tujuan1=xmlobject.getElementsByTagName('tujuan1')[0].firstChild.nodeValue;
     	tujuan1=tujuan1.replace("*","");
    tanggalperjalanan=xmlobject.getElementsByTagName('tanggalperjalanan')[0].firstChild.nodeValue;
     	tanggalperjalanan=tanggalperjalanan.replace("*","");
    tanggalkembali=xmlobject.getElementsByTagName('tanggalkembali')[0].firstChild.nodeValue;
     	tanggalkembali=tanggalkembali.replace("*","");
    uangmuka=xmlobject.getElementsByTagName('uangmuka')[0].firstChild.nodeValue;
     	uangmuka=uangmuka.replace("*","");
    tugas1=xmlobject.getElementsByTagName('tugas1')[0].firstChild.nodeValue;
     	tugas1=tugas1.replace("*","");		
    tugas2=xmlobject.getElementsByTagName('tugas2')[0].firstChild.nodeValue;
     	tugas2=tugas2.replace("*","");
    tugas3=xmlobject.getElementsByTagName('tugas3')[0].firstChild.nodeValue;
     	tugas3=tugas3.replace("*","");		
    tujuanlain=xmlobject.getElementsByTagName('tujuanlain')[0].firstChild.nodeValue;
     	tujuanlain=tujuanlain.replace("*","");						
    tugaslain=xmlobject.getElementsByTagName('tugaslain')[0].firstChild.nodeValue;
     	tugaslain=tugaslain.replace("*","");	
    pesawat=xmlobject.getElementsByTagName('pesawat')[0].firstChild.nodeValue;
     	pesawat=pesawat.replace("*","");
    darat=xmlobject.getElementsByTagName('darat')[0].firstChild.nodeValue;
     	darat=darat.replace("*","");
    laut=xmlobject.getElementsByTagName('laut')[0].firstChild.nodeValue;
     	laut=laut.replace("*","");		
    mess=xmlobject.getElementsByTagName('mess')[0].firstChild.nodeValue;
     	mess=mess.replace("*","");		
    hotel=xmlobject.getElementsByTagName('hotel')[0].firstChild.nodeValue;
     	hotel=hotel.replace("*","");	
		
	mobilsewa=xmlobject.getElementsByTagName('mobilsewa')[0].firstChild.nodeValue;
     	mobilsewa=mobilsewa.replace("*","");		
	
	
	ket=xmlobject.getElementsByTagName('ket')[0].firstChild.nodeValue;
     	ket=ket.replace("*","");	
		
    notransaksi=xmlobject.getElementsByTagName('notransaksi')[0].firstChild.nodeValue;
     	notransaksi=notransaksi.replace("*","");	
		
	jk=document.getElementById('karyawanid');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==karyawanid)
			{
				jk.options[x].selected=true;
			}
		}
	jk=document.getElementById('kodeorg');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==kodeorg)
			{
				jk.options[x].selected=true;
			}
		}
	jk=document.getElementById('persetujuan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==persetujuan)
			{
				jk.options[x].selected=true;
			}
		}
		
	
		jk=document.getElementById('persetujuan2');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==persetujuan2)
			{
				jk.options[x].selected=true;
			}
		}	
		
	jk=document.getElementById('hrd');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==hrd)
			{
				jk.options[x].selected=true;
			}
		}
	jk=document.getElementById('tujuan3');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==tujuan3)
			{
				jk.options[x].selected=true;
			}
		}

	jk=document.getElementById('tujuan2');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==tujuan2)
			{
				jk.options[x].selected=true;
			}
		}		
	jk=document.getElementById('tujuan1');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==tujuan1)
			{
				jk.options[x].selected=true;
			}
		}

	if(parseInt(pesawat)==1)
	   	document.getElementById('pesawat').checked=true;
	else
	   	document.getElementById('pesawat').checked=false;
	if(parseInt(darat)==1)
	   	document.getElementById('darat').checked=true;
	else
	   	document.getElementById('darat').checked=false;
	if(parseInt(laut)==1)
	   	document.getElementById('laut').checked=true;
	else
	   	document.getElementById('laut').checked=false;
	if(parseInt(mess)==1)
	   	document.getElementById('mess').checked=true;
	else
	   	document.getElementById('mess').checked=false;
	if(parseInt(hotel)==1)
	   	document.getElementById('hotel').checked=true;
	else
	   	document.getElementById('hotel').checked=false;
		
	if(parseInt(mobilsewa)==1)
	   	document.getElementById('mobilsewa').checked=true;
	else
	   	document.getElementById('mobilsewa').checked=false;	
		
	   
	document.getElementById('tanggalperjalanan').value=tanggalperjalanan;
	document.getElementById('tanggalkembali').value=tanggalkembali;
	document.getElementById('uangmuka').value=uangmuka;
	document.getElementById('tugas1').value=tugas1;
	document.getElementById('tugas2').value=tugas2;
	document.getElementById('tugas3').value=tugas3;
	document.getElementById('tujuanlain').value=tujuanlain;
	document.getElementById('tugaslain').value=tugaslain;
	
	document.getElementById('ket').value=ket;
	
    document.getElementById('method').value='update';			
	document.getElementById('notransaksi').value=notransaksi;
    tabAction(document.getElementById('tabFRM0'),0,'FRM',1);					
}

function simpanPJD()
{
	ket=document.getElementById('ket').value;
	karyawanid	= document.getElementById('karyawanid');
	karyawanid	=karyawanid.options[karyawanid.selectedIndex].value;
	kodeorg		= document.getElementById('kodeorg');
	kodeorg		=kodeorg.options[kodeorg.selectedIndex].value;
	persetujuan	= document.getElementById('persetujuan');
	persetujuan	=persetujuan.options[persetujuan.selectedIndex].value;	
	
	persetujuan2	= document.getElementById('persetujuan2');
	persetujuan2	=persetujuan2.options[persetujuan2.selectedIndex].value;
	
	
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
	   
	 if(document.getElementById('mobilsewa').checked==true)
	   mobilsewa=1;
	else
	   mobilsewa=0;  
	 if(document.getElementById('mobildinas').checked==true)
	   mobildinas=1;
	else
	   mobildinas=0; 
	
 
                if (persetujuan2 == '') {
                    if (confirm('Persetujuan Atasan dari Atasan akan sama dengan Atasan. Anda yakin?')) {
                        persetujuan2 = persetujuan;
                    }
                }
		if (karyawanid == '' || kodeorg == ''  || persetujuan2 == '' || hrd == '' || tujuan1 == '' || tanggalperjalanan=='') {
			//if (karyawanid == '' || kodeorg == '' || persetujuan == '' || persetujuan2 == '' || hrd == '' || tujuan1 == '' || tanggalperjalanan=='') {
			alert(' Employee, Org.Code, Traveling date, Approval, Approval 2,  first destination are obligatory');
		}
		else {
			param ='karyawanid='+karyawanid+'&kodeorg='+kodeorg+'&ket='+ket;
			param +='&persetujuan='+persetujuan+'&persetujuan2='+persetujuan2+'&hrd='+hrd; 
			param +='&tujuan3='+tujuan3+'&tujuan2='+tujuan2;	
			param +='&tujuan1='+tujuan1+'&tanggalperjalanan='+tanggalperjalanan;
			param +='&tanggalkembali='+tanggalkembali+'&uangmuka='+uangmuka;
			param +='&tugas1='+tugas1+'&tugas2='+tugas2;
			param +='&tugas3='+tugas3+'&tujuanlain='+tujuanlain;
			param +='&tugaslain='+tugaslain+'&pesawat='+pesawat;
			param +='&darat='+darat+'&laut='+laut;
			param +='&mess='+mess+'&hotel='+hotel+'&mobilsewa='+mobilsewa+'&mobildinas='+mobildinas;
			param += '&method=' + method + '&notransaksi=' + notransaksi;
			//alert(param);
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

function clearForm()
{
	keterangan=document.getElementById('ket').value='';
	karyawanid	= document.getElementById('karyawanid');
	karyawanid.options[0].selected=true;
	kodeorg		= document.getElementById('kodeorg');
	kodeorg.options[0].selected=true;
	persetujuan	= document.getElementById('persetujuan');
	persetujuan.options[0].selected=true;	
	
		persetujuan2	= document.getElementById('persetujuan2');
	persetujuan2.options[0].selected=true;	
	
	hrd			= document.getElementById('hrd');
	hrd.options[0].selected=true;
	tujuan3		= document.getElementById('tujuan3');
	tujuan3.options[0].selected=true;
	tujuan2		= document.getElementById('tujuan2');
	tujuan2.options[0].selected=true;
	tujuan1		= document.getElementById('tujuan1');
	tujuan1.options[0].selected=true;
	document.getElementById('tanggalperjalanan').value='';
	document.getElementById('tanggalkembali').value='';
	document.getElementById('uangmuka').value=0;
	document.getElementById('tugas1').value='';
	document.getElementById('tugas2').value='';
	document.getElementById('tugas3').value='';
	document.getElementById('tujuanlain').value='';
	document.getElementById('tugaslain').value='';
    document.getElementById('method').value='';
	
	document.getElementById('pesawat').checked=false; 
	document.getElementById('darat').checked=false;
	document.getElementById('laut').checked=false;
	document.getElementById('mess').checked=false;
	document.getElementById('hotel').checked=false;
	document.getElementById('mobilsewa').checked=false;

};
function loadList()
{      num=0;
	 	param='&page='+num;
		tujuan = 'sdm_slave_getPJDinasiList.php';
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
		tujuan = 'sdm_slave_getPJDinasiList.php';
		
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

function delPJD(nosk,karid)
{
        param='notransaksi='+nosk+'&method=delete&karyawanid='+karid;
		tujuan='sdm_slave_savePJDinas.php';
		if(confirm('Deleting Document '+nosk+', are you sure..?'))
		  post_response_text(tujuan, param, respog);	
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

function ganti(keuser,kolom,notransaksi){
	
        param='notransaksi='+notransaksi+'&keuser='+keuser+'&kolom='+kolom;
		tujuan='sdm_slave_gantiPersetujuanPJDinas.php';
		if(confirm('Change Approval for '+notransaksi+', are you sure..?'))
                    post_response_text(tujuan, param, respog);	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
					    alert('Changed');
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
