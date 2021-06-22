/**
 * @author antoniuslouis
 */
///response file
/*
 * 			echo"<?xml version='1.0' ?>
			     <karyawan>
				 <karyawanid>".$karid."</karyawanid>
				 <namakaryawan>".$nama."</namakaryawan>
				 </karyawan>";
 * 
 * 
 */
//============
/*PARSER
 * 	xml=tex.toString();
	xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");
    getId=xmlobject.getElementsByTagName('karyawanid')[0].firstChild.nodeValue;
	getNama=xmlobject.getElementsByTagName('namakaryawan')[0].firstChild.nodeValue;

 * 
 */
function getKarStat(karid)
{
	if(karid=='')
	{}
	else
	{
		param='karid='+karid;
		tujuan = 'sdm_slave_getPromosiCurStatus.php';
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

	kodejabatan	=xmlobject.getElementsByTagName('kodejabatan')[0].firstChild.nodeValue;
     	kodejabatan=kodejabatan.replace("*","");
	kodegolongan=xmlobject.getElementsByTagName('kodegolongan')[0].firstChild.nodeValue;
     	kodegolongan=kodegolongan.replace("*","");
	lokasitugas	=xmlobject.getElementsByTagName('lokasitugas')[0].firstChild.nodeValue;
     	lokasitugas=lokasitugas.replace("*","");
        tipekaryawan=xmlobject.getElementsByTagName('tipekaryawan')[0].firstChild.nodeValue;
     	tipekaryawan=tipekaryawan.replace("*","");
        bagian=xmlobject.getElementsByTagName('bagian')[0].firstChild.nodeValue;
     	bagian=bagian.replace("*","");
	
	jk=document.getElementById('oldokasitugas');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==lokasitugas)
			{
				jk.options[x].selected=true;
			}
		}
		
	jk=document.getElementById('oldjabatan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==kodejabatan)
			{
				jk.options[x].selected=true;
			}
		}	
	jk=document.getElementById('oldtipekaryawan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==tipekaryawan)
			{
				jk.options[x].selected=true;
			}
		}
	jk=document.getElementById('oldgolongan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==kodegolongan)
			{
				jk.options[x].selected=true;
			}
		}
	jk=document.getElementById('olddepartemen');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==bagian)
			{
				jk.options[x].selected=true;
			}
		}                
}


function editSK(notransaksi,karyawanid)
{
		param='karid='+karyawanid+'&notransaksi='+notransaksi;
		tujuan = 'sdm_slave_getPromosiForEdit.php';
		post_response_text(tujuan, param, respog);		

	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					//alert(con.responseText);
					parseEdit(con.responseText);
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

function parseEdit(tex)
{
  	xml=tex.toString();
	xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");	

	tipesk	=xmlobject.getElementsByTagName('tipesk')[0].firstChild.nodeValue;
     	tipesk=tipesk.replace("*","");
	jk=document.getElementById('tipetransaksi');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==tipesk)
			{
				jk.options[x].selected=true;
			}
		}
	karyawanid	=xmlobject.getElementsByTagName('karyawanid')[0].firstChild.nodeValue;
     	karyawanid=karyawanid.replace("*","");
	jk=document.getElementById('karyawanid');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==karyawanid)
			{
				jk.options[x].selected=true;
			}
		}
		

	darikodeorg	=xmlobject.getElementsByTagName('darikodeorg')[0].firstChild.nodeValue;
     	darikodeorg=darikodeorg.replace("*","");
	jk=document.getElementById('oldokasitugas');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==darikodeorg)
			{
				jk.options[x].selected=true;
			}
		}
	darikodejabatan	=xmlobject.getElementsByTagName('darikodejabatan')[0].firstChild.nodeValue;
     	darikodejabatan=darikodejabatan.replace("*","");
	jk=document.getElementById('oldjabatan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==darikodejabatan)
			{
				jk.options[x].selected=true;
			}
		}
	daritipe	=xmlobject.getElementsByTagName('daritipe')[0].firstChild.nodeValue;
     	daritipe=daritipe.replace("*","");
	jk=document.getElementById('oldtipekaryawan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==daritipe)
			{
				jk.options[x].selected=true;
			}
		}

	darikodegolongan	=xmlobject.getElementsByTagName('darikodegolongan')[0].firstChild.nodeValue;
     	darikodegolongan=darikodegolongan.replace("*","");
	jk=document.getElementById('oldgolongan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==darikodegolongan)
			{
				jk.options[x].selected=true;
			}
		}

	kekodeorg	=xmlobject.getElementsByTagName('kekodeorg')[0].firstChild.nodeValue;
     	kekodeorg=kekodeorg.replace("*","");
	jk=document.getElementById('newlokasitugas');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==kekodeorg)
			{
				jk.options[x].selected=true;
			}
		}		

	kekodejabatan	=xmlobject.getElementsByTagName('kekodejabatan')[0].firstChild.nodeValue;
     	kekodejabatan=kekodejabatan.replace("*","");
	jk=document.getElementById('newjabatan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==kekodejabatan)
			{
				jk.options[x].selected=true;
			}
		}
	ketipekaryawan	=xmlobject.getElementsByTagName('ketipekaryawan')[0].firstChild.nodeValue;
     	ketipekaryawan=ketipekaryawan.replace("*","");
	jk=document.getElementById('newtipekaryawan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==ketipekaryawan)
			{
				jk.options[x].selected=true;
			}
		}		

	kekodegolongan	=xmlobject.getElementsByTagName('kekodegolongan')[0].firstChild.nodeValue;
     	kekodegolongan=kekodegolongan.replace("*","");
	jk=document.getElementById('newgolongan');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==kekodegolongan)
			{
				jk.options[x].selected=true;
			}
		}		

	atasanbaru	=xmlobject.getElementsByTagName('atasanbaru')[0].firstChild.nodeValue;
     	atasanbaru=atasanbaru.replace("*","");
	jk=document.getElementById('atasanbaru');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==atasanbaru)
			{
				jk.options[x].selected=true;
			}
		}	
	bagian	=xmlobject.getElementsByTagName('bagian')[0].firstChild.nodeValue;
     	bagian=bagian.replace("*","");
	jk=document.getElementById('olddepartemen');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==bagian)
			{
				jk.options[x].selected=true;
			}
		}
	kebagian=xmlobject.getElementsByTagName('kebagian')[0].firstChild.nodeValue;
     	kebagian=kebagian.replace("*","");
	jk=document.getElementById('newdepartemen');
		for(x=0;x<jk.length;x++)
		{
			if(jk.options[x].value==kebagian)
			{
				jk.options[x].selected=true;
			}
		}                

//========update flag================
	document.getElementById('method').value='update';
	nomorsk	=xmlobject.getElementsByTagName('nomorsk')[0].firstChild.nodeValue;
    nomorsk=nomorsk.replace("*","");
    document.getElementById('nosk').value=nomorsk;
//======================
	tanggalsk	=xmlobject.getElementsByTagName('tanggalsk')[0].firstChild.nodeValue;
    tanggalsk=tanggalsk.replace("*","");
    document.getElementById('tanggalsk').value=tanggalsk;

	mulaiberlaku	=xmlobject.getElementsByTagName('mulaiberlaku')[0].firstChild.nodeValue;
    mulaiberlaku=mulaiberlaku.replace("*","");
    document.getElementById('tanggalberlaku').value=mulaiberlaku;
	
	namadireksi	=xmlobject.getElementsByTagName('namadireksi')[0].firstChild.nodeValue;
    namadireksi=namadireksi.replace("*","");
    document.getElementById('penandatangan').value=namadireksi;

	tembusan1	=xmlobject.getElementsByTagName('tembusan1')[0].firstChild.nodeValue;
    tembusan1=tembusan1.replace("*","");
    document.getElementById('tembusan1').value=tembusan1;
		
	tembusan2	=xmlobject.getElementsByTagName('tembusan2')[0].firstChild.nodeValue;
    tembusan2=tembusan2.replace("*","");
    document.getElementById('tembusan2').value=tembusan2;
	
	tembusan3	=xmlobject.getElementsByTagName('tembusan3')[0].firstChild.nodeValue;
    tembusan3=tembusan3.replace("*","");
    document.getElementById('tembusan3').value=tembusan3;

	tembusan4	=xmlobject.getElementsByTagName('tembusan4')[0].firstChild.nodeValue;
    tembusan4=tembusan4.replace("*","");
    document.getElementById('tembusan4').value=tembusan4;

	tembusan5	=xmlobject.getElementsByTagName('tembusan5')[0].firstChild.nodeValue;
    tembusan5=tembusan5.replace("*","");
    document.getElementById('tembusan5').value=tembusan5;
	
	darigaji	=xmlobject.getElementsByTagName('darigaji')[0].firstChild.nodeValue;
    darigaji=darigaji.replace("*","");
    document.getElementById('oldgaji').value=darigaji;

	newgaji	=xmlobject.getElementsByTagName('kegaji')[0].firstChild.nodeValue;
    newgaji=newgaji.replace("*","");
    document.getElementById('newgaji').value=newgaji;

	tjjabatan	=xmlobject.getElementsByTagName('tjjabatan')[0].firstChild.nodeValue;
    tjjabatan=tjjabatan.replace("*","");
    document.getElementById('tjjabatan').value=tjjabatan;

	ketjjabatan	=xmlobject.getElementsByTagName('ketjjabatan')[0].firstChild.nodeValue;
    ketjjabatan=ketjjabatan.replace("*","");
    document.getElementById('ketjjabatan').value=ketjjabatan;
    
        tjsdaerah	=xmlobject.getElementsByTagName('tjsdaerah')[0].firstChild.nodeValue;
    tjsdaerah=tjsdaerah.replace("*","");
    document.getElementById('tjsdaerah').value=tjsdaerah;

	ketjsdaerah	=xmlobject.getElementsByTagName('ketjsdaerah')[0].firstChild.nodeValue;
    ketjsdaerah=ketjsdaerah.replace("*","");
    document.getElementById('ketjsdaerah').value=ketjsdaerah;
    
	tjmahal	=xmlobject.getElementsByTagName('tjmahal')[0].firstChild.nodeValue;
    tjmahal=tjmahal.replace("*","");
    document.getElementById('tjmahal').value=tjmahal;
    
	ketjmahal	=xmlobject.getElementsByTagName('ketjmahal')[0].firstChild.nodeValue;
    ketjmahal=ketjmahal.replace("*","");
    document.getElementById('ketjmahal').value=ketjmahal;
    
    
	tjpembantu	=xmlobject.getElementsByTagName('tjpembantu')[0].firstChild.nodeValue;
    tjpembantu=tjpembantu.replace("*","");
    document.getElementById('tjpembantu').value=tjpembantu;
    
	ketjpembantu	=xmlobject.getElementsByTagName('ketjpembantu')[0].firstChild.nodeValue;
    ketjpembantu=ketjpembantu.replace("*","");
    document.getElementById('ketjpembantu').value=ketjpembantu;
    
 	tjkota	=xmlobject.getElementsByTagName('tjkota')[0].firstChild.nodeValue;
    tjkota=tjkota.replace("*","");
    document.getElementById('tjkota').value=tjkota;
    
	ketjkota	=xmlobject.getElementsByTagName('ketjkota')[0].firstChild.nodeValue;
    ketjkota=ketjkota.replace("*","");
    document.getElementById('ketjkota').value=ketjkota;
    
  	tjtransport	=xmlobject.getElementsByTagName('tjtransport')[0].firstChild.nodeValue;
    tjtransport=tjtransport.replace("*","");
    document.getElementById('tjtransport').value=tjtransport;
    
	ketjtransport	=xmlobject.getElementsByTagName('ketjtransport')[0].firstChild.nodeValue;
    ketjtransport=ketjtransport.replace("*","");
    document.getElementById('ketjtransport').value=ketjtransport;
    
  	tjmakan	=xmlobject.getElementsByTagName('tjmakan')[0].firstChild.nodeValue;
    tjmakan=tjmakan.replace("*","");
    document.getElementById('tjmakan').value=tjmakan;
    
	ketjmakan	=xmlobject.getElementsByTagName('ketjmakan')[0].firstChild.nodeValue;
    ketjmakan=ketjmakan.replace("*","");
    document.getElementById('ketjmakan').value=ketjmakan;    
     
    
    namajabatan	=xmlobject.getElementsByTagName('namajabatan')[0].firstChild.nodeValue;
    namajabatan=namajabatan.replace("*","");
    document.getElementById('namajabatan').value=namajabatan; 
	
        paragraf1	=xmlobject.getElementsByTagName('paragraf1')[0].firstChild.nodeValue;
    paragraf1=paragraf1.replace("*","");
    document.getElementById('paragraf1').value=paragraf1; 

        paragraf2	=xmlobject.getElementsByTagName('paragraf2')[0].firstChild.nodeValue;
    paragraf2=paragraf2.replace("*","");
    document.getElementById('paragraf2').value=paragraf2; 
    
  tabAction(document.getElementById('tabFRM0'),0,'FRM',1);//jangan tanya darimana
}

function savePromosi()
{
	tipetransaksi	= document.getElementById('tipetransaksi');
	tipetransaksi	=tipetransaksi.options[tipetransaksi.selectedIndex].value;
	karyawanid		= document.getElementById('karyawanid');
	karyawanid		=karyawanid.options[karyawanid.selectedIndex].value;
	oldokasitugas	= document.getElementById('oldokasitugas');
	oldokasitugas	=oldokasitugas.options[oldokasitugas.selectedIndex].value;
	oldjabatan		= document.getElementById('oldjabatan');
	oldjabatan		=oldjabatan.options[oldjabatan.selectedIndex].value;
	oldtipekaryawan	= document.getElementById('oldtipekaryawan');
	oldtipekaryawan	=oldtipekaryawan.options[oldtipekaryawan.selectedIndex].value;
	olddepartemen	= document.getElementById('olddepartemen');
	olddepartemen	=olddepartemen.options[olddepartemen.selectedIndex].value;
        oldgolongan		= document.getElementById('oldgolongan');
	oldgolongan		=oldgolongan.options[oldgolongan.selectedIndex].value;
	newlokasitugas	= document.getElementById('newlokasitugas');
	newlokasitugas	=newlokasitugas.options[newlokasitugas.selectedIndex].value;
	newjabatan		= document.getElementById('newjabatan');
	newjabatan		=newjabatan.options[newjabatan.selectedIndex].value;
	newtipekaryawan	= document.getElementById('newtipekaryawan');
	newtipekaryawan	=newtipekaryawan.options[newtipekaryawan.selectedIndex].value;
	newgolongan		= document.getElementById('newgolongan');
	newgolongan		=newgolongan.options[newgolongan.selectedIndex].value;
	atasanbaru		= document.getElementById('atasanbaru');
	atasanbaru		=atasanbaru.options[atasanbaru.selectedIndex].value;
	newdepartemen	= document.getElementById('newdepartemen');
	newdepartemen	=newdepartemen.options[newdepartemen.selectedIndex].value;
        
	tanggalsk		= trim(document.getElementById('tanggalsk').value);
	tanggalberlaku	= trim(document.getElementById('tanggalberlaku').value);
	oldgaji			= remove_comma(document.getElementById('oldgaji'));
	newgaji			= remove_comma(document.getElementById('newgaji'));
	penandatangan	= trim(document.getElementById('penandatangan').value);
	namajabatan		= trim(document.getElementById('namajabatan').value);
	tembusan1		= document.getElementById('tembusan1').value;
	tembusan2		= document.getElementById('tembusan2').value;
	tembusan3		= document.getElementById('tembusan3').value;
	tembusan4		= document.getElementById('tembusan4').value;
	tembusan5		= document.getElementById('tembusan5').value;
	method			= document.getElementById('method').value;
	noskedit		= document.getElementById('nosk').value;

	tjjabatan	= remove_comma(document.getElementById('tjjabatan'));
	ketjjabatan	= remove_comma(document.getElementById('ketjjabatan'));
        
	tjsdaerah	= remove_comma(document.getElementById('tjsdaerah'));
	ketjsdaerah	= remove_comma(document.getElementById('ketjsdaerah'));      
	ketjmahal	= remove_comma(document.getElementById('ketjmahal'));        
	tjmahal         = remove_comma(document.getElementById('tjmahal'));
 	tjpembantu	= remove_comma(document.getElementById('tjpembantu'));       
	ketjpembantu	= remove_comma(document.getElementById('ketjpembantu'));

	tjkota          = remove_comma(document.getElementById('tjkota'));
	ketjkota	= remove_comma(document.getElementById('ketjkota'));       
	tjtransport	= remove_comma(document.getElementById('tjtransport'));        
	ketjtransport   = remove_comma(document.getElementById('ketjtransport'));
 	tjmakan         = remove_comma(document.getElementById('tjmakan'));       
	ketjmakan	= remove_comma(document.getElementById('ketjmakan'));
        
        paragraf1	= document.getElementById('paragraf1').value;
	paragraf2	= document.getElementById('paragraf2').value;
	statustransaksi	= document.getElementById('statustransaksi').value;
/*
 * exception
 */
    oldgaji=oldgaji==''?0:oldgaji;
    newgaji=newgaji==''?0:newgaji;
    tjjabatan=tjjabatan==''?0:tjjabatan;
    ketjjabatan=ketjjabatan==''?0:ketjjabatan;
    tjsdaerah=tjsdaerah==''?0:tjsdaerah;
    ketjsdaerah=ketjsdaerah==''?0:ketjsdaerah;
    tjmahal=tjmahal==''?0:tjmahal;
    ketjmahal=ketjmahal==''?0:ketjmahal; 
    tjpembantu=tjpembantu==''?0:tjpembantu;
    ketjpembantu=ketjpembantu==''?0:ketjpembantu
    tjkota=tjkota==''?0:tjkota;
    ketjkota=ketjkota==''?0:ketjkota; 
    tjtransport=tjtransport==''?0:tjtransport;
    ketjtransport=ketjtransport==''?0:ketjtransport; 
    tjmakan=tjmakan==''?0:tjmakan;
    ketjmakan=ketjmakan==''?0:ketjmakan;

	if (tipetransaksi == '' || karyawanid == '' || tanggalsk == '' || tanggalberlaku == '' || penandatangan == '') {
			alert('Transaction type, Employee, Doc.Date, Effective Date and Signer are obligatory');
		}
		else {
			param = 'tanggalsk=' + tanggalsk + '&tanggalberlaku=' + tanggalberlaku;
			param += '&oldgaji=' + oldgaji + '&newgaji=' + newgaji + '&penandatangan=' + penandatangan;
			param += '&tembusan1=' + tembusan1 + '&tembusan2=' + tembusan2 + '&tembusan3=' + tembusan3;
			param += '&tembusan4=' + tembusan4 + '&tipetransaksi=' + tipetransaksi;
			param += '&karyawanid=' + karyawanid + '&oldokasitugas=' + oldokasitugas;
			param += '&oldjabatan=' + oldjabatan + '&oldtipekaryawan=' + oldtipekaryawan;
			param += '&oldgolongan=' + oldgolongan + '&newlokasitugas=' + newlokasitugas;
			param += '&newjabatan=' + newjabatan + '&newgolongan=' + newgolongan;
			param += '&method='+method + '&newtipekaryawan=' + newtipekaryawan;
			param += '&nosk='+noskedit+'&namajabatan='+namajabatan;
			param += '&tjjabatan=' + tjjabatan + '&ketjjabatan=' + ketjjabatan;
                        param += '&olddepartemen=' + olddepartemen + '&newdepartemen=' + newdepartemen;
                        
                        param += '&tjsdaerah=' + tjsdaerah + '&ketjsdaerah=' + ketjsdaerah;
                        param += '&tjmahal=' + tjmahal + '&ketjmahal=' + ketjmahal;
                        param += '&tjpembantu=' + tjpembantu + '&ketjpembantu=' + ketjpembantu;                        
                        param += '&tjkota=' + tjkota + '&ketjkota=' + ketjkota; 
                        param += '&tjtransport=' + tjtransport + '&ketjtransport=' + ketjtransport; 
                        param += '&tjmakan=' + tjmakan + '&ketjmakan=' + ketjmakan; 
                        
			param += '&tembusan5=' + tembusan5+'&atasanbaru='+atasanbaru;
                        param += '&paragraf1='+paragraf1+'&paragraf2='+paragraf2+'&statustransaksi='+statustransaksi;
			
			if (confirm('Saving, are you sure..?')) {
				tujuan = 'sdm_slave_savePromosi.php';
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
	tipetransaksi	= document.getElementById('tipetransaksi');
	tipetransaksi.options[0].selected=true;
	karyawanid		= document.getElementById('karyawanid');
	karyawanid.options[0].selected=true;
	oldokasitugas	= document.getElementById('oldokasitugas');
	oldokasitugas.options[0].selected=true;
	oldjabatan		= document.getElementById('oldjabatan');
	oldjabatan.options[0].selected=true;
	oldtipekaryawan	= document.getElementById('oldtipekaryawan');
	oldtipekaryawan.options[0].selected=true;
	oldgolongan		= document.getElementById('oldgolongan');
	oldgolongan.options[0].selected=true;
	newlokasitugas	= document.getElementById('newlokasitugas');
	newlokasitugas.options[0].selected=true;
	newjabatan		= document.getElementById('newjabatan');
	newjabatan.options[0].selected=true;
	newtipekaryawan	= document.getElementById('newtipekaryawan');
	newtipekaryawan.options[0].selected=true;
	newgolongan	= document.getElementById('newgolongan');
	newgolongan.options[0].selected=true;
        
	//document.getElementById('tanggalsk').value='';
	//document.getElementById('tanggalberlaku').value='';
	document.getElementById('oldgaji').value=0;
	document.getElementById('newgaji').value=0;
	document.getElementById('tjjabatan').value=0;
	document.getElementById('ketjjabatan').value=0;        
	//document.getElementById('penandatangan').value='';
	//document.getElementById('tembusan1').value='';
	//document.getElementById('tembusan2').value='';
	//document.getElementById('tembusan3').value='';
	//document.getElementById('tembusan4').value='';	
	 //document.getElementById('tembusan5').value='';
	
	document.getElementById('tjsdaerah').value=0;
	document.getElementById('ketjsdaerah').value=0;
	document.getElementById('tjmahal').value=0;
	document.getElementById('ketjmahal').value=0;
	document.getElementById('tjpembantu').value=0;
	document.getElementById('ketjpembantu').value=0;	
                        
	document.getElementById('tjkota').value=0;
	document.getElementById('ketjkota').value=0;
	document.getElementById('tjtransport').value=0;
	document.getElementById('ketjtransport').value=0;
	document.getElementById('tjmakan').value=0;
	document.getElementById('ketjmakan').value=0;     
        
	document.getElementById('method').value='insert';
	document.getElementById('nosk').value='';
	document.getElementById('paragraf1').value='';
	document.getElementById('paragraf2').value='';
	document.getElementById('tipetransaksi').options[0].selected=true;
	
};
function loadList()
{num=0;
	 	param='&page='+num;
		tujuan = 'sdm_slave_getPromosiList.php';
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
					
function cariSK(num)
{
	tex=trim(document.getElementById('txtbabp').value);
		param='&page='+num;
		if(tex!='')
			param+='&tex='+tex;
		tujuan = 'sdm_slave_getPromosiList.php';
		
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

function delSK(nosk,karid)
{
        param='nosk='+nosk+'&method=delete&karyawanid='+karid;
		tujuan='sdm_slave_savePromosi.php';
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

function previewSK(nosk,ev)
{
   	param='nosk='+nosk;
	tujuan = 'sdm_slave_printSK_pdf.php?'+param;	
 //display window
   title=nosk;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
   
}

function getTjBaru()
{
        jabatan=document.getElementById('newjabatan');
        jabatan=jabatan.options[jabatan.selectedIndex].value;
        lokasitugas=document.getElementById('newlokasitugas');
        lokasitugas=lokasitugas.options[lokasitugas.selectedIndex].value;
        
        param='jabatan='+jabatan+'&lokasitugas='+lokasitugas;
        tujuan = 'sdm_slave_getPromosiTj.php';
        post_response_text(tujuan, param, respog);		

	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					//alert(con.responseText);
					parseTunjangan(con.responseText);
					getAtasan();
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}   
}

function getAtasan()
{
	newlokasitugas = document.getElementById('newlokasitugas').value;
	
	
	param='newlokasitugas='+newlokasitugas+'&proses=getAtasan';
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
					document.getElementById('atasanbaru').innerHTML = data;
                                       
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}   
}

function parseTunjangan(tex)
{
        xml=tex.toString();
        xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");
        ketjjabatan  =xmlobject.getElementsByTagName('tjjabatan')[0].firstChild.nodeValue;
        ketjjabatan=ketjjabatan.replace("*","");
        document.getElementById('ketjjabatan').value=ketjjabatan;
   
        ketjkota  =xmlobject.getElementsByTagName('tjkota')[0].firstChild.nodeValue;
        ketjkota=ketjkota.replace("*","");
        document.getElementById('ketjkota').value=ketjkota;

        ketjtransport  =xmlobject.getElementsByTagName('tjtransport')[0].firstChild.nodeValue;
        ketjtransport=ketjtransport.replace("*","");
        document.getElementById('ketjtransport').value=ketjtransport;

        ketjmakan  =xmlobject.getElementsByTagName('tjmakan')[0].firstChild.nodeValue;
        ketjmakan=ketjmakan.replace("*","");
        document.getElementById('ketjmakan').value=ketjmakan;

        ketjsdaerah  =xmlobject.getElementsByTagName('tjsdaerah')[0].firstChild.nodeValue;
        ketjsdaerah=ketjsdaerah.replace("*","");
        document.getElementById('ketjsdaerah').value=ketjsdaerah;

        ketjmahal  =xmlobject.getElementsByTagName('tjmahal')[0].firstChild.nodeValue;
        ketjmahal=ketjmahal.replace("*","");
        document.getElementById('ketjmahal').value=ketjmahal;

        ketjpembantu  =xmlobject.getElementsByTagName('tjpembantu')[0].firstChild.nodeValue;
        ketjpembantu=ketjpembantu.replace("*","");
        document.getElementById('ketjpembantu').value=ketjpembantu;
}
