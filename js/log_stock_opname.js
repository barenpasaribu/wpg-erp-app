// GET FUNCTION //
function DownloadPDF(id,event){
	param='method=DownloadPDF';
	tujuan='log_slave_cetak_po.php';
	function respog(){
		if(con.readyState==4){
			if (con.status == 200){
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
					showDialog1('Print PDF',"<iframe frameborder=0 style='width:1000px;height:500px' src='log_slave_print_stock_opname_convert.php?column="+id+"'></iframe>",'1000','500',event);
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}	
	} 	
	post_response_text(tujuan, param, respog);
}
function GetGudang(){
	Method = 'GetGudang';
	Lokasi = document.getElementById('lokasi').options[document.getElementById('lokasi').selectedIndex].value;
	
	param ='method='+Method+'&lokasi='+Lokasi;
	tujuan='log_slave_get_pemakaian_barang.php';
	
	post_response_text(tujuan, param, respog);
	function respog(){
		if(con.readyState==4){
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {alert('ERROR TRANSACTION,\n' + con.responseText);}
				else {
					document.getElementById('gudang').innerHTML=con.responseText;
					showById('buttoncari');
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}	
	}
}
function DisplayList(){
	Method = 'LoadData';
	lokasi = document.getElementById('lokasi').options[document.getElementById('lokasi').selectedIndex].value;
	gudang = document.getElementById('gudang').options[document.getElementById('gudang').selectedIndex].value;
	periode = trim(document.getElementById('periode').value);
	nostokopname  = trim(document.getElementById('nostokopname').value);
	
	if(lokasi == '' || lokasi == null){
		alert('Lokasi harus diisi.');
		return
	}
	if(gudang == '' || gudang == null){
		alert('Gudang harus diisi.');
		return
	}
	if(periode == '' || periode == null){
		alert('Periode harus diisi.');
		return
	}
	
	param ='method='+Method+'&lokasi='+lokasi+'&gudang='+gudang;
	param+='&periode='+periode+'&nostokopname='+nostokopname;
	
	tujuan='log_slave_get_stock_opname.php';
	
	post_response_text(tujuan, param, respog);
	function respog(){
		if(con.readyState==4){
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {alert('ERROR TRANSACTION,\n' + con.responseText);}
				else {
					document.getElementById('ListContainer').innerHTML=con.responseText;
					document.getElementById('ViewListHeader').style.display='block';
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}	
	}          
}

// VIEW FUNCTION //
function PopUpSearchItem(title, method, ev){
	content= "<div>";
	content+="<fieldset>"+title+" <input type=text id=textsearch class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=CariPopUpSearchItem('"+method+"')>Cari</button></fieldset>";
	content+="<div id=containeritem style=\"height:400px;width:600px;overflow:scroll;\">";
	content+="</div></div>";
	title=title;
	width='600';
	height='440';
	showDialog1(title,content,width,height,ev);
}
function CariPopUpSearchItem(method){
	textsearch = document.getElementById('textsearch').value;
	
	param  = 'method='+method+'&textsearch='+textsearch;
	tujuan = 'log_slave_get_daftar_tutup_paksa_pr.php';
	post_response_text(tujuan, param, respog);	
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('containeritem').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}
function SetNoTutupPaksaPR(No){
	notutuppaksa = document.getElementById('notutuppaksa_'+No).value;
	document.getElementById('notutuppaksa').value = notutuppaksa;
	closeDialog();
}
function CariSO(title,ev){
	method  = 'GetNoSO';
	noso 	= '';
	unitDt	= trim(document.getElementById('lokasi').value);
	gudang	= trim(document.getElementById('gudang').value);
	periode = trim(document.getElementById('periode').value);
	
	param='noso='+noso+'&unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode+'&method='+method;
	tujuan = 'log_slave_get_stock_opname.php';
	post_response_text(tujuan, param, respog);	
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					content= "<div>";
					content+="<fieldset>No Stok Opname:<input type=text id=textso class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariSO()>Go</button> </fieldset>";
					content+="<div id=containercari style=\"height:400px;width:600px;overflow:scroll;\">";
					content+="</div></div>";
					title=title+' SO:';
					width='600';
					height='400';
					showDialog1(title,content,width,height,ev);	
					document.getElementById('containercari').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	   
}
function goCariSO(){
	method  = 'GetNoSO';
	noso	= trim(document.getElementById('textso').value);
	unitDt	= trim(document.getElementById('lokasi').value);
	gudang	= trim(document.getElementById('gudang').value);
	periode = trim(document.getElementById('periode').value);
	
	//if(noso.length<0)
	//   alert('Text too short');
	//else{   
	param	= 'noso='+noso+'&unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode+'&method='+method;
	tujuan  = 'log_slave_get_stock_opname.php';
	post_response_text(tujuan, param, respog);			
	//}
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('containercari').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}
function goPickSo(NoSo){
	document.getElementById('nostokopname').value=NoSo;
}





/*function CariNoPr(title,ev){
	content= "<div>";
	content+="<fieldset>No PR <input type=text id=noprpopup class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=CariNoPrGo()>Cari</button></fieldset>";
	content+="<div id=containerpopupsrch style=\"height:400px;width:600px;overflow:scroll;\">";
	content+="</div></div>";
	title=title;
	width='600';
	height='440';
	showDialog1('Cari No PR',content,width,height,ev);
}

function CariNoPrGo(){
	Method 	= 'CariNoPr';
	noprpopup = document.getElementById('noprpopup').value;
	
	param  = 'method='+Method+'&noprpopup='+noprpopup;
	tujuan = 'log_slave_get_daftar_tutup_paksa_pr.php';
	post_response_text(tujuan, param, respog);	
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('containerpopupsrch').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}

function CariNoPrPick(nopr){
	NoPR = document.getElementById('list_'+nopr).value;
	document.getElementById('noprsrch').value = NoPR;
	closeDialog();
}

function Cari(Type,Id,ev){
	content= "<div>";
	content+="<fieldset>No "+Type+" <input type=text id="+Type+"popup class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25>";
	content+="<button class=mybutton onclick=CariGo('"+Type+"','"+Id+"')>Cari</button></fieldset>";
	content+="<div id=container"+Type+"popupsrch style=\"height:400px;width:600px;overflow:scroll;\">";
	content+="</div></div>";
	width='600';
	height='440';
	showDialog1('Cari '+Type,content,width,height,ev);
}

function CariGo(Type,Id){
	Method 	= 'Cari'+Type;
	ItemSearch = document.getElementById(Type+'popup').value;
	
	param  = 'method='+Method+'&itemsearch='+ItemSearch+'&idcari='+Id;
	tujuan = 'log_slave_get_daftar_tutup_paksa_pr.php';
	post_response_text(tujuan, param, respog);	
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('container'+Type+'popupsrch').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}

function CariPick(no,Id){
	ItemPick = document.getElementById('list_item_'+no).value;
	document.getElementById(Id).value = ItemPick;
}

function DownloadExcel(){
	Method 	= 'DownloadExcel';
	ListData = document.getElementById('ListContainer').innerHTML;
	
	param  = 'method='+Method+'&listdata='+ListData+'&judul=Laporan';
	tujuan = 'log_slave_get_daftar_tutup_paksa_pr.php';
	post_response_text(tujuan, param, respog);	
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('container'+Type+'popupsrch').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}*/

