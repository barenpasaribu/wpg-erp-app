function loadNotifikasi()
{
        proses="getNotifikasi";
        param="method="+proses;
        tujuan="log_slave_save_permintaan_harga.php";
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
                                                        document.getElementById('notifikasiKerja').innerHTML=con.responseText;
                                        }
                        }
                        else {
                                        busy_off();
                                        error_catch(con.status);
                        }
                }
        }

}


function displayFormInput()
{
        document.getElementById('formPP').style.display='block';
        document.getElementById('list_permintaan').style.display='none';
        //document.getElementById('formEditData2').style.display='none';
        //document.getElementById('nopp').value='';
        document.getElementById('listBrgPP').style.display='none';
        //document.getElementById('tmblGetpp').disabled=false;
        document.getElementById('formPP2').style.display='none';
        //document.getElementById('printContainer').innerHTML='';
        document.getElementById('listBrgPP').style.display='none';
        document.getElementById('listSupplier').style.display='none';
        document.getElementById('dataBarang').innerHTML='';
        document.getElementById('supplierForm').style.display='none';
        document.getElementById('listHasilSave').innerHTML='';
        //document.getElementById('excelBtn').style.display='none';
        document.getElementById('noUrut').value='';
}
function displayList()
{
        document.getElementById('formPP').style.display='none';
        document.getElementById('formPP2').style.display='none';
        //document.getElementById('printContainer').innerHTML='';
        document.getElementById('list_permintaan').style.display='block';
        document.getElementById('txtsearch').value='';
        document.getElementById('tgl_cari').value='';
        //document.getElementById('txtnopp').value='';
        //document.getElementById('txtnmbrg').value='';
        document.getElementById('formEditData2').style.display='none';
        get_data();
        //loadEmployeeList();
}
function displayFormEdit()
{
        document.getElementById('formPP2').style.display='block';
        document.getElementById('list_permintaan').style.display='none';
        //document.getElementById('nopp').value='';
        document.getElementById('listBrgPP').style.display='none';
        //document.getElementById('tmblGetpp').disabled=false;
        //document.getElementById('formEditData').style.display='none';
        document.getElementById('printContainer').innerHTML='';
        document.getElementById('formPP').style.display='none';
        document.getElementById('formEditData2').style.display='none';
}
function searchSupplier(title,content,ev)
{
    width='500';
    height='400';
    showDialog1(title,content,width,height,ev);
}
function searchNopp(title,content,ev)
{
    width='500';
    height='400';
    showDialog2(title,content,width,height,ev);
}

function clikcAll()
{
    drt=document.getElementById('dtSemua');
    if(drt.checked==true)
        {
            chk=true;
        }
        else
            {
                chk=false;
            }
    var tbl = document.getElementById("dataBarang");
    var row = tbl.rows.length;
     row=row-1;

    for(i=1;i<=row;i++)
    {
        document.getElementById('pilBrg_'+i).checked=chk;
    }
}
function findSupplier()
{
    nmSupplier=document.getElementById('nmSupplier').value;
    param='method=getSupplierNm'+'&nmSupplier='+nmSupplier;
    tujuan='log_slave_save_permintaan_harga.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerSupplier').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function getDtPP(kdunit)
{
    displayFormInput();
    param='method=getBarangPP'+'&kdPt='+kdunit;
    tujuan='log_slave_save_permintaan_harga.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('listBrgPP').style.display='block';
                                  document.getElementById('listSupplier').style.display='none';
                                  document.getElementById('dataBarang').innerHTML=con.responseText;
                                  document.getElementById('noUrut').value=1;
                                  document.getElementById('notransaksi').value='';
                                  document.getElementById('dtSemua').checked=false;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	

}

function lanjutAdd()
{
    var tbl = document.getElementById("dataBarang");
    var row = tbl.rows.length;
    row=row-1;
        //alert(row);
        strUrl = '';
    for(i=1;i<=row;i++)
        {
            ar=document.getElementById('pilBrg_'+i);
            if(ar.checked==true)
            {
            //alert(i);           
                    try{
                            if(strUrl != '')
                            {
                                strUrl +='&kdbrg[]='+trim(document.getElementById('kodebrg_'+i).innerHTML)
                                       +'&lstnopp[]='+trim(document.getElementById('nopplst_'+i).innerHTML);
                            }
                            else
                            {
                               strUrl +='&kdbrg[]='+trim(document.getElementById('kodebrg_'+i).innerHTML)
                                      +'&lstnopp[]='+trim(document.getElementById('nopplst_'+i).innerHTML);
                            }
                    }
                    catch(e){}

            }
        }
        if(strUrl=='')
        {
                alert('Choose one');
                return;
        }
        param='method=cekBarang'+'&baris='+i;
        param+=strUrl;
        tujuan='log_slave_save_permintaan_harga.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                    document.getElementById('listBrgPP').style.display='none';
                                    document.getElementById('listSupplier').style.display='none';
                                    document.getElementById('supplierForm').style.display='block';
                            }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	

}
function lanjutAdd2()
{
    document.getElementById('listSupplier').style.display='none';
    document.getElementById('supplierForm').style.display='block';
}
function addDataSma()
{
    idsp=document.getElementById('id_supplier').options[document.getElementById('id_supplier').selectedIndex].value;
    var tbl = document.getElementById("dataBarang");
    var row = tbl.rows.length;
     row=row-1;

    strUrl = '';
    for(i=1;i<=row;i++)
    {
          ar=document.getElementById('pilBrg_'+i);
          if(ar.checked==true)
          {
            try{
                    if(strUrl != '')
                    {
                           strUrl +='&kdbrg[]='+trim(document.getElementById('kodebrg_'+i).innerHTML)
                                    +'&lstnopp[]='+trim(document.getElementById('nopplst_'+i).innerHTML)
                                    +'&jmlh[]='+trim(document.getElementById('jumlah_'+i).innerHTML);
                    }
                    else
                    {
                            strUrl +='&kdbrg[]='+trim(document.getElementById('kodebrg_'+i).innerHTML)
                                   +'&lstnopp[]='+trim(document.getElementById('nopplst_'+i).innerHTML)
                                   +'&jmlh[]='+trim(document.getElementById('jumlah_'+i).innerHTML);
                    }
                }
                 catch(e){}

            }
     }
     nor=document.getElementById('noUrut').value;
     notran=document.getElementById('notransaksi').value;
     param='method=addData'+'&id_supplier='+idsp+'&norurut='+nor;
     param+='&notransaksi='+notran;
     param+=strUrl;

     tujuan='log_slave_save_permintaan_harga.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                 isiTran=con.responseText.split("###");
                                 document.getElementById('notransaksi').value=isiTran[0];
                                 document.getElementById('noUrut').value=isiTran[1];
                                 loadSupplier();
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	

}
function loadSupplier()
{
        notrans=document.getElementById('notransaksi').value;
        param='method=loadSuppier'+'&notrans='+notrans;
        tujuan = 'log_slave_save_permintaan_harga.php';
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
                                                document.getElementById('listHasilSave').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }	
}
function delPer(no_per,nourut)
{
    param='no_permintaan='+no_per+'&nourut='+nourut;
    param+='&method=deleted';
    tujuan='log_slave_save_permintaan_harga.php';

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
                               // document.getElementById('contain').innerHTML=con.responseText;
                                alert('Delete successfull');
                                loadSupplier();
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
      }
         }
         if(confirm('Delete, are you sure?'))
                post_response_text(tujuan, param, respog);
}
function delPer1(no_per,nourut)
{
    param='no_permintaan='+no_per+'&nourut='+nourut;
    param+='&method=deleted';
    tujuan='log_slave_save_permintaan_harga.php';

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
                               // document.getElementById('contain').innerHTML=con.responseText;
                                alert('Delete successfull');
                               get_data();
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
      }
         }
         if(confirm('Delete, are you sure?'))
                post_response_text(tujuan, param, respog);
}
function slsiSma()
{
    if(confirm("Finish, are you sure?"))
    document.getElementById('listBrgPP').style.display='none';
    document.getElementById('listSupplier').style.display='none';
    document.getElementById('tmblGetpp').disabled=false;
    document.getElementById('supplierForm').style.display='none';
    zPreview('log_slave_2perbandingan_harga','##nopp##formPil','printContainer');
    //displayList();
}
function findNopp()
{
    kdNopp=document.getElementById('kdNopp').value;
    param='method=getNopp'+'&kdNopp='+kdNopp;
    tujuan='log_slave_save_permintaan_harga.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerNopp').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setData(kdSupp)
{
    l=document.getElementById('id_supplier');

    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==kdSupp)
                {
                    l.options[a].selected=true;
                }
        }
       closeDialog();
}
function findNopp2()
{
    kdNopp=document.getElementById('kdNopp').value;
    param='method=getNopp2'+'&kdNopp='+kdNopp;
    tujuan='log_slave_save_permintaan_harga.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerNopp').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setDataNopp(brNopp)
{
    document.getElementById('nopp').value=brNopp;
    closeDialog2();
}
function get_data()
{
        param='method=cari_pp'
        tujuan = 'log_slave_save_permintaan_harga.php';
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
                                                   document.getElementById('contain').innerHTML=con.responseText;
                                                   loadNotifikasi();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }		
}
function headher_permintaan()
{
        nm_supp=document.getElementById('id_supplier').options[document.getElementById('id_supplier').selectedIndex].value;
        //nopp=document.getElementById('nopp').options[document.getElementById('nopp').selectedIndex].value;

        nopp=document.getElementById('nopp').value;
        term_pay=document.getElementById('term_pay').options[document.getElementById('term_pay').selectedIndex].value;
        tmpt_krm=document.getElementById('tmpt_krm').options[document.getElementById('tmpt_krm').selectedIndex].value;
        stockId=document.getElementById('stockId').options[document.getElementById('stockId').selectedIndex].value;

        if(nm_supp=='')	
        {
                alert('Please select supplier');
                return;
        }

        else if(nopp=='')
        {
                alert('PR no is empty');
                return;
        }

        else if(term_pay=='')
        {
                alert('Payment term is empty');
                return;
        }

        else if(tmpt_krm=='')
        {
                alert('Delivery location required');	
                return;
        }

        else if(stockId=='')
        {
                alert('Stock is empty');
                return;
        }
        //tmpt_krm
        //stockId


        else
        {
                document.getElementById('dtHeader').style.display='none';
                //document.getElementById('tmbl_save').disabled=true;
                //document.getElementById('tmbl_cancel').disabled=true;
                document.getElementById('form_permintaan').style.display='block';
                document.getElementById('tmbl_all').style.display='block';
                met=document.getElementById('method').value='create_no';
                param='method='+met;
                //alert(param);
                tujuan='log_slave_save_permintaan_harga.php';
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
                                                                document.getElementById('no_prmntan').value=con.responseText;
                                                                document.getElementById('method').value='insert';
                                                                document.getElementById('detailTable').style.display='block';

                                                                 document.getElementById('formDetailIsian').style.display='block';

                                                                pass2detail(1);
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }
                 }
                post_response_text(tujuan, param, respog);
        }
}

function pass2detail(c) {
    if(c==1)
        {
            var kode = document.getElementById('nopp');
            idPer=document.getElementById('no_prmntan').value;
            param = "id="+kode.value+'&saveStat='+c+'&idPer='+idPer;
            param += "&proses=createTable"
        }
        else
{
    var kode = document.getElementById('no_prmntan');
    param = "id="+kode.value;
    param += "&proses=createTable";
}
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var detailDiv = document.getElementById('detailTable');
                    detailDiv.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('log_slave_permintaan_detail.php', param, respon);
}
function searchBrg(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function findBrg()
{
        txt=trim(document.getElementById('no_brg').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Too short');	
        }
        else
        {
                param='txtfind='+txt;
                tujuan='log_slave_get_brg.php';
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
function setBrg(no_brg,namabrg,satuan,nomor)
{
         nomor=document.getElementById('nomor').value;
     document.getElementById('kd_brg_'+nomor).value=no_brg;
        if( document.getElementById('oldKdbrg_'+nomor).value=='')
        {
                document.getElementById('oldKdbrg_'+nomor).value=no_brg;
        }
         document.getElementById('nm_brg_'+nomor).value=namabrg;
         document.getElementById('sat_'+nomor).value=satuan;
         getSpek(no_brg,nomor);
         closeDialog();
}
function getSpek(kodebarang,id)
{
        kdBrg=kodebarang;
        param='method=getSpek'+'&kdbrg='+kdBrg;
        tujuan='log_slave_save_permintaan_harga.php';
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
                                                        document.getElementById('spek_'+id).value=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }
         }

}

function clear_all_data()
{
        document.getElementById('form_permintaan').style.display='none';
        document.getElementById('list_permintaan').style.display='block';
        document.getElementById('no_prmntan').value='';
        //document.getElementById('nm_supplier').value='';
        document.getElementById('id_supplier').value='';
        stat_input=0;
}
stat_input=0;
stat_inputc=0;
function edit_header()
  {
        //alert(strUrl);

        stats=document.getElementById('method');
        if(stat_input==1)
        {
                no_per = trim(document.getElementById('no_prmntan').value);
                supplier_id = trim(document.getElementById('id_supplier').value);
                method=document.getElementById('method').value;
                method='update';
                param='no_permintaan='+no_per+'&id_supplier='+supplier_id; //+'&rkd_org='+rkd_org;
                param+='&method='+method;
                //param+=strUrl;
                /*alert(param);
                return;*/
                tujuan='log_slave_save_permintaan_harga.php';
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
                                                                        clear_all_data();
                                                                        displayList();
                                                                        //document.getElementById('contain').innerHTML=con.responseText;
                                                                        //alert('Saved succeed !!');
                                                                        //clear_all_data();
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 } 	
                 //post_response_text(tujuan, param, respog);
                        var answer =confirm('Edit header, are you sure ?');
                        if (answer){
                        post_response_text(tujuan, param, respog);
                        }
                        else{
                        clear_all_data();
                        }
        }
        else if(stat_input==0)
        {
                //alert('insert');
                if(stat_inputc==0)
                {
                        cek_data();
                }
                else
                {
                        displayList();
                }
        }
}

function fillField(nomor,tgl,purchase,supplier_id,npp,trmPayment,lokKrm,stock,nilaiPPn) {

    document.getElementById('form_permintaan').style.display='block';
    document.getElementById('list_permintaan').style.display='none';
    document.getElementById('method').value='update';
    document.getElementById('no_prmntan').value=nomor;
    document.getElementById('tgl_prmntan').value=tgl;
    document.getElementById('purchser_id').value=purchase;
    document.getElementById('id_supplier').value=supplier_id;
    document.getElementById('nopp').value=npp;
    //
    document.getElementById('term_pay').value=trmPayment;
    document.getElementById('tmpt_krm').value=lokKrm;
    document.getElementById('stockId').value=stock;
    document.getElementById('formDetailIsian').style.display = 'block';
    document.getElementById('dtHeader').style.display = 'none';
    document.getElementById('detailTable').style.display='block';
    document.getElementById('tmbl_all').style.display = 'block';
    document.getElementById('tmbl_save').disabled=true;
    document.getElementById('tmbl_cancel').disabled=true;
    document.getElementById('criTmbl').style.display='none';

                stat_input=1;
        stat_inputb=0;
                stat_inputc=1;
                var kode = document.getElementById('no_prmntan');
                //var sup_id= documnet.getElementById('id_supplier');
                param = "id="+kode.value;
                //param += "id_supplier="+sup_id.value;
                param += "&proses=createTable";

                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                                dt=con.responseText.split("###");
                                var detailDiv = document.getElementById('detailTable');
                                document.getElementById('ketUraian').value=dt[1];
                                detailDiv.innerHTML =dt[0];// con.responseText;
                                document.getElementById('ppN').value=nilaiPPn;
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
                post_response_text('log_slave_permintaan_detail.php', param, respon);
}







function cek_data()
{
    no_prmntan=document.getElementById('detail_kode').value;
    rid_supplier = trim(document.getElementById('id_supplier').value);
    id_user = trim(document.getElementById('purchser_id').value);
    rtgl=trim(document.getElementById('tgl_prmntan').value);
    met=document.getElementById('method').value='cek_data_header';
    var tbl = document.getElementById("ppDetailTable");
    var row = tbl.rows.length;
    strUrl = '';
    for(i=0;i<row;i++)
    {
                    try{
                            if(strUrl != '')
                            {
//								ar=document.getElementById('jmlhKurs_'+i);
//								ar.value=remove_comma(ar);
//								jmlh=ar.value;
                                    strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
                                              +'&price[]='+document.getElementById('price_'+i).value
                                              +'&rspek[]='+encodeURIComponent(trim(document.getElementById('spek_'+i).value))
                                              +'&jmlh[]='+document.getElementById('jumlah_'+i).value
                                              +'&jmlhKurs[]='+document.getElementById('jmlhKurs_'+i).value;
                                              +'&kurs[]='+document.getElementById('kurs_'+i).options[document.getElementById('kurs_'+i).selectedIndex].value
                                              +'&tglDari[]='+document.getElementById('tgl_dari_'+i).value
                                              +'&tglSamp[]='+document.getElementById('tgl_smp_'+i).value;
                            }
                            else
                            {
//								ar=document.getElementById('jmlhKurs_'+i);
//								ar.value=remove_comma(ar);
//								jmlh=ar.value;
                                    strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
                                    +'&price[]='+document.getElementById('price_'+i).value
                                    +'&rspek[]='+encodeURIComponent(trim(document.getElementById('spek_'+i).value))
                                                                        +'&jmlh[]='+document.getElementById('jumlah_'+i).value
                                                                        +'&kurs[]='+document.getElementById('kurs_'+i).options[document.getElementById('kurs_'+i).selectedIndex].value
                                              +'&jmlhKurs[]='+document.getElementById('jmlhKurs_'+i).value
                                              +'&tglDari[]='+document.getElementById('tgl_dari_'+i).value
                                              +'&tglSamp[]='+document.getElementById('tgl_smp_'+i).value;

                            }
                    }
                    catch(e){}
    }
    param='ckno_permintaan='+no_prmntan+'&id_supplier='+rid_supplier+'&tgl='+rtgl+'&user_id='+id_user+'&method='+met;
    param+=strUrl;
    tujuan='log_slave_save_permintaan_harga.php';
    //alert(param);
//  return;
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
                                                  /*  alert(con.responseText);
                                                    return;*/
                                                    var id=con.responseText;
                                                    id=id-1;
                                                    switchEditAdd(id,'detail');
                                                    addNewRow('detailBody',true);
                                                    stat_inputc=1;
                                                document.getElementById('tmbl_all').innerHTML=con.responseText;
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }
     }
    /*alert(param);
    return;*/
}
function simpanSemua()
{
    //alert("masuk");
    no_prmntan=document.getElementById('detail_kode').value;
    rid_supplier = trim(document.getElementById('id_supplier').value);
    id_user = trim(document.getElementById('purchser_id').value);
    rtgl=trim(document.getElementById('tgl_prmntan').value);

    nilDiskon=document.getElementById('angDiskon').value;
//    nilDiskon.value=remove_comma(nilDiskon);
//    nilDiskon=nilDiskon.value;

    diskonPersen=document.getElementById('diskon').value;
    nilPPn=document.getElementById('hslPPn').innerHTML;

    nilaiPermintaan=document.getElementById('grand_total').value;
//    nilaiPermintaan.value=remove_comma(nilaiPermintaan);
//    nilaiPermintaan=nilaiPermintaan.value;

    subTotal=document.getElementById('total_harga_po').value;
//    subTotal.value=remove_comma(subTotal);
//    subTotal=subTotal.value;

    noPP=document.getElementById('nopp').value;
    termPay=document.getElementById('term_pay').options[document.getElementById('term_pay').selectedIndex].value;
    idFranco=document.getElementById('tmpt_krm').options[document.getElementById('tmpt_krm').selectedIndex].value;
    stockId=document.getElementById('stockId').options[document.getElementById('stockId').selectedIndex].value;
    ketUraian=document.getElementById('ketUraian').value;
    met=document.getElementById('method').value;
    if((subTotal=='0')||(subTotal==''))
        {
            subTotal=nilDiskon=diskonPersen=nilPPn=0;
        }
    var tbl = document.getElementById("ppDetailTable");
    var row = tbl.rows.length-5;
    strUrl = '';
    for(i=0;i<row;i++)
    {
                    try{
                            if(strUrl != '')
                            {

                                            strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
                                            +'&price[]='+document.getElementById('price_'+i).value
                                            +'&rspek[]='+encodeURIComponent(trim(document.getElementById('spek_'+i).value))
                                            +'&jmlh[]='+document.getElementById('jumlah_'+i).value
                                            +'&kurs[]='+document.getElementById('kurs_'+i).options[document.getElementById('kurs_'+i).selectedIndex].value
                                            +'&jmlhKurs[]='+document.getElementById('jmlhKurs_'+i).value
                                            +'&tglDari[]='+document.getElementById('tgl_dari_'+i).value
                                            +'&tglSamp[]='+document.getElementById('tgl_smp_'+i).value;
                            }
                            else
                            {

                                            strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
                                            +'&price[]='+document.getElementById('price_'+i).value
                                            +'&rspek[]='+encodeURIComponent(trim(document.getElementById('spek_'+i).value))
                                            +'&jmlh[]='+document.getElementById('jumlah_'+i).value
                                            +'&kurs[]='+document.getElementById('kurs_'+i).options[document.getElementById('kurs_'+i).selectedIndex].value
                                            +'&jmlhKurs[]='+document.getElementById('jmlhKurs_'+i).value
                                            +'&tglDari[]='+document.getElementById('tgl_dari_'+i).value
                                            +'&tglSamp[]='+document.getElementById('tgl_smp_'+i).value;

                            }
                    }
                    catch(e){}
    }
    param='ckno_permintaan='+no_prmntan+'&id_supplier='+rid_supplier+'&tgl='+rtgl+'&user_id='+id_user+'&method='+met;
    param+='&nilDiskon='+nilDiskon+'&diskonPersen='+diskonPersen+'&nilPPn='+nilPPn+'&nilaiPermintaan='+nilaiPermintaan;
    param+='&subTotal='+subTotal+'&kdNopp='+noPP+'&termPay='+termPay+'&idFranco='+idFranco+'&stockId='+stockId+'&ketUraian='+ketUraian;
    param+=strUrl;
    tujuan='log_slave_save_permintaan_harga.php';
 //   alert(param);
//  return;
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
                                                       displayList();
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }
     }
    /*alert(param);
    return;*/
}
function addDetail(id) {

        crt=document.getElementById('method');
        var detKode = document.getElementById('detail_kode');
        var rkd_brg = document.getElementById('kd_brg_'+id);
        var rprice = document.getElementById('price_'+id);
        var rspek = document.getElementById('spek_'+id);
        var jumlah = document.getElementById('jumlah_'+id);
        var kurs = document.getElementById('kurs_'+id).options[document.getElementById('kurs_'+id).selectedIndex].value;
        var jmhKurs=document.getElementById('jmlhKurs_'+id);
        var tglDari=document.getElementById('tgl_dari_'+id).value
        var tglSamp=document.getElementById('tgl_smp_'+id).value;

        var id_user = trim(document.getElementById('purchser_id').value);
        //var nopp = document.getElementById('nopp_'+id).value;
        rid_supplier = trim(document.getElementById('id_supplier').value);
        rtgl=trim(document.getElementById('tgl_prmntan').value);

        if(stat_inputc==0)
        {

                var a=confirm('Edit detail, are you sure ?');
                if(a)
                {
                        cek_data();
                }
        }
        else
        {
        //alert('test');
                        param = "proses=detail_add";
                        param += "&kode="+detKode.value;
                        param += "&kdbrg="+rkd_brg.value;
                        rprice.value=remove_comma(rprice);
                        rprice=rprice.value;
                        param += "&price="+rprice;
                        param += "&rspek="+rspek.value;
                          jumlah.value=remove_comma(jumlah);
                          jumlah=jumlah.value;
                        param += "&jmlh="+jumlah;
                        param += "&kurs="+kurs;
                        param += "&no_permintaan="+detKode.value;
                        param += "&tgl="+rtgl;
                        param += "&supplier_id="+rid_supplier;
                        param += "&user_id="+id_user;
                        jmhKurs.value=remove_comma(jmhKurs);
                        jmlhKurs=jmhKurs.value;
                        param += "&jmlhKurs="+jmlhKurs;
                        param += "&tglDari="+tglDari;
                        param += "&tglSamp="+tglSamp;
                        //param += "&nopp="+nopp;
                        tujuan='log_slave_permintaan_detail.php';
                        //alert(param);
                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                           //alert(con.responseText);
                                           stat_inputc=1;

                                           switchEditAdd(id,'detail');
                                           addNewRow('detailBody',true);


                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
                post_response_text(tujuan, param, respon);
        }

}
/* Function editDetail(id,primField,primVal)
 * Fungsi untuk mengubah data Detail
 * I : id row (urutan row pada table Detail)
 * P : Mengubah data pada tabel Detail
 * O : Notifikasi data telah berubah
 */
function editDetail(id) {
//	alert('test');
    var detKode = document.getElementById('detail_kode');
    var rkd_brg = document.getElementById('kd_brg_'+id);
    var rprice = document.getElementById('price_'+id);
    var rspek = document.getElementById('spek_'+id);
    var jumlah = document.getElementById('jumlah_'+id);
    var kurs = document.getElementById('kurs_'+id);
    var oldKdbrg=document.getElementById('oldKdbrg_'+id).value;
    var jmhKurs=document.getElementById('jmlhKurs_'+id).value;   
    var tglDari=document.getElementById('tgl_dari_'+id).value
    var tglSamp=document.getElementById('tgl_smp_'+id).value;

    param = "proses=detail_edit";
    param += "&kode="+detKode.value;
    param += "&kdbrg="+rkd_brg.value;
    rprice.value=remove_comma(rprice);
    rprice=rprice.value;
    param += "&price="+rprice;
    param += "&rspek="+rspek.value;
     jumlah.value=remove_comma(jumlah);
    jumlah=jumlah.value;
        param += "&jmlh="+jumlah;
        param += "&krs="+kurs.value;
        param +="&oldKdbrg="+oldKdbrg;
        param +="&jmlhKurs="+jmhKurs;
        param += "&tglDari="+tglDari;
        param += "&tglSamp="+tglSamp;
        //alert(param);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        document.getElementById('oldKdbrg_'+id).value=rkd_brg.value;
                    alert('Successfull edited');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('log_slave_permintaan_detail.php', param, respon);
}

/* Function deleteDelete(id)
 * Fungsi untuk menghapus data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menghapus data pada tabel Detail
 * O : Menghapus baris pada tabel Detail
 */
function deleteDetail(id) {
    var detKode = document.getElementById('detail_kode');
    var rkd_brg = document.getElementById('kd_brg_'+id);
    var rprice = document.getElementById('price_'+id);
    var rspek = document.getElementById('spek_'+id);

    param = "proses=detail_delete";
    param += "&kode="+detKode.value;
    param += "&kdbrg="+rkd_brg.value;
    param += "&price="+rprice.value;
    param += "&rspek="+rspek.value;

    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    row = document.getElementById("detail_tr_"+id);
                    if(row) {
                        //row.style.display="none";
                        row = document.getElementById("detail_tr_"+id);
                        if(row) 
                        {
                                //
                                document.getElementById('price_'+id).value=0;
                                document.getElementById('total_'+id).value=0;	
                                document.getElementById('dtKdbrg_'+id).innerHTML="";
                                //document.getElementById('dtKdbrg_'+id).innerHTML="";
                               // document.getElementById('jmlhDiminta_'+id).value="";
                                row.style.display="none";
                                //pengurang+=1;
                                plusAll();
                        } 
                        else 
                        {
                                alert("Row undetected");
                        }
                    } else {
                        alert("Row undetected");
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        a=confirm('Delete item, are you sure?');
        if(a)
        {
            post_response_text('log_slave_permintaan_detail.php', param, respon);
        }
        else
        {
                return;
        }
}
 /* Function addNewRow
 * Fungsi untuk menambah row baru ke dalam table
 * I : id dari tbody tabel
 * P : Persiapan row dalam bentuk HTML
 * O : Tambahan row pada akhir tabel (append)
 */
function addNewRow(body,onDetail) {
        //alert(body);
    var tabBody = document.getElementById(body);
    if(onDetail) {
        var detail = onDetail;

    } else {
        //alert('test 1');
        var detail = false;
    }

    // Search Available numRow
    var numRow = 0;
    if(!detail) {
        while(document.getElementById('tr_'+numRow)) {
            numRow++;
        }
    } else {
        //	alert('test 2');
        while(document.getElementById('detail_tr_'+numRow)) {
            numRow++;
        }
    }

    // Add New Row
    var newRow = document.createElement("tr");
    tabBody.appendChild(newRow);
    if(!detail) {
        newRow.setAttribute("id","tr_"+numRow);
    } else {
        //alert('test 4');
        newRow.setAttribute("id","detail_tr_"+numRow);
    }
    newRow.setAttribute("class","rowcontent");

    if(!detail) {
        newRow.innerHTML += "<td><input id='kode_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='matauang_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='simbol_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='kodeiso_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><img id='add_"+numRow+
        "' title='Tambah' class=zImgBtn onclick=\"addMain('"+numRow+"')\" src='images/save.png'/>"+
        "&nbsp;<img id='delete_"+numRow+"' />"+
        "&nbsp;<img id='pass_"+numRow+"' />"+
        "</td>";
    } 
        else
        {
        //	alert('test 5');
        // Create Row
        newRow.innerHTML += "<td><input id='kd_brg_"+numRow+"' type='text' class='myinputtext' style='width:120px' disabled='disabled' value='' /></td><td>"+
            "<input id='nm_brg_"+numRow+"' type='text' class='myinputtext' style='width:120px' disabled='disabled' value='' /></td><td><input id='sat_"+numRow+
        "' type='text' class='myinputtext' style='width:70px'disabled='disabled' value='' /><img src=images/search.png class=dellicon title='"+jdl_ats_0+"' onclick=\"searchBrg('"+jdl_ats_1+"','"+content_0+"<input id=nomor type=hidden value="+numRow+" />',event)\";> <input type=hidden id=oldKdbrg_"+numRow+" name=oldKdbrg_"+numRow+"  /></td>"+"<td><input id='spek_"+numRow+"' type='text' class='myinputtext' style='width:230px' onkeypress='return tanpa_kutip(event)' value='' maxlength=100 /></td>"+
        "<td><input id='jumlah_"+numRow+"' type='text' class='myinputtextnumber' style='width:70px' onkeypress='return angka_doang(event)' value='' onfocus=\"normal_number('"+numRow+"')\"  onblur=\"display_number('"+numRow+"')\" /></td>"+
                "<td><input id='price_"+numRow+"' type='text' class='myinputtextnumber' style='width:70px' onkeypress='return angka_doang(event)' onfocus=\"normal_number('"+numRow+"')\"  onblur=\"display_number('"+numRow+"')\" value='' /></td>"+
                "<td><select id='kurs_"+numRow+"' name='kurs_"+numRow+"'  style='width:70px' onchange='getKurs("+numRow+")'>"+Option_Isi+"</select><input type=hidden id=jmlhKurs_"+numRow+" name=jmlhKurs_"+numRow+" /></td>"+
                "<td><input type='text' style='width:70px' id='tgl_dari_"+numRow+"' class='myinputtext' name='tgl_dari_"+numRow+"' maxlength=\"10\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\" ></td>"+
                "<td><input type='text' style='width:70px' id='tgl_smp_"+numRow+"' class='myinputtext' name='tgl_smp_"+numRow+"' maxlength=\"10\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\" ></td>"+
                "<td><img id='detail_add_"+numRow+"' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+
        "&nbsp;<img id='detail_delete_"+numRow+"' />"+
        "&nbsp;<img id='detail_pass_"+numRow+"' />"+
        "</td>";
        }
}
/* Function switchEditAdd
 * Fungsi untuk mengganti image add menjadi edit dan keroconya
 * I : id nomor row
 * P : Image Add menjadi Edit
 * O : Image Edit
 */
function switchEditAdd(id,main) {

 if(main=='main') {
        var idField = document.getElementById('add_'+id);
        var delImg = document.getElementById('delete_'+id);
        var passImg = document.getElementById('pass_'+id);
        var kode = document.getElementById('kode_'+id);
    } else {
        //alert(id);
        var idField = document.getElementById('detail_add_'+id);
        var delImg = document.getElementById('detail_delete_'+id);
    }
    if(idField) {
        idField.removeAttribute('id');
        idField.removeAttribute('name');
        idField.removeAttribute('onclick');
        idField.removeAttribute('src');
        idField.removeAttribute('title');

        // Set Edit Image Attr
        idField.setAttribute('title','Edit');
        if(main=='main') {
            idField.setAttribute('id','edit_'+id);
            idField.setAttribute('name','edit_'+id);
            idField.setAttribute('onclick','editMain(\''+id+'\',\'kode\',\''+kode.value+'\')');
        } else {
                        //alert(id);
                idField.setAttribute('id','detail_edit_'+id);
                        idField.setAttribute('name','detail_edit_'+id);
            idField.setAttribute('onclick','editDetail(\''+id+'\')');
        }
        idField.setAttribute('src','images/save.png');

        // Set Delete Image Attr
                delImg.setAttribute('class','zImgBtn');
        delImg.setAttribute('title','Hapus');
        if(main=='main') {
                        delImg.setAttribute('name','delete_'+id);
            delImg.setAttribute('onclick','deleteMain(\''+id+'\',\'kode\',\''+kode.value+'\')');
        } else {
                        //alert(id);
                        delImg.setAttribute('name','detail_delete_'+id);
            delImg.setAttribute('onclick','deleteDetail(\''+id+'\')');
        }
        delImg.setAttribute('src','images/delete_32.png');

    } else {
        alert('DOM Definition Error');
    }
}
stat_inputb=0;
function reset_data()
{
        op = document.getElementById('method');
        if(stat_inputb==0)
        {
                clear_all_data();
        }
        else if(stat_inputb==1)
        {
                nomor = document.getElementById('detail_kode');
                //nomor = nomor.value;
                param='no_permintaan='+nomor;
                param+='&method=delete';
                tujuan='log_slave_save_permintaan_harga.php';
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
                                                                //document.getElementById('contain').innerHTML=con.responseText;

                                                                //alert('Delete Data Succeed');
                                                                clear_all_data();
                                                                displayList();
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 }
                 post_response_text(tujuan, param, respog);
        }
}
function cariPnwrn()
{
        txtSearch=trim(document.getElementById('txtsearch').value);
        tglCari=trim(document.getElementById('tgl_cari').value);
        param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method=cari_permintaan';

        tujuan='log_slave_save_permintaan_harga.php';
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
                                                                document.getElementById('contain').innerHTML=con.responseText;


                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 }
                 post_response_text(tujuan, param, respog);
}

function cariBast(num)
{
                txtSearch=trim(document.getElementById('txtsearch').value);
                tglCari=trim(document.getElementById('tgl_cari').value);
                if(txtSearch!=''||tglCari!=''){
                    param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method=cari_permintaan';
                }else{
                 param='method=cari_pp';
                }
                param+='&page='+num;
                tujuan = 'log_slave_save_permintaan_harga.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function get_nopp()
{
        //alert('masuk');
        id=document.getElementById('nomor').value;
        kd_brg=document.getElementById('kd_brg_'+id).value;
        param='method=get_nopp'+'&kdbrg='+kd_brg;
        tujuan='log_slave_save_permintaan_harga.php';
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
                                                                //alert('nopp_'+id);
                                                                //alert(con.responseText);
                                                                //document.getElementById('nopp_'+id).createElement('option')=con.responseText;
                                                                document.getElementById('nopp_'+id).innerHTML=con.responseText;
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 }


}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
    width='300';
    height='100';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}

function datakeExcel(ev,nmr)
{
        param='method=printExcel'+'&no_permintaan='+nmr;
        //alert(param);
        tujuan='log_slave_save_permintaan_harga.php';
        judul='RFQ convert spreadsheet';		
        printFile(param,tujuan,judul,ev)	
}
function printFile2(title,ev)
{
    width='550';
    height='300';
   content="<div id=keteranganForm></div>";
   showDialog1(title,content,width,height,ev); 	
}
function addKet(notrans,nourut,jdl,ev){
    printFile2(jdl,ev);
    param='notransaksi='+notrans+'&nourut='+nourut;
    param+='&method=getKetNopp';
    tujuan = 'log_slave_save_permintaan_harga.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                              
                                    document.getElementById('keteranganForm').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function saveKetData(notrans,nourut,noke){
    ev="event";
    kdbrg=document.getElementById('kdBrg_'+noke).innerHTML;
    ketBrg=document.getElementById('ketId_'+noke).value;
    param='notransaksi='+notrans+'&nourut='+nourut;
    param+='&method=updateKet'+'&ket='+ketBrg+'&kdBrng='+kdbrg;
    tujuan = 'log_slave_save_permintaan_harga.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    addKet(notrans,nourut,jdl,ev);
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}



function zPreview(fileTarget,passParam,idCont) {
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
    param+='&proses=preview2';
    //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById(idCont);
                    res.innerHTML = con.responseText;
                    document.getElementById('formEditData').style.display='block';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);

}
function zPreview2(fileTarget,notrans,idCont) {
    if(notrans==''){
        notrans=document.getElementById('notransaksi').value;
    }
    param='method=preview2'+'&notransaksi='+notrans;   
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('formPP').style.display='none';
                    document.getElementById('listBrgPP').style.display='none';
                    document.getElementById('list_permintaan').style.display='none';
                    document.getElementById('listSupplier').style.display='none';
                    document.getElementById('supplierForm').style.display='none';
                    document.getElementById('formEditData2').style.display='block';
                    var res = document.getElementById(idCont);
                    res.innerHTML = con.responseText;

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
   post_response_text(fileTarget+'.php', param, respon);
}
function zExcel(ev,tujuan,passParam)
{
        judul='Spreadsheet';
        //alert(param);	
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
        nourut=document.getElementById('noUrut').value; 
        param+='&proses=excel'+'&noUrut='+nourut;
        //alert(param);
        printFile(param,tujuan,judul,ev)	
}
function addSupplierPlus(notr,nour){ 
        document.getElementById('formPP').style.display='block';
        document.getElementById('listBrgPP').style.display='none';
     param='method=listBarangDetail';
     param+='&notransaksi='+notr+'&nourut='+nour;
     tujuan='log_slave_save_permintaan_harga.php';
     post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {

                            document.getElementById('dataBarang').innerHTML=con.responseText;
                            document.getElementById('formPP').style.display='block';
                            document.getElementById('listBrgPP').style.display='none';
                            document.getElementById('list_permintaan').style.display='none';
                            document.getElementById('listSupplier').style.display='none';
                            document.getElementById('supplierForm').style.display='block';
                            document.getElementById('notransaksi').value=notr;
                            nour=parseInt(nour)+1;
                            document.getElementById('noUrut').value=nour;   
                            loadSupplier();
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}

function display_number(id)
{
        price=document.getElementById('price_'+id);
        change_number(price);
                jmlh=document.getElementById('jumlah_'+id);
        change_number(jmlh);     
}
function normal_number(id, row, totRow)
{
    //satu = document.getElementById('harga_satuan_' + id);
    satu = document.getElementById('price_' + id + '_' + row);
        satu.value=remove_comma(satu);
}

function calculate(id,row,totRow)
{
    jmlh_brg=document.getElementById('jumlah_'+id).innerHTML;
    harga=document.getElementById('price_'+id+'_'+row).value;

        if(jmlh_brg==''||harga=='')
        {
                a=document.getElementById('total_'+id+'_'+row);
                a.value='';
                a=parseFloat(a.value);
        }
        else
        {
                        harg=document.getElementById('price_'+id+'_'+row);
                        harg.value=remove_comma_var(harg.value);
                        jmlh_sub=jmlh_brg*harg.value;

                        if(jmlh_sub==0)
                        {
                                document.getElementById('total_'+id+'_'+row).value=0;
                        }
                        else
                        {
                                        as=document.getElementById('total_'+id+'_'+row);
                                        as.value=jmlh_sub
                                        change_number(as);
                        }

        }

        grnd_total(row,totRow);
                                //grandTotal();

}

function grnd_total(brs,totRow){ 
   row=totRow+1;
   total=0;
   for(i=1;i<row;i++)
       {
            b=document.getElementById('total_'+i+'_'+brs);
            b.value=remove_comma_var(b.value);
            total+=parseFloat(b.value);
            change_number(b);
           // alert(b+"------"+total);
            //alert(b.value);
            //change_number(b);
            if(isNaN(total))
               {
                   total=0;
               }
       }
           document.getElementById('total_harga_po_'+brs).innerHTML=total;
           tot=document.getElementById('total_harga_po_'+brs);
           tot.innerHTML=total;
           //change_number(tot);
           calculate_diskon(brs);
           grandTotal(brs);
}

function getZero(brs)
{
        dis=document.getElementById('diskon_'+brs);
        if(dis.value=="")
        {
                dis.value=0;
        }
        nPpn=document.getElementById('ppN_'+brs);
        if(nPpn.value=="")
        {
                nPpn.value=0;
        }
        angdis=document.getElementById('angDiskon_'+brs);
        //angdis.value=remove_comma(angdis);
        if(angdis.value=="")
        {
                angdis.value=0;
        }
}
function periksa_isi(obj)
{
        if(trim(obj.value)=='')	
        {
                alert('Please complete the form');
                obj.focus();
                return;
        }
}
function cek_isi(obj)
{
        if(trim(obj.value)!='')	
        {
                change_number(obj.value);
        }
        else
        {
                change_number(obj.value);
        }
}
function calculate_diskon(brs)
{
        sb_tot=document.getElementById('total_harga_po_'+brs);
        sb_tot.innerHTML=remove_comma_var(sb_tot.innerHTML);
        nil_dis=document.getElementById('diskon_'+brs).value;
        angk=document.getElementById('angDiskon_'+brs).value;
        if((nil_dis==0)||(angk==0))
        {
                document.getElementById('angDiskon_'+brs).disabled=false;
                document.getElementById('diskon_'+brs).disabled=false;
        }
        if((nil_dis!=0)||(angk!=0))
        {
                document.getElementById('angDiskon_'+brs).disabled=true;
                if(nil_dis>100)
                {	
                        alert('Discount must lower than 100%');
                        document.getElementById('diskon_'+brs).value='';
                        document.getElementById('angDiskon_'+brs).disabled=false;
                }
                else
                {
                        disc=(nil_dis*(parseFloat(sb_tot.innerHTML)))/100;
                }
                 //  	grnd_tot=(sb_tot.value-disc)+pn;
                        //document.getElementById('angDiskon').value=disc;
                        nilaiDis=document.getElementById('angDiskon_'+brs);
                        nilaiDis.value=disc;
                        //change_number(nilaiDis);
                        calculatePpn(brs);
                        grandTotal(brs);
        }

}
function calculate_angDiskon(brs)
{
        nilDis=document.getElementById('angDiskon_'+brs);
        //nilDis.value=remove_comma(nilDis);
        if(nilDis.value!=0)
        {
                document.getElementById('diskon_'+brs).disabled=true;
                subTot=document.getElementById('total_harga_po_'+brs);
                //subTot.innerHTML=remove_comma(subTot);
                if(nilDis.value!=subTot.innerHTML)
                {
                        persenDis=parseFloat(nilDis.value/subTot.innerHTML)*100;
                }
                if(persenDis<100)
                {
                        persen=Math.ceil(persenDis);
                        document.getElementById('diskon_'+brs).value=persen;
                        //sbTot=document.getElementById('total_harga_po').value
                }
                else 
                {
                        alert("Discount too large");
                        document.getElementById('angDiskon_'+brs).value='';
                        document.getElementById('diskon_'+brs).value='';
                        document.getElementById('diskon_'+brs).disabled=false;
                }

                //nilDiskon=document.getElementById('angDiskon').value;
        calculatePpn(brs);
        grandTotal(brs);
        }
        else if(nilDis.value==0)
        {
                document.getElementById('diskon_'+brs).disabled=false;
        }
}
function calculatePpn(brs)
{
        var reg = /^[0-9]{1,2}$/;
        nilP=document.getElementById('ppN_'+brs).value;
        dis=document.getElementById('angDiskon_'+brs);
        subTot=document.getElementById('total_harga_po_'+brs);
        //alert(reg);
        if(reg.test(nilP))
        {
                if(nilP==10)
                {
                        //dis.value=remove_comma(dis);
                        //subTot.innerHTML=remove_comma(subTot);
                        pn=(parseFloat((subTot.innerHTML-dis.value))*10)/100;	
                        if(isNaN(pn))
                        {
                            pn=0;
                        }
                        document.getElementById('ppn_'+brs).value=pn;
                }

                else if(nilP==0)
                {
                        //dis.value=remove_comma(dis);
                        //subTot.innerHTML=remove_comma(subTot);
                        pn=(parseFloat((subTot.innerHTML-dis.value))*nilP)/100;	
                        document.getElementById('ppn_'+brs).value=pn;
                }	
                else if(nilP==2)
                {
                        //dis.value=remove_comma(dis);
                        //subTot.value=remove_comma(subTot);
                        pn=(parseFloat((subTot.innerHTML-dis.value))*nilP)/100;	
                        if(isNaN(pn))
                        {
                            pn=0;
                        }
                        document.getElementById('ppn_'+brs).value=pn;
                }	
        }
        else
        {
                alert("Valid 0 to 10 only");
                document.getElementById('ppn_'+brs).value='0';
                document.getElementById('ppN_'+brs).value='0';
                return;
        }

                grandTotal(brs);
}
nilPpn=0;
function grandTotal(brs)
{
        sb_tot=document.getElementById('total_harga_po_'+brs);
        nilDiskon=document.getElementById('angDiskon_'+brs);
        ppn=document.getElementById('ppN_'+brs);

        if(ppn.value!=0||ppn.value!='')
        {
            nilPpn=(parseFloat((sb_tot.innerHTML-nilDiskon.value))*ppn.value)/100;	
            document.getElementById('ppn_'+brs).value=nilPpn;   
        }
        else
        {
            document.getElementById('ppN_'+brs).value=0;
            document.getElementById('ppn_'+brs).value=0;
            nilPpn=0;
        }

        grnd_tot=parseFloat((sb_tot.innerHTML-nilDiskon.value))+parseFloat(nilPpn);
        total=document.getElementById('grand_total_'+brs);
        total.innerHTML=grnd_tot;

}
function simpanSemua2(brs,totRow)
{
    no_prmntan=document.getElementById('no_prmntan_'+brs).value;
    nilDiskon=document.getElementById('angDiskon_'+brs).value;
    diskonPersen=document.getElementById('diskon_'+brs).value;
    supplierId=document.getElementById('supplierId_'+brs).value;
    nilPPn=document.getElementById('ppn_'+brs).value;
    nilaiPermintaan=document.getElementById('grand_total_'+brs).innerHTML;
    subTotal=document.getElementById('total_harga_po_'+brs).innerHTML;
    termPay=document.getElementById('term_pay_'+brs).options[document.getElementById('term_pay_'+brs).selectedIndex].value;
    idFranco=document.getElementById('tmpt_krm_'+brs).options[document.getElementById('tmpt_krm_'+brs).selectedIndex].value;
    stockId=document.getElementById('stockId_'+brs).options[document.getElementById('stockId_'+brs).selectedIndex].value;
    ketUraian=document.getElementById('ketUraian_'+brs).value;
    mtng=document.getElementById('mtUang_'+brs).options[document.getElementById('mtUang_'+brs).selectedIndex].value;
    krs=document.getElementById('Kurs_'+brs).value;
    tgldari=document.getElementById('tgl_dari_'+brs).value;
    tglsmp=document.getElementById('tgl_smp_'+brs).value;
    if((subTotal=='0')||(subTotal==''))
        {
            subTotal=nilDiskon=diskonPersen=nilPPn=0;
        }

    var row = totRow+1;
    strUrl = '';
    for(i=1;i<row;i++)
    {
                    try{
                            if(strUrl != '')
                            {
                                    strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).innerHTML))
                                    +'&merk[]='+document.getElementById('merk_'+i+'_'+brs).value
                                    +'&price[]='+document.getElementById('price_'+i+'_'+brs).value
                                    +'&jmlh[]='+document.getElementById('jumlah_'+i).innerHTML;
                            }
                            else
                            {
                                    strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).innerHTML))
                                    +'&merk[]='+document.getElementById('merk_'+i+'_'+brs).value
                                    +'&price[]='+document.getElementById('price_'+i+'_'+brs).value
                                    +'&jmlh[]='+document.getElementById('jumlah_'+i).innerHTML;	
                            }
                    }
                    catch(e){}
    }
    param='ckno_permintaan='+no_prmntan+'&nourut='+brs+'&method=updateTransaksi';
    param+='&nilDiskon='+nilDiskon+'&diskonPersen='+diskonPersen+'&nilPPn='+nilPPn+'&nilaiPermintaan='+nilaiPermintaan;
    param+='&subTotal='+subTotal+'&termPay='+termPay+'&idFranco='+idFranco+'&stockId='+stockId+'&ketUraian='+ketUraian;
    param+='&tglDari='+tgldari+'&tglSmp='+tglsmp+'&mtUang='+mtng+'&kurs='+krs+'&supplierId='+supplierId;
    param+=strUrl;
    tujuan='log_slave_save_permintaan_harga.php';

//  return;
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
                                                    alert("Done");
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }
     }

}

function validat(ev)
{
  key=getKey(ev);
  if(key==13){
    cariPnwrn();
  } else {
  return tanpa_kutip_dan_sepasi(ev);	
  }	
}