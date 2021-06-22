/**
 * @author antoniuslouis
 */
function setSloc(x){
	gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        tglstart=document.getElementById(gudang+'_start').value;
	tglend=document.getElementById(gudang+'_end').value;
	tglstart=tglstart.substr(6,2)+"-"+tglstart.substr(4,2)+"-"+tglstart.substr(0,4);
	tglend=tglend.substr(6,2)+"-"+tglend.substr(4,2)+"-"+tglend.substr(0,4);
	document.getElementById('displayperiod').innerHTML=tglstart+" - "+tglend;
	
	if (gudang != '') {
		if (x == 'simpan') {
			document.getElementById('sloc').disabled = true;
			document.getElementById('btnsloc').disabled = true;
			document.getElementById('pemilikbarang').disabled = true;
			tujuan = 'log_slave_permintaan_gudang_mris.php';
			param = 'gudang=' + gudang+'&proses=getNotrans';
                        statInputan=0;
			post_response_text(tujuan, param, respog);
		}
		else {
                    document.location.reload();//updateby jamhari
//			document.getElementById('nodok').value='';
//			document.getElementById('sloc').disabled = false;
//			document.getElementById('pemilikbarang').disabled = false;
//			document.getElementById('pemilikbarang').innerHTML = "<option value=''></option>";
//			document.getElementById('sloc').options[0].selected=true;
//			document.getElementById('btnsloc').disabled = false;
//			kosongkan();
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
						//alert(con.responseText);
						document.getElementById('nodok').value = trim(con.responseText);
					        getBastList(gudang);
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}

function loadSubunit(induk,afd,namapnrima){
   
   param='induk='+induk;
   if(afd!=''){
       param+='&afdeling='+afd;
   }
   if(namapnrima!=''){
       param+='&namapenerima='+namapnrima;
   }
   document.getElementById('subunit').innerHTML='';
   document.getElementById('blok').innerHTML='';
   document.getElementById('penerima').innerHTML="";
	tujuan = 'log_slave_getSubUnitOption.php';
	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
                                        isidt=con.responseText.split("####");
					document.getElementById('subunit').innerHTML=isidt[0];
					document.getElementById('blok').innerHTML=isidt[0];
                                        document.getElementById('penerima').innerHTML=isidt[1];
                                        if(afd!=''){
                                            loadBlock(afd,namapnrima);
                                            document.getElementById('subunit').disabled=true;
                                        }
                                        //
				    //loadMesin(induk);
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	   	
}

function loadMesin(induk){
	
        param='induk='+induk;
	tujuan = 'log_slave_getMesinOption.php';
	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
				        document.getElementById('blok').options[0].selected=true;	
                                        document.getElementById('mesin').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
	
}
function getKegiatan(blok,x,kdkeg){
        param='blok='+blok+'&jenis='+x+'&kdkegiatan='+kdkeg+'&proses=getKegiatan';
	tujuan = 'log_slave_permintaan_gudang_mris.php';
                    if(x=='TRAKSI')
                        document.getElementById('blok').options[0].selected=true;
                    else if(x=='BLOK')
                         document.getElementById('mesin').options[0].selected=true;
	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('kegiatan').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}		
}
function loadBlock(induk,namapnrima)
{
	
	untukunit=document.getElementById('untukunit').value;	
	
   param='induk='+induk+'&untukunit='+untukunit;
   if(namapnrima!=''){
       param+='&namapenerima='+namapnrima+'&untukunit='+untukunit;;
   }
   document.getElementById('blok').innerHTML='';
   document.getElementById('penerima').innerHTML="";
   document.getElementById('mesin').options[0].selected=true;
	tujuan = 'log_slave_getSubUnitOption.php';
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
                                        
                                        isiDataEr=con.responseText.split("####");
					document.getElementById('blok').innerHTML=isiDataEr[0];
                                        document.getElementById('penerima').innerHTML=isiDataEr[1];
					getKegiatan(induk);
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	   	
}


function getPT(gudang)
{
   param='gudang='+gudang;
	tujuan = 'log_slave_gudangGetPTOption.php';
	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('pemilikbarang').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}


function disableHeader()
{
	document.getElementById('tanggal').disabled=true;
	document.getElementById('untukunit').disabled=true;
	document.getElementById('penerima').disabled=true;
	document.getElementById('catatan').disabled=true;
  
}
function enableHeader()
{
	document.getElementById('tanggal').disabled=false;
	document.getElementById('untukunit').disabled=false;
	document.getElementById('penerima').disabled=false;
	document.getElementById('catatan').disabled=false;
	document.getElementById('subunit').disabled=false;	
      
}

function showWindowBarang(title,ev)
{
	  
	  content= "<div style='width:100%;'>";
	  content+="<fieldset>"+title+"<input type=text id=txtnamabarang class=myinputtext size=25 onkeypress=\"return enterEuy(event);\" maxlength=35><button class=mybutton onclick=goCariBarang()>Go</button> </fieldset>";
	  content+="<div id=containercari style='overflow:scroll;height:300px;width:520px'></div></div>";
     //display window
	   width='550';
	   height='350';
	   showDialog1(title,content,width,height,ev);		
}

function enterEuy(evt)
{
	key=getKey(evt);
	if(key==13)
	{
		goCariBarang();
	}
	else
	{
		return tanpa_kutip(evt);
	}
	
}

function loadField(kode,nama,satuan)
{
	document.getElementById('kodebarang').value=kode;
	document.getElementById('namabarang').value=nama;
	document.getElementById('satuan').value=satuan;
	closeDialog();		
}

function kosongkan()
{
                        document.location.reload();//updateby jamhari
//			document.getElementById('kodebarang').value='';
//			document.getElementById('namabarang').value='';
//			document.getElementById('penerima').value='';
//			document.getElementById('catatan').value='';
//			document.getElementById('satuan').value='';
//			document.getElementById('qty').value=0;
//			document.getElementById('blok').innerHTML="<option value=''></option>";
//			document.getElementById('mesin').options[0].selected=true;
//                        document.getElementById('kegiatan').options[0].selected=true;
//			document.getElementById('subunit').innerHTML="<option value=''></option>";
//			enableHeader();	
}

function nextItem()
{
	document.getElementById('kodebarang').disabled=false;
	document.getElementById('satuan').disabled=false;
	document.getElementById('namabarang').disabled=false;	
	document.getElementById('blok').disabled=false;
	document.getElementById('kodebarang').value='';
	document.getElementById('namabarang').value='';
	document.getElementById('satuan').value='';
	document.getElementById('qty').value=0;	
	document.getElementById('subunit').disabled=false;
	document.getElementById('method').value='insert';
        document.getElementById('mesin').options[0].selected=true;
        document.getElementById('kegiatan').options[0].selected=true;
               
}

function bastBaru()
{
  nextItem();
    document.getElementById('kodebarang').value='';
    document.getElementById('namabarang').value='';
    document.getElementById('penerima').value='';
    document.getElementById('catatan').value='';
    document.getElementById('satuan').value='';
    document.getElementById('qty').value=0;
    document.getElementById('blok').innerHTML="<option value=''></option>";
    document.getElementById('mesin').options[0].selected=true;
    document.getElementById('kegiatan').options[0].selected=true;
    document.getElementById('subunit').innerHTML="<option value=''></option>";
    enableHeader();		
  setSloc('simpan');
  document.getElementById('untukunit').options[0].selected=true;
  document.getElementById('bastcontainer').innerHTML='';
  document.getElementById('subunit').disabled=false;
  document.getElementById('subunit').innerHTML="<option value=''></option>";
  document.getElementById('penerima').innerHTML="<option value=''></option>";
  statInputan=0;//reset dari nol
}

function goCariBarang()
{
	gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
	nodok=document.getElementById('nodok').value;
	if (nodok == '') {
		alert('Document Number is Obligatory');
	}
	else {
		txtcari = trim(document.getElementById('txtnamabarang').value);
		pemilikbarang = document.getElementById('pemilikbarang');
		pemilikbarang = pemilikbarang.options[pemilikbarang.selectedIndex].value;
		
		if (document.getElementById('nodok') == '') {
			alert('Document number is obligatory');
		}
		else 
			if (pemilikbarang.length < 3) {
				alert('Goods Owner(PT) is obligatory');
			}
			else {
				if (txtcari.length < 3) {
					alert('material name min. 3 char');
				}
				else {
					param = 'txtcari=' + txtcari + '&pemilikbarang=' + pemilikbarang;
					param+='&gudang='+gudang;
					tujuan = 'log_slave_cariBarang.php';
					post_response_text(tujuan, param, respog);
				}
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

function saveItemBast(){
	gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        tanggal=document.getElementById('tanggal').value;
		x=tanggal;
		_start=document.getElementById(gudang+'_start').value;
		_end=document.getElementById(gudang+'_end').value;
		console.log(x);
		while (x.lastIndexOf("-") > -1) {
			x = x.replace("-", "");
		}
		console.log(x);
		while (x.lastIndexOf("-") > -1) {
		    x=x.replace("/","");
		}
		console.log(x);
		
		curdateY=x.substr(4,4).toString();
		curdateM=x.substr(2,2).toString();
		curdateD=x.substr(0,2).toString();
		curdate=curdateY+curdateM+curdateD;	
		curdate=parseInt(curdate);
	if (curdate < parseInt(_start) || curdate > parseInt(_end)) {
		alert('Date out of range')
	}
	else {
			nodok		=trim(document.getElementById('nodok').value);
			tanggal		=trim(document.getElementById('tanggal').value);
			kodebarang	=trim(document.getElementById('kodebarang').value);
			penerima	=trim(document.getElementById('penerima').value);
			catatan		=trim(document.getElementById('catatan').value);			
			satuan		=trim(document.getElementById('satuan').value);
			qty			=trim(document.getElementById('qty').value);
			method		=trim(document.getElementById('method').value);
			kdpt            =document.getElementById('pemilikbarang');
                        kdpt            =trim(kdpt.options[kdpt.selectedIndex].value);
			blok		=document.getElementById('blok');
				blok	=trim(blok.options[blok.selectedIndex].value);
			mesin		=document.getElementById('mesin');
				mesin	=trim(mesin.options[mesin.selectedIndex].value);
			untukunit	=document.getElementById('untukunit');
				untukunit=trim(untukunit.options[untukunit.selectedIndex].value);
			subunit		=document.getElementById('subunit');
				subunit	=trim(subunit.options[subunit.selectedIndex].value);
			kegiatan		=document.getElementById('kegiatan');
				kegiatan	=trim(kegiatan.options[kegiatan.selectedIndex].value);			
	        gudang 		=trim(document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value);
	        pemilikbarang =trim(document.getElementById('pemilikbarang').options[document.getElementById('pemilikbarang').selectedIndex].value);
		if(nodok=='')
		{
			alert('Document Number is obligatory');
		}
		else if(untukunit=='')
		{
			alert('Bussiness unit(Unit) is obligatory');
		} 
		else if(kegiatan=='')
		{
			alert('Activity is obligatory');
		}
		//else if(penerima=='')
		//{
		//	alert('Recipient name is obligatory');
		//}  
		else if(kodebarang=='' || satuan=='' || parseFloat(qty)<0.001)
		{
			alert('Material, UOM and volume is obligatory');
		}
		else
		{
			if(confirm('Are you sure?'))
			{
				param='nodok='+nodok+'&tanggal='+tanggal+'&kodebarang='+kodebarang;
				param+='&penerima='+penerima+'&satuan='+satuan+'&qty='+qty;
				param+='&blok='+blok+'&mesin='+mesin+'&untukunit='+untukunit;
				param+='&gudang='+gudang+'&pemilikbarang='+pemilikbarang;
				param+='&catatan='+catatan+'&kegiatan='+kegiatan+'&pemilikbarang='+kdpt;
				param+='&subunit='+subunit+'&proses=simpan'+'&method='+method;
                                param+='&statInputan='+statInputan;
				tujuan='log_slave_permintaan_gudang_mris.php';
				post_response_text(tujuan, param, respog);
				disableHeader();
				document.getElementById('qty').style.backgroundColor='red';
			}
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
						document.getElementById('qty').style.backgroundColor='#ffffff';
						nextItem();
						//document.getElementById('bastcontainer').innerHTML=con.responseText;
                                                balikdr=con.responseText.split("####");
						document.getElementById('bastcontainer').innerHTML=balikdr[0];
                                                document.getElementById('nodok').value=balikdr[1];
						//setelah menyimpan 1 baris yakinkan method adalah insert
						document.getElementById('method').value='insert';
                                                statInputan=1;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

function getBastList(gudang)
{
		param='gudang='+gudang+'&proses=loadData';
		tujuan = 'log_slave_permintaan_gudang_mris.php';
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

function delBast(notransaksi,kodebarang,kodeblok,kdmesin)
{
        untukunit	=document.getElementById('untukunit');
		     untukunit=trim(untukunit.options[untukunit.selectedIndex].value);
		pemilikbarang = document.getElementById('pemilikbarang');
		     pemilikbarang=trim(pemilikbarang.options[pemilikbarang.selectedIndex].value);
		param='nodok='+notransaksi+'&kodebarang='+kodebarang;
		param+='&delete=true&blok='+kodeblok+'&pemilikbarang='+pemilikbarang;
		param+='&untukunit='+untukunit+'&proses=simpan'+'&kdmesin='+kdmesin;
		tujuan='log_slave_permintaan_gudang_mris.php';
                 
		if(confirm('Deleting Document '+notransaksi+', are you sure..?'))
		  post_response_text(tujuan, param, respog);	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('bastcontainer').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

function editBast(kodebarang,namabarang,satuan,jumlah,kodeblok,kodekegiatan,kodemesin,tipe){
        //set blok karena merupakan primary
        document.getElementById('blok').innerHTML="<option value=''></option><option value='"+kodeblok+"'>"+kodeblok+"</option>";
  	//document.getElementById('subunit').innerHTML="<option value='"+kodeblok+"'>"+kodeblok+"</option>";
	document.getElementById('kodebarang').value=kodebarang;
	document.getElementById('namabarang').value=namabarang;
	document.getElementById('satuan').value=satuan;
	
	document.getElementById('kodebarang').disabled=true;
	document.getElementById('satuan').disabled=true;
	document.getElementById('namabarang').disabled=true;
	document.getElementById('blok').disabled=true;
	document.getElementById('subunit').disabled=true;
        document.getElementById('method').value="";
	document.getElementById('method').value="update";
	document.getElementById('qty').value=jumlah;
        blok=document.getElementById('blok');
	blok.options[1].selected=true;	
        strcek=kodeblok.substring(4,1);
        isiblok="";
        if((kodemesin!='')||(kodeblok!='')){
            isiblok=kodeblok;
            if(kodemesin!=''){
                isiblok=kodemesin;
            }
        }
        getKegiatan(isiblok,tipe,kodekegiatan);
   
   disableHeader();	
}

function delXBapb(nodok)
{
	if(confirm('Deleting Doc: '+nodok+', Are sure..?'))
	{
		param='notransaksi='+nodok+'&proses=hapustrans';
		tujuan='log_slave_permintaan_gudang_mris.php';//file ini berfungsi untuk penerimaan dan pengeluaran
	   if(confirm('All data in this document will be removed. Continue ?'))
	   {
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
						gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
						setSloc('simpan');
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}		
}


function editXBast(notransaksi,untukunit,tanggal,namapenerima,keterangan){

	nextItem();
	document.getElementById('nodok').value = notransaksi;
	document.getElementById('tanggal').value=tanggal;
	//document.getElementById('method').value='update';
	document.getElementById('catatan').value=keterangan;		
        unit=untukunit.substring(0,4); 
	unt=document.getElementById('untukunit');
	for(x=0;x<unt.length;x++)
	{
		if(unt.options[x].value==unit)
		{
			unt.options[x].selected=true;
		}
	}
        unt.disabled=true;
        tabAction(document.getElementById('tabFRM0'),0,'FRM',1);//jangan tanya darimana	
	tujuan='log_slave_permintaan_gudang_mris.php';
	param='nodok='+notransaksi+'&displayonly=true'+'&proses=simpan';
    post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
					   document.getElementById('bastcontainer').innerHTML=con.responseText;
                                           loadSubunit(unit,untukunit,namapenerima);
                                           statInputan=1;
					   //loadBlock(untukunit);
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

function cariBast(num)
{
	tex=trim(document.getElementById('txtbabp').value);
	gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
    if(gudang =='')
	{
		alert('Storage Location  is obligatory')
	}
	else
	{
		param='gudang='+gudang+'&proses=loadData';
		param+='&page='+num;
		if(tex!='')
			param+='&tex='+tex;
		tujuan = 'log_slave_permintaan_gudang_mris.php';
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


function previewBast(notransaksi,ev)
{
   	param='notransaksi='+notransaksi;
	tujuan = 'log_slave_print_mris_pdf.php?'+param;	
 //display window
   title=notransaksi;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
   
}
function postingData(notrans,kdgdng){
    param='notransaksi='+notrans+'&proses=postingdata'
    tujuan='log_slave_permintaan_gudang_mris.php';
    if(confirm("Are you sure posting this transaction !!")){
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
                                            getBastList(kdgdng);
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
                    }
            }	
}