function simpanProduksi()
{
	kodeorg 	=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
	tanggal  	=document.getElementById('tanggal').value;
	sisatbskemarin	=document.getElementById('sisatbskemarin').value;
	tbsmasuk	=document.getElementById('tbsmasuk').value;
	tbsdiolah	=document.getElementById('tbsdiolah').value;
	sisahariini	=document.getElementById('sisa').value;	
		
	oer			=document.getElementById('oercpo').value;	
	dirt		=document.getElementById('dirtcpo').value;
	kadarair	=document.getElementById('kadaraircpo').value;
	ffa			=document.getElementById('ffacpo').value;

	oerpk		=document.getElementById('oerpk').value;	
	dirtpk		=document.getElementById('dirtpk').value;
	kadarairpk	=document.getElementById('kadarairpk').value;
	ffapk		=document.getElementById('ffapk').value;
		
	if(kodeorg=='' ||  tanggal==''  || sisahariini=='' || sisahariini==null || sisatbskemarin=='' || sisatbskemarin==null || tbsmasuk=='' ||tbsmasuk==null || tbsdiolah=='' ||tbsdiolah==null || oer=='' ||oer==null || kadarair=='' ||kadarair==null || ffa==''  ||ffa==null  || dirt==''  ||dirt==null  || oerpk=='' ||oerpk==null || kadarairpk=='' ||kadarairpk==null || ffa==''  ||ffa==null  || dirtpk==''||dirtpk==null)
	{
		alert('Semua field harus diisi');
	}
	else
	{
		param='kodeorg='+kodeorg+'&tanggal='+tanggal;
		param+='&tbsmasuk='+tbsmasuk+'&tbsdiolah='+tbsdiolah;
		param+='&sisahariini='+sisahariini+'&sisatbskemarin='+sisatbskemarin;
		param+='&dirt='+dirt+'&kadarair='+kadarair;
		param+='&oer='+oer+'&ffa='+ffa;
		param+='&dirtpk='+dirtpk+'&kadarairpk='+kadarairpk;
		param+='&oerpk='+oerpk+'&ffapk='+ffapk;
		tujuan='pabrik_slave_save_produksi.php';
		post_response_text(tujuan, param, respog);		
	}

	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {;
							document.getElementById('container').innerHTML=con.responseText;
						   bersihkanForm();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 		
}

function bersihkanForm()
{
	document.getElementById('tanggal').value='';
	document.getElementById('sisatbskemarin').value='0';
	document.getElementById('tbsmasuk').value='0';
	document.getElementById('tbsdiolah').value='0';
	document.getElementById('sisa').value='0';
	
	document.getElementById('oercpo').value='0';
	document.getElementById('dirtcpo').value='0';
	document.getElementById('kadaraircpo').value='0';
	document.getElementById('ffacpo').value='0';
	document.getElementById('oerpk').value='0';
	document.getElementById('dirtpk').value='0';
	document.getElementById('kadarairpk').value='0';
	document.getElementById('ffapk').value='0';
}

function delProduksi(kodeorg,tanggal)
{
		param='kodeorg='+kodeorg+'&tanggal='+tanggal;
		param+='&del=true';
		if (confirm('Yakin akan hapus data ?')) {
			tujuan = 'pabrik_slave_save_produksi.php';
			post_response_text(tujuan, param, respog);
		}
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {;
							document.getElementById('container').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}

function hitungSisa()
{
	sisatbskemarin	=parseInt(document.getElementById('sisatbskemarin').value);
	tbsmasuk		=parseInt(document.getElementById('tbsmasuk').value);
	tbsdiolah		=parseInt(document.getElementById('tbsdiolah').value);
	sisa=(sisatbskemarin+tbsmasuk)-tbsdiolah;
	if (sisa >= 0) {
		document.getElementById('sisa').value = sisa;
	}
	else
	{
		alert('Angka salah');
		document.getElementById('tbsdiolah').value=0;
	}	
}

function periksaCPO(obj)
{
  	dirt		=parseFloat(document.getElementById('dirtcpo').value);
	kadarair	=parseFloat(document.getElementById('kadaraircpo').value);
	ffa			=parseFloat(document.getElementById('ffacpo').value);
	x=dirt+kadarair+ffa;
	if(x>50)//yang tidak terpakai lebih besar di dalam cpo
	{
		alert('Angka salah');
		obj.focus();
		obj.value=0;
				
	} 	
}
function periksaPK(obj)
{
	oerpk		=parseFloat(document.getElementById('oerpk').value);	
	dirtpk		=parseFloat(document.getElementById('dirtpk').value);
	kadarairpk	=parseFloat(document.getElementById('kadarairpk').value);
	ffapk		=parseFloat(document.getElementById('ffapk').value);	
	x=dirtpk+kadarairpk+ffapk;
	if(x>50)//yang tidak terpakai lebih besar di dalam pk
	{
		alert('Angka salah');
		obj.focus();
		obj.value=0;
		
	}	
}

function periksaOERCPO(obj)
{
	oercpo		=parseFloat(document.getElementById('oercpo').value);
	if(oercpo<1)
	{
		alert('Angka salah');
		obj.focus();
		obj.value=0;
		
	}	
}
function periksaOERPK(obj)
{
	oerpk		=parseFloat(document.getElementById('oerpk').value);
	if(oerpk<1)
	{
		alert('Angka salah');
		obj.focus();
		obj.value=0;
		
	}	
}

function getLaporanPrdPabrik()
{
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    tampil=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].text;
    pabrik=document.getElementById('pabrik').options[document.getElementById('pabrik').selectedIndex].text;
    param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;
    tujuan='pabrik_slave_3produksiHarian_v1.php';
	post_response_text(tujuan, param, respog);
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {;
							document.getElementById('container').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 		
	
}


function cetakPDF(kodeorg,tanggal,ev = 'event')
{
	param='tanggal='+tanggal+'&kodeorg='+kodeorg;
	tujuan = 'pabrik_slave_printProduksi_pdf_v1.php?'+param;	
	title= "Laporan Produksi Harian " + tanggal;
	width='700';
	height='400';
	content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
	showDialog1(title,content,width,height,ev);
}

function grafikProduksi(periode,tampil,pabrik,ev)
{
   param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;
   //document.getElementById('container').innerHTML="<img src='pabrik_slave_grafikProduksi.php?"+param+"'>";		
   tujuan='pabrik_slave_grafikProduksi.php?'+param;
   title=periode;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}

function laporanEXCEL(kodeorg,periode,ev = 'event')
{
    param='kodeorg='+kodeorg+'&periode='+periode+'&tampil='+tampil;
    tujuan = 'pabrik_slave_produksi_harian_excel.php?method=excel&'+param;	
    //display window
    title=periode;
    width='700';
    height='400';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);
}

