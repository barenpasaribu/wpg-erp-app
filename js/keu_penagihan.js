function saveData(fileTarget,passParam) {
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    tipe=document.getElementById('tipe').value;
    if( tipe=='0' || tipe=='2' || tipe=='3'){
        maxRow=document.getElementById('MaxRow').innerHTML;
        if(maxRow =="" || maxRow=='0'){
            alert('Tidak ada data timbangan yang diambil, Untuk tipe penagihan biasa dan pelunasan harus mengambil data timbangan !')
        
        }
        
    }
	
  //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    console.log('sini')
                    
                if(tipe=='0' || tipe =='2' || tipe=='3'){
                    //if(maxRow==null){
                        loopSimpanTiket(1,maxRow);
                   // }else{
                   //     loadData(0);
                   //     cancelData();
                   // }
                }else{
                    console.log('masuk')
                    loadData();
                    cancelData();
                }
                 //   
                   // cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(fileTarget+'.php', param, respon);

}

function loopSimpanTiket(currRow,maxRow)
{
  
    notiket=document.getElementById('notiket'+currRow).innerHTML;
    nodo=document.getElementById('nodo'+currRow).innerHTML;
    kontrkno=document.getElementById('kntrkno'+currRow).innerHTML;
    noinvoice=document.getElementById('noinvoice').value;
    param+='&proses=simpanDt';
    param+='&notiket='+notiket+'&nodo='+nodo+'&kontrkno='+kontrkno+'&noinvoice='+noinvoice;
    //alert(param);
    tujuan = 'keu_slave_penagihan.php';
    //alert(param);

    if(document.getElementById('chk'+currRow).checked==true){
    
    post_response_text(tujuan, param, respog);
    
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
                        console.log('no')
                       
                        //unlockScreen();
                        
                    }
                    if(currRow>maxRow)
                    {
                      
                        tutupProses();
                        console.log('yes')
                        loadData();
                        cancelData();
                    }  
                    else
                    {
                        console.log(currRow+' '+maxRow)
                        loopSimpanTiket(currRow,maxRow);
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


    }else{
        currRow+=1;
        if(currRow>maxRow)
        {
            tutupProses('simpan');
            console.log('yes')
            loadData();
            cancelData();
        }  else {
            console.log(currRow+' '+maxRow)
            loopSimpanTiket(currRow,maxRow);
        }

    }

           
    
}
function tutupProses(x)
{
  
        if (x == 'simpan') {
            unlockScreen();
            console.log('disini')
            alert("Data Tersimpan");
           loadData(0);
        }
        else
        {
            unlockScreen();
        }
    
}
function displayFormInput(){
        clearData();
        param='proses=genNo';
	tujuan='keu_slave_penagihan';
        post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                        document.getElementById('formInput').style.display='block';
                        document.getElementById('printArea').style.display='block';
                        document.getElementById('listData').style.display='none';
                        document.getElementById('noinvoice').value=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getNoT(){
       tanggal = document.getElementById('tanggal').value;
        param='proses=genNo'+'&tanggal='+tanggal;
    tujuan='keu_slave_penagihan';
        post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                      
                        document.getElementById('noinvoice').value=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getPage(){
    pg=document.getElementById('pages');
    pg=pg.options[pg.selectedIndex].value;
    paged=parseFloat(pg)-1;
    loadData(paged);
}
function cariData(pg){
    ntrs=document.getElementById('txtsearch').value;
    tglcr=document.getElementById('tgl_cari').value;
    param='proses=loadData'+'&page='+pg;
    if(ntrs!=''){
        param+='&noinvoice='+ntrs;
    }
    if(tglcr!=''){
        param+='&tanggalCr='+tglcr;
    }
    tujuan='keu_slave_penagihan.php';
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
                else {
                        isdt=con.responseText.split("####");
                        document.getElementById('formInput').style.display='none';
                        document.getElementById('listData').style.display='block';
                        document.getElementById('continerlist').innerHTML=isdt[0];
                        document.getElementById('footData').innerHTML=isdt[1];
                        
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
}
function loadData(page){
    ntrs=document.getElementById('txtsearch').value;
    tglcr=document.getElementById('tgl_cari').value;
    param='proses=loadData'+'&page='+page;
    if(ntrs!=''){
        param+='&noinvoice='+ntrs;
    }
    if(tglcr!=''){
        param+='&tanggalCr='+tglcr;
    }
    tujuan='keu_slave_penagihan.php';
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
                else {
                        isdt=con.responseText.split("####");
                        document.getElementById('formInput').style.display='none';
                        document.getElementById('printArea').style.display='none';
                        document.getElementById('listData').style.display='block';
                        document.getElementById('uangmuka').type ='hidden';
                        document.getElementById('akunuangmuka').style.display='none';
                        document.getElementById('lbluangmuka').style.display='none';
                       // document.getElementById('lblakunuangmuka').style.display='none';
                        document.getElementById('continerlist').innerHTML=isdt[0];
                        document.getElementById('footData').innerHTML=isdt[1];
                        clearData();
                        closeDialog();
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
}
function fillField(noinv){
    param='proses=getData'+'&noinvoice='+noinv;
    tujuan='keu_slave_penagihan.php';
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
                else {
                    //alert(con.responseText);
                        document.getElementById('formInput').style.display='block';
                        document.getElementById('listData').style.display='none';
                        isis=con.responseText.split("###");
                        document.getElementById('noinvoice').value=isis[0];
                        document.getElementById('kodeorganisasi').value=isis[1];
                        document.getElementById('tanggal').value=isis[2];
                        document.getElementById('noorder').value=isis[3];
                        kdcst=document.getElementById('kodecustomer');
                        for(a=0;a<kdcst.length;a++){
                            if(kdcst.options[a].value==isis[4]){
                                    kdcst.options[a].selected=true;
                                }
                        }
                        document.getElementById('nilaiinvoice').value=isis[5];
                        document.getElementById('nilaippn').value=isis[6];
                        document.getElementById('jatuhtempo').value=isis[7];
                        document.getElementById('keterangan').value=isis[8];
                        byrke=document.getElementById('bayarke');
                        for(a=0;a<byrke.length;a++){
                            if(byrke.options[a].value==isis[9]){
                                    byrke.options[a].selected=true;
                                }
                        }
                        dbt=document.getElementById('debet');
                        for(a=0;a<dbt.length;a++){
                            if(dbt.options[a].value==isis[10]){
                                    dbt.options[a].selected=true;
                                }
                        }
                        kridit=document.getElementById('kredit');
                        for(a=0;a<kridit.length;a++){
                            if(kridit.options[a].value==isis[11]){
                                    kridit.options[a].selected=true;
                                }
                        }
                        document.getElementById('uangmuka').value=isis[12];
                        
                         
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
}

function getAkun(){
    clearData();
    tipe=document.getElementById('tipe').value;
    param='proses=getAkun'+'&tipe='+tipe;
    tujuan='keu_slave_penagihan.php';
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
                else {
                    isis=con.responseText.split("####");
                   // alert(isis);
                    document.getElementById('debet').value=isis[0];
                    document.getElementById('kredit').value=isis[1];
                    if(tipe==3){
                      document.getElementById('akunuangmuka').value='2140101';  
                    }
                    if(tipe==1){
                    document.getElementById('uangmuka').type ='hidden';
                    document.getElementById('tonase').readonly ='false';
                    document.getElementById('akunuangmuka').style.display='none';   
                    //document.getElementById('lblakunuangmuka').style.display='none';
                    document.getElementById('lbluangmuka').style.display='none';
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
//jamhari
function searchNosibp(title,content,ev){
	width='400';
	height='520';
	showDialog1(title,content,width,height,ev);
        getFormNosibp();
	//alert('asdasd');
}
function getFormNosibp(){
        param='proses=getFormNosipb';
        tujuan='keu_slave_penagihan.php';
        post_response_text(tujuan+'?'+'', param, respog);
	
	function respog(){
              if(con.readyState==4){
                if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                                alert('ERROR TRANSACTION,\n' + con.responseText);
                        }
                        else {
                                //alert(con.responseText);
                                document.getElementById('formPencariandata').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }
	 }
} 
function findNosipb(){
	txt=trim(document.getElementById('nosipbcr').value);
	param='txtfind='+txt+'&proses=getnosibp';
        tujuan='keu_slave_penagihan.php';
        if(txt==''){
            alert("Nosipb is obligatory");
        } else {
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

function setData(nosibp,kdcust,nokontrak){
    document.getElementById('noorder').value=nosibp;
    document.getElementById('nokontrak').value=nokontrak;
     tanggal = document.getElementById('tanggal').value;
    kridit=document.getElementById('kodecustomer');
    for(a=0;a<kridit.length;a++){
        if(kridit.options[a].value==kdcust){
                kridit.options[a].selected=true;
            }
    }
    kridit.disabled=true;
     nodo = document.getElementById('noorder').value;
     tipe = document.getElementById('tipe').value;
     if(tipe=='0' || tipe=='2'){
        console.log('1');
        
         param='sipb='+nodo+'&proses=getDataDetail'+'&tanggal='+tanggal;
        tujuan='keu_slave_penagihan.php';
        post_response_text(tujuan, param, respog);
    }else if(tipe =='1'){
        console.log('2');
       param='sipb='+nodo+'&proses=getDataDetail1'+'&tanggal='+tanggal;
        tujuan='keu_slave_penagihan.php';
        post_response_text(tujuan, param, respog); 
    }else if(tipe =='3'){
        console.log('3');
       param='sipb='+nodo+'&proses=getDataDetail2'+'&tanggal='+tanggal;
        tujuan='keu_slave_penagihan.php';
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
                                    ss=con.responseText.split(",");
                                    document.getElementById('noinvoice').value=ss[1];

                                    if(tipe =='0'){
                                        console.log('1');
                                      getDataTimbangan(nosibp);  
                                       document.getElementById('uangmuka').type ='hidden';
                                       document.getElementById('akunuangmuka').style.display='none';
                                        document.getElementById('lbluangmuka').style.display='none';

                                    document.getElementById('nilaiinvoice').value=ss[0];
                                    document.getElementById('tonase').value=ss[2];
                                    document.getElementById('hargasatuan').value=ss[3];
                                    document.getElementById('jumlah').value=ss[2]*ss[3];
                                      //  document.getElementById('lblakunuangmuka').style.display='none';
                                    }else if(tipe =='1'){
                                         console.log('2');
                                        document.getElementById('uangmuka').type ='text';
                                        document.getElementById('akunuangmuka').style.display='block';
                                        document.getElementById('lbluangmuka').style.display='block';
                                       // document.getElementById('lblakunuangmuka').style.display='block';
                                        getDataTimbangan(nosibp);
                                    }else if(tipe =='2'){
                                         console.log('2');
                                        document.getElementById('uangmuka').type ='text';
                                        document.getElementById('akunuangmuka').style.display='block';
                                        document.getElementById('lbluangmuka').style.display='block';
                                       // document.getElementById('lblakunuangmuka').style.display='block';
                                        getDataTimbangan(nosibp);  
                                    }else if (tipe =='3'){
                                         console.log('3');
                                        document.getElementById('uangmuka').value=ss[4];
                                        document.getElementById('uangmuka').type ='text';
                                        document.getElementById('akunuangmuka').style.display='block';
                                        document.getElementById('lbluangmuka').style.display='block';
                                       // document.getElementById('lblakunuangmuka').style.display='block';
                                        getDataTimbangan(nosibp); 
                                    document.getElementById('nilaiinvoice').value=ss[0];
                                    document.getElementById('tonase').value=ss[2];
                                    document.getElementById('hargasatuan').value=ss[3];
                                    document.getElementById('jumlah').value=ss[2]*ss[3];
                                  
                                  
                                    }
                                    
                                    closeDialog();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
     }

    
}
function getDataTimbangan(nosibp){
   
  
    param='nodo='+nosibp+'&proses=preview';
        tujuan='keu_slave_penagihan.php';
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
                            else {
                                    //alert(con.responseText);
                                   document.getElementById('printContainer').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
     }

    
}
function GetPPN(){
    nilaiinvoice = document.getElementById('dpp').value;
    if(document.getElementById('cekppn').checked == true){
        nilaippn = nilaiinvoice * (10/100);
      document.getElementById('nilaippn').value=nilaippn;
    }
    if(document.getElementById('cekppn').checked == false){
      document.getElementById('nilaippn').value="0";
    }
    // else{
    //     document.getElementById('nilaippn').value='0';
    // }


    
}
function GetPPH(){
    cust = document.getElementById('kodecustomer').value;
    nilaiinvoice = document.getElementById('dpp').value;
    if(cust =='' || cust =='0'){
        alert('Kode Pelanggan belum di pilih ,Mohon Pilih DO untuk mengisi Kode Pelanggan !')
    }else{
      param='cust='+cust+'&nilaiinvoice='+nilaiinvoice+'&proses=getDataPPh';
        tujuan='keu_slave_penagihan.php';
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
                                    ss=con.responseText;
                                    document.getElementById('nilaipph').value=ss;
                                   
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
     }
    // else{
    //     document.getElementById('nilaippn').value='0';
    // }
   
}

function hitungPotSusutInt(){
    nokontrak=document.getElementById('nokontrak').value;
    kgpotsusut=document.getElementById('potongsusutkgint').value;
    if(kgpotsusut==''){
        kgpotsusut= '0';
    }
    if(nokontrak==''){
        alert('Isi No SIPB dulu !!')
    }else{

    param='nokontrak='+nokontrak+'&proses=getHslSusut'+'&kgpotsusut='+kgpotsusut;
        tujuan='keu_slave_penagihan.php';
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
                                    document.getElementById('potongsusutjmlint').value=con.responseText;
                                    closeDialog();
                                    hitungakhir();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
     }

    
}


function hitungakhir(){
    potongsusutjmlint=document.getElementById('potongsusutjmlint').value;
    potongsusutjmlext=document.getElementById('potongsusutjmlext').value;
    potongmutuint=document.getElementById('potongmutuint').value;
    potongmutuext=document.getElementById('potongmutuext').value;
    nilaiinvoice=document.getElementById('nilaiinvoice').value;
    uangmuka=document.getElementById('uangmuka').value;

    tonase=document.getElementById('tonase').value;
    hargasatuan=document.getElementById('hargasatuan').value;
    hasil=0;

    if(potongsusutjmlint==''){
        potongsusutjmlint= '0';
    }
    if(potongsusutjmlext==''){
        potongsusutjmlext= '0';
    } 
    if(potongmutuint==''){
        potongmutuint= '0';
    } 
    if(potongmutuext==''){
        potongmutuext= '0';
    } 
    if(nilaiinvoice==''){
        nilaiinvoice= '0';
    }
    if(uangmuka==''){
        uangmuka= '0';
    }
    if(tonase==''){
        tonase= '0';
    }
    if(hargasatuan==''){
        hargasatuan= '0';
    }

    document.getElementById('jumlah').value=parseInt(tonase)*parseInt(hargasatuan);
    document.getElementById('nilaiinvoice').value=parseInt(tonase)*parseInt(hargasatuan);
    hasil=parseInt(nilaiinvoice)-parseInt(uangmuka)-parseInt(potongsusutjmlint)-parseInt(potongsusutjmlext)-parseInt(potongmutuint)-parseInt(potongmutuext);
    document.getElementById('dpp').value=hasil;
    
}

function hitungTonase(){
    var maxRow=document.getElementById('MaxRow').innerHTML;
    totaltonase=0;
 //   alert(maxRow);
    for(i=1;i<=maxRow;i++) {
        tonase=0;
        if(document.getElementById('chk'+i).checked==true){

        tonase=document.getElementById('netto'+i).innerHTML;
        totaltonase=parseInt(totaltonase)+parseInt(tonase);
        }
    }
    document.getElementById('tonase').value=totaltonase;

    tonase=document.getElementById('tonase').value;
    hargasatuan=document.getElementById('hargasatuan').value;
    hasil=parseInt(tonase)*parseInt(hargasatuan);    
    document.getElementById('jumlah').value=hasil;
    document.getElementById('nilaiinvoice').value=hasil;
    document.getElementById('dpp').value=hasil;
}

function hitungPotSusutExt(){
    nokontrak=document.getElementById('nokontrak').value;
    kgpotsusut=document.getElementById('potongsusutkgext').value;
    if(kgpotsusut==''){
        kgpotsusut= '0';
    }
    if(nokontrak==''){
        alert('Isi No SIPB dulu !!')
    }else{

    param='nokontrak='+nokontrak+'&proses=getHslSusut'+'&kgpotsusut='+kgpotsusut;
        tujuan='keu_slave_penagihan.php';
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
                                    document.getElementById('potongsusutjmlext').value=con.responseText;
                                    closeDialog();
                                    hitungakhir();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
     }

    
}

function cancelData(){
//    $arr="##noinvoice##jatuhtempo##kodeorganisasi##nofakturpajak##tanggal##bayarke";
//    $arr.="##kodecustomer##uangmuka##noorder##nilaippn##keterangan##nilaiinvoice##debet##kredit";
document.getElementById('formInput').style.display='none';
document.getElementById('listData').style.display='block';
document.getElementById('uangmuka').type ='hidden';
document.getElementById('akunuangmuka').style.display='none';
//document.getElementById('lblakunuangmuka').style.display='none';
document.getElementById('lbluangmuka').style.display='none';
document.getElementById('printContainer').innerHTML='';
clearData();
}
function clearData(){
document.getElementById('jatuhtempo').value='';
document.getElementById('nofakturpajak').value='';
document.getElementById('tanggal').value='';
document.getElementById('bayarke').value='';
document.getElementById('kodecustomer').value='';
document.getElementById('uangmuka').value='';
document.getElementById('noorder').value='';
document.getElementById('nilaippn').value='';
document.getElementById('keterangan').value='';
document.getElementById('nilaiinvoice').value='';
document.getElementById('debet').value='';
document.getElementById('kredit').value='';
document.getElementById('txtsearch').value="";
document.getElementById('tgl_cari').value="";
document.getElementById('potongmutuint').value="";
document.getElementById('potongmutuext').value="";
document.getElementById('potongsusutkgint').value="";
document.getElementById('potongsusutkgext').value="";
document.getElementById('potongsusutjmlint').value="";
document.getElementById('potongsusutjmlext').value="";

document.getElementById('nokontrak').value="";
document.getElementById('tonase').value="";
document.getElementById('hargasatuan').value="";
document.getElementById('jumlah').value="";
document.getElementById('dpp').value="";
document.getElementById('akunuangmuka').value="";
}
function delData(notrans){
        param='noinvoice='+notrans+'&proses=delData';
        tujuan='keu_slave_penagihan.php';  
        if(confirm("Anda yakin menghapus no invoice ini?"+ notrans)){
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
                                    getPage();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
	 }
}
function postingData(notrans){
        param='noinvoice='+notrans+'&proses=postingData';
        tujuan='keu_slave_penagihan.php';  
        if(confirm("Anda yakin memposting no invoice ini?"+ notrans)){
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
                                    getPage();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }
	 }
}
function detailPDF(numRow,ev) {
    // Prep Param
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var noakun = document.getElementById('noakun_'+numRow).getAttribute('value');
    var tipetransaksi = document.getElementById('tipetransaksi_'+numRow).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+numRow).getAttribute('value');
    param = "proses=pdf&notransaksi="+notransaksi+"&kodeorg="+kodeorg+
        "&tipetransaksi="+tipetransaksi+"&noakun="+noakun;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_kasbank_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}