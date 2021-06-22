/**
 * @author Developer 
 */
function simpanJI()
{
	kode=document.getElementById('kodeijin').value;
	jenisijin=document.getElementById('jenisijin').value;
	
	iscuti = document.getElementById('iscuti').checked;
	ispotong = document.getElementById('ispotong').checked;
	isdayoff = document.getElementById('isdayoff').checked;
	jumlahhari=document.getElementById('jumlahhari').value;
	kdabsen=document.getElementById('kdabsen');
	kdabsen=kdabsen.options[kdabsen.selectedIndex].value;
	met=document.getElementById('method').value;
	dttdlkp=document.getElementById('dttdlkp').value;
	
	if (iscuti == true){
		iscutis = 1;
	}
	else {
		iscutis = 0;
	}
	if (ispotong == true){
		ispotongs = 1;
	}
	else {
		ispotongs = 0;
	}	
	if (isdayoff == true){
		isdayoffs = 1;
	}
	else {
		isdayoffs = 0;
	}
	if(trim(kode)=='' || kdabsen=='')
	{
		alert(dttdlkp);
	}
	else
	{
		kode=trim(kode);
		jenisijin=trim(jenisijin);
		param='kode='+kode+'&jenisijin='+jenisijin+'&iscuti='+iscutis+'&ispotong='+ispotongs+'&isdayoff='+isdayoffs+'&jumlahhari='+jumlahhari+'&kdabsen='+kdabsen+'&method='+met;
		tujuan='sdm_slave_save_jenisijin.php';
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
						else {
							document.getElementById('container').innerHTML=con.responseText;
							isup=document.getElementById('isup').value;
							jenisicada=document.getElementById('jenisicada').value;
							if (isup==1){
								alert(jenisicada);
							}
							cancelJI();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

//tambah fungsi untuk cek jika merupakan cuti memotong atau tidak ==Jo 31-03-2017== 
function cekpotong(){
	ispotong = document.getElementById('ispotong').checked;
	
	if(ispotong==true){
		document.getElementById('jumlahhari').disabled=true;
	}
	else{
		document.getElementById('jumlahhari').disabled=false;
	}
}

function hapusJI(kode)
{
	cnfdel=document.getElementById('cnfdel').value;
	param='kode='+kode+'&method=deletes';
	tujuan='sdm_slave_save_jenisijin.php';
	if(confirm(cnfdel))
    {
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
						else {
							//alert(con.responseText);
							
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

function fillField(kode,jenis,ic,ip,idf,jnhari,kdabsen)
{
	document.getElementById('kodeijin').value=kode;
    document.getElementById('kodeijin').disabled=true;
	document.getElementById('jenisijin').value=jenis;
	if (ic == 1){
		document.getElementById('iscuti').checked=true;
	}
	else {
		document.getElementById('iscuti').checked=false;
	}
	if (ip==1){
		document.getElementById('ispotong').checked=true;
	}
	else {
		document.getElementById('ispotong').checked=false;
	}
	if (idf==1){
		document.getElementById('isdayoff').checked=true;
	}
	else {
		document.getElementById('isdayoff').checked=false;
	}
	ispotong = document.getElementById('ispotong').checked;
	
	if(ispotong==true){
		document.getElementById('jumlahhari').disabled=true;
	}
	else{
		document.getElementById('jumlahhari').disabled=false;
	}
	
	x=document.getElementById('kdabsen');
	for(z=0;z<x.length;z++)
	{
		if(x.options[z].value==kdabsen)
		x.options[z].selected=true;
	}
	document.getElementById('jumlahhari').value=jnhari;
	document.getElementById('method').value='update';
}

function cancelJI()
{
    document.getElementById('kodeijin').disabled=false;
	document.getElementById('kodeijin').value='';
	document.getElementById('jenisijin').value='';
	document.getElementById('iscuti').checked=false;
	document.getElementById('ispotong').checked=false;
	document.getElementById('isdayoff').checked=false;
	//document.getElementById('kdabsen').value='';
	document.getElementById('jumlahhari').value='';
	document.getElementById('method').value='insert';		
}

