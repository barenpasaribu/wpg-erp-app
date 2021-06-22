var showPerPage = 10;

function getValue(id) {
    var tmp = document.getElementById(id);
    
    if(tmp) {
        if(tmp.options) {
            return tmp.options[tmp.selectedIndex].value;
        } else if(tmp.nodeType=='checkbox') {
            if(tmp.checked==true) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return tmp.value;
        }
    } else {
        return false;
    }
}

/* Search
 * Filtering Data
 */
function searchTrans() {
    var notrans = document.getElementById('sNoTrans'),
        nopo = document.getElementById('sNoPo'),
        where = '[["noinvoice","'+notrans.value+'"],["noinvoicesupplier","'+notrans.value+'"],["nopo","'+nopo.value+'"]]';
    
    goToPages(1,showPerPage,where);
}

/* Paging
 * Paging Data
 */
function defaultList() {
    goToPages(1,showPerPage);
}

function goToPages(page,shows,where) {
    if(typeof where != 'undefined') {
        var newWhere = where.replace(/'/g,'"');
    }
    var workField = document.getElementById('workField');
    var param = "page="+page;
    param += "&shows="+shows+"&tipe=KB";
    if(typeof where != 'undefined') {
        param+="&where="+newWhere;
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan.php?proses=showHeadList', param, respon);
}

function choosePage(obj,shows,where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal,shows,where);
}

/* Halaman Manipulasi Data
 * Halaman add, edit, delete
 */
function showAdd() {
    var workField = document.getElementById('workField');
    var param = "";
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    document.getElementById('printArea').style.display='block';

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan.php?proses=showAdd', param, respon);
}

function showEditFromAdd() {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('noinvoice');
    var param = "noinvoice="+trans.value;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('noinvoice_'+num);
    var param = "numRow="+num+"&noinvoice="+trans.innerHTML;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    if(getValue('nopo')=='') {
        alert('No PO harus dipilih');
        return;
    }
    
    var param = "noinvoice="+getValue('noinvoice')+"&noinvoicesupplier="+getValue('noinvoicesupplier')+"&tanggal="+getValue('tanggal')+"&tipeinvoice="+getValue('tipeinvoice');
    param += "&nopo="+getValue('nopo')+"&keterangan="+getValue('keterangan')+"&nilaiinvoice="+getValue('nilaiinvoice');
    param += "&jatuhtempo="+getValue('jatuhtempo')+"&nofp="+getValue('nofp');
    param += "&noakun="+getValue('noakun')+"&uangmuka="+getValue('uangmuka')+"&potsusutkg="+getValue('potsusutkg')+"&potsusutjml="+getValue('potsusutjml')+"&potmutu="+getValue('potmutu')+"&potmutujml="+getValue('potmutujml');
    param += "&nilaippn="+getValue('nilaippn')+"&kodeorg="+getValue('kodeorg');//+"&akunppn="+getValue('akunppn');
    param += "&pph="+getValue('pph')+"&perhitunganpph="+getValue('perhitunganpph');
    // alert(getValue('perhitunganpph'), getValue('pph'));
   // if(getValue('tipeinvoice')=='ot'){
        param+="&kodesupplier="+getValue('kodesupplier');
    //}
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    tipeinvoice = getValue('tipeinvoice');
                    if(tipeinvoice !="sj"){
                        alert('Added Data Header');
                        defaultList();
                        console.log("kesini");
                    }
                    totaldata = document.getElementById('MaxRow').innerHTML;
                    if(totaldata==null || totaldata=='0'  ){
                    	alert('Added Data Header');
                    	defaultList();
                        console.log("kesini");
                    }else{
                    	SimpanLoopingDataTiket();
                    }
                    console.log("kesini");
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "noinvoice="+getValue('noinvoice')+"&noinvoicesupplier="+getValue('noinvoicesupplier')+"&tanggal="+getValue('tanggal')+"&tipeinvoice="+getValue('tipeinvoice');
    param += "&nopo="+getValue('nopo')+"&keterangan="+getValue('keterangan')+"&nilaiinvoice="+getValue('nilaiinvoice');
    param += "&jatuhtempo="+getValue('jatuhtempo')+"&nofp="+getValue('nofp');
    param += "&noakun="+getValue('noakun')+"&uangmuka="+getValue('uangmuka')+"&potsusutkg="+getValue('potsusutkg')+"&potsusutjml="+getValue('potsusutjml')+"&potmutu="+getValue('potmutu');
    param += "&nilaippn="+getValue('nilaippn')+"&kodeorg="+getValue('kodeorg');
    param += "&perhitunganpph="+getValue('perhitunganpph');
    if(getValue('tipeinvoice')=='ot'){
        param+="&kodesupplier="+getValue('kodesupplier');
    }
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan.php?proses=edit', param, respon);
}

/*
 * Detail
 */

function showDetail() {
    var detailField = document.getElementById('detailField');
    var notrans = document.getElementById('noinvoice').value;
    var param = "noinvoice="+notrans+"&nopo="+getValue('nopo');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    detailField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var notrans = document.getElementById('noinvoice_'+num).innerHTML;
    var param = "noinvoice="+notrans;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var tmp = document.getElementById('tr_'+num);
                    tmp.parentNode.removeChild(tmp);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    if(confirm('Are You Sure Delete this data ??'))
    post_response_text('keu_slave_tagihan.php?proses=delete', param, respon);
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_tagihan_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}
function detailPDF(num) {
    // Prep Param
    var notrans = document.getElementById('noinvoice_'+num).innerHTML;
     var param = "noinvoice="+notrans;
    masterPDF('keu_tagihanht',notrans,'','keu_tagihan_pdf',event);
}

/* Update No Urut di halaman absensi
 */
function updNoUrut() {
    var tabBody = document.getElementById('mTabBody');
    var nourut = document.getElementById('nourut');
    var maxNum = 0;
    
    if(tabBody.childNodes.length>0) {
        for(i=0;i<tabBody.childNodes.length;i++) {
            var tmp = document.getElementById('nourut_'+i);
            if(tmp.innerHTML > maxNum) {
                maxNum = tmp.innerHTML;
            }
        }
    }
    nourut.value = parseInt(maxNum)+1;
}

function updPO() {
    var nopo = document.getElementById('nopo');
    nopo.options.length = 0;
    
    var param = "pokontrak="+getValue('tipeinvoice');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    eval('var res='+con.responseText);
                    for(i in res) {
                        nopo.options[nopo.options.length] = new Option(res[i],res[i]);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan.php?proses=updpo', param, respon);
}

function updInvoice() {
    var invoice = document.getElementById('nilaiinvoice');
    
    var param = "nopo="+getValue('nopo');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    if(con.responseText!='') {
                        invoice.value = con.responseText;
                        invoice.value = _formatted(invoice);
                        invoice.setAttribute('disabled','disabled');
                    } else {
                        invoice.value = 0;
                        invoice.removeAttribute('disabled');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_tagihan.php?proses=updInvoice', param, respon);
}

//jamhari
function searchNopo(title,content,ev)
{
        tipe = getValue('tipeinvoice');
        if(tipe!='ot'){
            document.getElementById('kodesupplier').disabled=true;
        var theTitle = "Find ";
        switch(tipe) {
                case 'po':
                        theTitle += "PO";
                        break;
                case 'kontrak':
                        theTitle += "No. Kontrak";
                        break;
                case 'sj':
                        theTitle += "Surat Jalan";
                        break;
                case 'fee':
                        theTitle += "Fee";
                        break;
                case 'ks':
                        theTitle += "No. Konosemen";
                        break;
        }
        isi=document.getElementById('tipeinvoice').options[document.getElementById('tipeinvoice').selectedIndex].value;
        content=content+"<input type='hidden' id='jnsInvoice' value="+isi+">";
        if(tipe=='sj' || tipe=='fee' ){
        	content=content+"<input type='hidden' id='terpilih' >";
        	content=content+"<input type='hidden' id='supplier' >";
        	content=content+"<button class='mybutton' onclick='selesaichk()'>Selesai</button>";
        }
        
        width='500';
        height='400';
       
          showDialog1(theTitle,content,width,height,ev);
         findNopo();  
        
         
        }else{
            document.getElementById('kodesupplier').disabled=false;
        }
}
function pilihchk(nopo,no,kodesupplier)
{
    
  if(document.getElementById('chk'+no).checked==true){
  		isi = document.getElementById('terpilih').value;
  		supplier = document.getElementById('supplier').value;
  		if(isi==""){
  			isinew = isi+"'";
  		}else{
  			isinew = isi+", '";
  		}
  		if(supplier==""){
  			document.getElementById('supplier').value =kodesupplier;
  			console.log(kodesupplier);
  		}else{
  			if(supplier != kodesupplier){
  				document.getElementById('chk'+no).checked=false;
  				alert('NO SPB harus di pilih dengan expeditor yang sama,\n');
  			}
  		}
  		console.log("ini "+nopo);
  		document.getElementById('terpilih').value = isinew+nopo+"'";
  }else if(document.getElementById('chk'+no).checked==false){
  	isi = document.getElementById('terpilih').value;

  var resK = isi.match(/,/g);
  if(resK==null || resK==""){
	var res = isi.replace("'"+nopo+"'", "");
  	document.getElementById('terpilih').value=res;
  	document.getElementById('supplier').value="";
  	console.log("iniM "+res);
  }else{
  		var res = isi.replace(", '"+nopo+"'", "");
  	document.getElementById('terpilih').value=res;
  	console.log("ini "+nopo);
  }
  

  }
}
function selesaichk()
{	
	kodesupplier= document.getElementById('supplier').value;
	nopo = document.getElementById('terpilih').value; 
	if(kodesupplier ==""){
		alert('Pilihan tidak boleh kosong !,\n');
	}else{
		nilai = 0;
	ppn=0;
	pph=0;
	jns = document.getElementById('tipeinvoice').value;
  setPo(nopo,kodesupplier,nilai,jns,ppn,pph);
	}
	

	
}
function resetkodesup()
{
    
 
  		isi = document.getElementById('terpilih').value;
  		supplier = document.getElementById('supplier').value;
  		
}

function findNopo()
{
    txt=trim(document.getElementById('no_brg').value);
    
        jnsInvoice=document.getElementById('tipeinvoice').value;
        document.getElementById('tipeinvoice').disabled=true;
        param='txtfind='+txt+'&jnsInvoice='+jnsInvoice;
        tujuan='keu_slave_tagihan.php';
        post_response_text(tujuan+'?'+'proses=getPo', param, respog);
    
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
                            document.getElementById('container2').innerHTML=con.responseText;
                        }
                    }
                    else {
                        busy_off();
                        error_catch(con.status);
                    }
              }
     }
}


function cekStatus(np,jns,sb,nilai,pph)
{
    param='np='+np+'&jns='+jns+'&sb='+sb;
    //alert(param);
    tujuan='keu_slave_tagihan.php';
    post_response_text(tujuan+'?'+'proses=cekStatus', param, respog);
    
    function respog()
    {
          if(con.readyState==4)
          {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else 
                    {
                        i=con.responseText;
                        if(i=='A')
                        {
                            alert(" Sorry you can't pay this PO because this po is credit, and don't have BAST");
                            
                            closeDialog();
                        }
                        else
                        {
                                document.getElementById('nopo').value=np;
                                document.getElementById('nilaiinvoice').value=nilai;
                                document.getElementById('nilaippn').value=sb;
                                document.getElementById('perhitunganpph').value=pph;
                                document.getElementById('tipeinvoice').disabled=false;                             
                                closeDialog();
                        }
                        //alert(con.responseText);
                        //document.getElementById('kdAfd').innerHTML=con.responseText;
                        //getKar();
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                }
          } 
    }
    
    
}


function setPo(np,kodesuplier,nilai,jns,pph,ppn)
{
    
    if(jns=='po')
    {
        cekStatus(np,jns,ppn,nilai,pph);
    }else if(jns=='kontrak' ){
        document.getElementById('nopo').value=np;
        document.getElementById('nilaiinvoice').value=nilai;
        document.getElementById('tipeinvoice').disabled=false;
        
         console.log('getkontrak');
        closeDialog();

    }else if(jns=='sj'){
    		var strArray = np.split(",");
        
        // Display array values on page
        
    nopoA =strArray[0] ;
    nopoAkhir =  nopoA.replace(/'/g, ''); 
    	 document.getElementById('nopo').value=nopoAkhir;
       	terpilih = document.getElementById('terpilih').value;
        document.getElementById('tipeinvoice').disabled=false;
        getDataTimbangan(np,jns,kodesuplier);

         console.log(kodesuplier);
        closeDialog();
    }else if(jns=='fee') {
            var strArray = np.split(",");
        
        // Display array values on page
        
    nopoA =strArray[0] ;
    nopoAkhir =  nopoA.replace(/'/g, ''); 
         document.getElementById('nopo').value=nopoAkhir;
        terpilih = document.getElementById('terpilih').value;
        document.getElementById('tipeinvoice').disabled=false;
        getDataTimbangan(np,jns,kodesuplier);

         console.log(kodesuplier);
        closeDialog();

    }else {
        document.getElementById('nopo').value=np;
        document.getElementById('nilaiinvoice').value=nilai;
        document.getElementById('tipeinvoice').disabled=false;
        closeDialog();
    }
     for (c=0;c<=(document.getElementById('kodesupplier').length-1);c++)
        {
        if(document.getElementById('kodesupplier').options[c].value==kodesuplier)
              document.getElementById('kodesupplier').options[c].selected=true;
          }


}

function getppn()
{

    nilaiI = document.getElementById('nilaiinvoice').value;
    nilaiinvoice =  nilaiI.replace(/,/g, ''); 
    console.log(nilaiinvoice);
    var potsusutjumlah = document.getElementById('potsusutjml').value;
    potmutu = document.getElementById('potmutu').value;
    potmutujml = document.getElementById('potmutujml').value;
    if(potmutu =='0'){
         var potmutujumlah = potmutujml;
    }else{
         var potmutujumlah = (potmutu/100)*nilaiinvoice;
    }
    kodesupplier = document.getElementById('kodesupplier').value;
   
  
     param='kodesupplier='+kodesupplier;
        tujuan='keu_slave_tagihan.php';
       post_response_text(tujuan+'?'+'proses=getPPNS', param, respog);
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
                           desimalppn = con.responseText;
                             nilaippn = ((parseFloat(nilaiinvoice)) - (parseFloat(potsusutjumlah)+parseFloat(potmutujumlah)))*(desimalppn);
                             nilaippnA = Math.floor(nilaippn)
                            document.getElementById('nilaippn').value=nilaippnA;
                        
                    }
                }
                    else {
                        busy_off();
                        error_catch(con.status);
                    }
              }
     }

    

}
function getPotSusut()
{
    if(jns=='kontrak' || jns=='sj'){
    nilaiI = document.getElementById('nilaiinvoice').value;
    potsusutkg = document.getElementById('potsusutkg').value;
    nilaiinvoice =  nilaiI.replace(/,/g, ''); 
    console.log(nilaiinvoice);
    totalnetto = document.getElementById('TotalNetto').innerHTML;
     if(totalnetto==0){
          var harga = 0;
    }else{
          var harga = (nilaiinvoice)/totalnetto;
    }
    var potsusutjumlah = harga * potsusutkg;
    document.getElementById('potsusutjml').value=potsusutjumlah;
}
    
}
function getPPH()
{
 
    kodesupplier = document.getElementById('kodesupplier').value;
    pph = document.getElementById('pph').value;
   nilaiI = document.getElementById('nilaiinvoice').value;
    nilaiinvoice =  nilaiI.replace(/,/g, ''); 
    console.log(tanggal);
   
    param='kodesupplier='+kodesupplier+'&pph='+pph;
        tujuan='keu_slave_tagihan.php';
       post_response_text(tujuan+'?'+'proses=getPPH', param, respog);
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
                            potmutu = document.getElementById('potmutu').value;
                            potmutujml = document.getElementById('potmutujml').value;
                              if(potmutu=='0'){
                            var potmutujumlah = potmutujml;
                            }else{
                             potmutujumlah = ((parseFloat(potmutu))/100)*(nilaiinvoice);
                         }
                         
                              var  potsusutjumlah = document.getElementById('potsusutjml').value;
                            console.log(potmutujumlah);
                            console.log(potsusutjumlah);
                            var totalpot = (parseFloat(potmutujumlah))+(parseFloat(potsusutjumlah));
                             console.log(totalpot);

                            hasil = (nilaiinvoice - totalpot) * (con.responseText);
                            document.getElementById('perhitunganpph').value=Math.floor(hasil);
                        }
                    }
                    else {
                        busy_off();
                        error_catch(con.status);
                    }
              }
     }

    
}
function getNilaiInvoiceA()
{
    jns = document.getElementById('tipeinvoice').value;
    totalnetto = document.getElementById('TotalNetto').innerHTML;
    totalnilai = document.getElementById('TotalNilai').innerHTML;
    nopo = document.getElementById('nopo').value;
    kodesupplier = document.getElementById('kodesupplier').value;
    tanggal = document.getElementById('tanggal').value;
    console.log(tanggal);
   
    param='totalnetto='+totalnetto+'&totalnilai='+totalnilai+'&jns='+jns+'&notransaksi='+nopo+'&kodesupplier='+kodesupplier+'&tgl='+tanggal+'&nopo='+nopo;
        tujuan='keu_slave_tagihan.php';
       post_response_text(tujuan+'?'+'proses=getNilaiInvoice', param, respog);
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
                           
                            document.getElementById('nilaiinvoice').value=con.responseText;
                        }
                    }
                    else {
                        busy_off();
                        error_catch(con.status);
                    }
              }
     }

    
}
function getNilaiInvoice()
{
    nilaiI = document.getElementById('nilaiinvoice').value;
    nilaiinvoice =  nilaiI.replace(/,/g, ''); 
   potmutu = document.getElementById('potmutu').value;
   if(potmutu=='0'){
    var potmutujumlah = document.getElementById('potmutujml').value;
   }else{
    var potmutujumlah = (potmutu/100)*(nilaiinvoice);
   }
    
   var  potsusutjumlah = document.getElementById('potsusutjml').value;
   var  nilaippn =    document.getElementById('nilaippn').value;
   var  nilaipph =    document.getElementById('perhitunganpph').value;
   var totalpot = (((parseFloat(potmutujumlah)) + (parseFloat(potsusutjumlah))));
   var nilaiInvoiceBersih = (parseFloat(nilaiinvoice))-(totalpot); 
    var nilaiinvoiceA = (((parseFloat(nilaiInvoiceBersih))+(parseFloat(nilaippn)))- (parseFloat(nilaipph)));
     console.log(nilaiInvoiceBersih);
    document.getElementById('nilaiinvoiceA').value=nilaiinvoiceA;
    
}

function getDataTimbangan(notransaksi,jns,kodesupplier){
   
    console.log('getdata');
    param='notransaksi='+notransaksi+'&jns='+jns+'&kodesupplier='+kodesupplier;
        tujuan='keu_slave_tagihan.php';
       post_response_text(tujuan+'?'+'proses=preview', param, respog);
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
                                    console.log('eksekusi');
                                  
                                   document.getElementById('printContainer').innerHTML=con.responseText;
                                    getNilaiInvoiceA();
                                  
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
     }

    
}
function postingData(row)
{
    noinvoice=document.getElementById('noinvoice_'+row).innerHTML;
    param='noinvoice='+noinvoice;
        tujuan='keu_slave_tagihanPosting.php';
        if(confirm('Anda yakin dokumen telah lengkap..?'))
             post_response_text(tujuan+'?'+'proses=getPo', param, respog);
    
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
                                        x=document.getElementById('tr_'+row);
                                        x.cells[9].innerHTML=con.responseText;
                                        x.cells[12].innerHTML="<img class='zImgBtn' title=Lengkap' src='images/skyblue/posted.png'>";
                                        x.cells[11].innerHTML='';
                                        x.cells[10].innerHTML='';
                                        
                                    
                            }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
            }
     }    
}

function detailPDF1(numRow,ev) {
    // Prep Param
    var notransaksi = document.getElementById('noinvoice_'+numRow).getAttribute('value'),
        param = "proses=pdf&noinvoice="+notransaksi;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_tagihan_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}
function SimpanLoopingDataTiket()
{
//
	maxRow = kodebarang=document.getElementById('MaxRow').innerHTML;
	if(confirm('apakah anda yakin ?'))
	{
		   loopSimpanDt(1,maxRow);
		  
	}
	
}




function loopSimpanDt(currRow,maxRow)
{
   
	notiket=document.getElementById('notiket'+currRow).innerHTML;
    nospb=document.getElementById('nospb'+currRow).innerHTML;
    noinvoice=document.getElementById('noinvoice').value;
   
    param='notiket='+notiket+'&nospb='+nospb+'&noinvoice='+noinvoice;
    //alert(param);
    tujuan='keu_slave_tagihan.php';
    post_response_text(tujuan+'?'+'proses=simpanDt', param, respog);
	document.getElementById('row_'+currRow).style.backgroundColor='orange';
	lockScreen('wait');
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
					if(con.responseText==1)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='green';
						currRow+=1;
					}
					else if(con.responseText==0)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='red';
						currRow+=1;
					}
					else
					{
						alert("Error");
						tutupProses();
						//unlockScreen();
						
					}
					if(currRow>maxRow)
					{
						
						tutupProses('simpan');
                        window.location.reload();	
					}  
					else
					{
						loopSimpanDt(currRow,maxRow);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
				unlockScreen();
			}
		}
	}		
	
}
function tutupProses(x)
{
	
		if (x == 'simpan') {
			unlockScreen();
			alert("Data Telah Telah Tersimpan !!!");
			
		}
		else
		{
			unlockScreen();
		}
	
		
}