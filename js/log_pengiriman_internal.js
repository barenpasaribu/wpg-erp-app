function searchSupplier(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function searchInternal(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}

function findSupplier()
{
    nmSupplier=document.getElementById('nmSupplier').value;
    param='proses=getSupplierNm'+'&nmSupplier='+nmSupplier;
    tujuan='log_slave_save_po.php';
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
function findKaryawan()
{
    nmSupplier=document.getElementById('nmKaryawan').value;
    param='method=getKaryNm'+'&nmKaryawan='+nmSupplier;
    tujuan='log_slave_pengiriman_internal.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerKaryawan').innerHTML=con.responseText;
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
function setDatakary(kdSupp)
{
    l=document.getElementById('id_internal');

    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==kdSupp)
                {
                    l.options[a].selected=true;
                }
        }

       closeDialog();
}

function newData(kdpt)
{
    kodePt=kdpt;
    param='method=getDataPt'+'&kdPt='+kodePt;
    tujuan='log_slave_pengiriman_internal';
    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('statusInputan').value=0;
                    document.getElementById('vwListPenerimaan').style.display='block';
                    document.getElementById('dataListMnc').style.display='none';
                    document.getElementById('formInputanDt').style.display='none';
                    var res = document.getElementById('listPenerimaan');
                    res.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}

function process()
{
    var tbl = document.getElementById("dataBarangBapb");
    var row = tbl.rows.length;
    row=row-1;
        //alert(row);
        strUrl = '';
    for(i=1;i<=row;i++)
        {
          ar=document.getElementById('dtBpab_'+i);
           if(ar.checked==true)
                   {
            //alert(i);           
                                try{
                                        if(strUrl != '')
                                        {
                                                strUrl += '&notransc[]='+trim(document.getElementById('notrans_c'+i).innerHTML)
                                                       +'&kdbrgc[]='+trim(document.getElementById('kdBarang_c'+i).innerHTML)
                                                       +'&jmlhc[]='+trim(document.getElementById('jmlhBarang_c'+i).innerHTML)
                                                       +'&nopoc[]='+trim(document.getElementById('nopo_c'+i).innerHTML);
                                        }
                                        else
                                        {
                                                strUrl += '&notransc[]='+trim(document.getElementById('notrans_c'+i).innerHTML)
                                                       +'&kdbrgc[]='+trim(document.getElementById('kdBarang_c'+i).innerHTML)
                                                       +'&jmlhc[]='+trim(document.getElementById('jmlhBarang_c'+i).innerHTML)
                                                       +'&nopoc[]='+trim(document.getElementById('nopo_c'+i).innerHTML);
                                        }
                                }
                                catch(e){}

                        }
        }

                //return;
                if(strUrl=='')
                {
                        alert('Choose one');
                        return;
                }
                else
                {
                    statInputan=document.getElementById('statusInputan').value;
                    kdPte=document.getElementById('kdPte').innerHTML;
                    param="method=createTable"+"&kdPt="+kdPte+'&statInputan='+statInputan;
                    param+=strUrl;
                    //alert(param);
                    tujuan='log_slave_pengiriman_internal.php';
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
                                    document.getElementById('dataListMnc').style.display='none';
                                    document.getElementById('vwListPenerimaan').style.display='none';
                                    document.getElementById('formInputanDt').style.display='block';
                                    //document.getElementById('no_po').value=a[0];
                                    document.getElementById('formInputan').innerHTML=con.responseText;

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
function centangSma(mulai,jmlhbaris)
{

    for(mulai=0;mulai<jmlhbaris;mulai++)
        {

            document.getElementById('dtBpab_'+mulai).checked=true;
        }
}
function saveFranco()
{
    statInputan=document.getElementById('statusInputan').value;
    id_supplier=document.getElementById('id_supplier').options[document.getElementById('id_supplier').selectedIndex].value;
    id_supplier2=document.getElementById('id_internal').options[document.getElementById('id_internal').selectedIndex].value;
    idFranco=document.getElementById('franco_id').options[document.getElementById('franco_id').selectedIndex].value;
    moda=document.getElementById('moda_trans').options[document.getElementById('moda_trans').selectedIndex].value;
    tglKrm=document.getElementById('tglKrm').value;
    jlhKoli=document.getElementById('jlhKoli').value;
    //kpd=document.getElementById('kpd').options[document.getElementById('kpd').selectedIndex].value;
    ket=document.getElementById('ket').value;//
    lokPenerimaan=document.getElementById('lokPenerimaan').value;
    srtJalan=document.getElementById('srtJalan').value;
    biaya=document.getElementById('biaya').value;
    biayapck=document.getElementById('biayapckng').value;
    biayakg=document.getElementById('biayaPerkg').value;
    berat=document.getElementById('beratKg').value;
    
    if(id_supplier==''){
        id_supplier=id_supplier2;
    }
    
    ket=document.getElementById('ket').value;
    var tbl = document.getElementById("detailDtBarang");
    var row = tbl.rows.length;

    row=row-1;
    strUrl5 = '';
    for(i=0;i<row;i++)
    {

        try{

                if(strUrl5 != '')
                {					
                        strUrl5 +='&notrans[]='+trim(document.getElementById('notrans_'+i).innerHTML)
                        +'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kdbarang_'+i).innerHTML))
                        +'&satbrg[]='+encodeURIComponent(trim(document.getElementById('satuanbrg_'+i).innerHTML))
                        +'&jmlhbrg[]='+encodeURIComponent(trim(document.getElementById('jmlhBarang_'+i).value))
                        +'&nopodata[]='+encodeURIComponent(trim(document.getElementById('nopodata_'+i).innerHTML));

                        }
                        else
                        {
                        strUrl5 +='&notrans[]='+trim(document.getElementById('notrans_'+i).innerHTML)
                        +'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kdbarang_'+i).innerHTML))
                        +'&satbrg[]='+encodeURIComponent(trim(document.getElementById('satuanbrg_'+i).innerHTML))
                        +'&jmlhbrg[]='+encodeURIComponent(trim(document.getElementById('jmlhBarang_'+i).value))
                        +'&nopodata[]='+encodeURIComponent(trim(document.getElementById('nopodata_'+i).innerHTML));

                        }

                }
        catch(e){}

    }
    param='id_supplier='+id_supplier+'&tglKrm='+tglKrm+'&jlhKoli='+jlhKoli;
    param+='&lokPenerimaan='+lokPenerimaan+'&srtJalan='+srtJalan;
    param+='&biaya='+biaya+'&method=insert'+'&ket='+ket+'&biayapckng='+biayapck;
    param+='&statInputan='+statInputan+'&method=insert'+'&moda_trans='+moda;
    param+='&idFranco='+idFranco+'&biayaPerkg='+biayakg+'&beratKg='+berat;
    param+=strUrl5;
   // alert(param);
    tujuan='log_slave_pengiriman_internal.php';
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
                               normalView();
                    }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
          }	
     } 


}
function normalView()
{
    document.getElementById('dataListMnc').style.display='block';
    document.getElementById('vwListPenerimaan').style.display='none';
    document.getElementById('formInputanDt').style.display='none';
    document.getElementById('txtsearch').value='';
    document.getElementById('tgl_cari').value='';
    loadData();         
}
function loadData()
{
    txtSrc=document.getElementById('txtsearch').value;
    tglSrc=document.getElementById('tgl_cari').value;
        param='method=loadData';
        if(txtSrc!='')
            {
                param+='&txtSrc='+txtSrc;
            }
            if(tglSrc!='')
            {
            param+='&tglSrc='+tglSrc;
            }
        tujuan='log_slave_pengiriman_internal';
        post_response_text(tujuan+'.php', param, respon);
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('dataListMnc').style.display='block';
                    document.getElementById('vwListPenerimaan').style.display='none';
                    document.getElementById('formInputanDt').style.display='none';
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function cariBast(num)
{
            txtSrc=document.getElementById('txtsearch').value;
            tglSrc=document.getElementById('tgl_cari').value;
            param='method=loadData';
            if(txtSrc!='')
            {
            param+='&txtSrc='+txtSrc;
            }
            if(tglSrc!='')
            {
            param+='&tglSrc='+tglSrc;
            }
                param+='&page='+num;
                tujuan = 'log_slave_pengiriman_internal.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('dataListMnc').style.display='block';
                                                document.getElementById('vwListPenerimaan').style.display='none';
                                                document.getElementById('formInputanDt').style.display='none';
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
function getLok()
{
        karyId=document.getElementById('kpd').options[document.getElementById('kpd').selectedIndex].value;
        param='method=getLokasi'+'&kpd='+karyId;
        tujuan='log_slave_pengiriman_internal';
        post_response_text(tujuan+'.php', param, respon);
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                      document.getElementById('lokPenerimaan').value=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function checkData(dt)
{
    total=document.getElementById('jmlh_total_'+dt).innerHTML;
    brgdkrm=document.getElementById('jmlhBarang_'+dt).value;
    brgdkrm=parseInt(brgdkrm);
    total=parseInt(total);
    if(brgdkrm>total)
    {
        alert("Error: Volume of deliver should less than current balance available:"+brgdkrm+"__"+total);
        document.getElementById('jmlhBarang_'+dt).value=total;
        return;
    }
}

function fillField(notran)
{

        param='method=createTable'+'&notrans='+notran+'&statInputan=1';
        tujuan='log_slave_pengiriman_internal';
        post_response_text(tujuan+'.php', param, respon);
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('dataListMnc').style.display='none';
                    document.getElementById('vwListPenerimaan').style.display='none';
                    document.getElementById('formInputanDt').style.display='block';
                    document.getElementById('formInputan').innerHTML=con.responseText;
                    document.getElementById('statusInputan').value=1;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}function deleteDetail(id) {
        var tbl = document.getElementById("detailDtBarang");
                var baris = tbl.rows.length;
                baris=baris-1;
        //	alert(baris);
                //return;
                if(baris==1)
                {
                        notran=document.getElementById('srtJalan').value;
                        delData(notran);
                }
        else if(baris>1)
                {
                        //alert(baris);

                        //alert(tabel.rows[id]);

                        //tabel.removeChild(tabel.rows[id]);
                        //elem.parentNode.removeChild(elem);
                        var srtJln=document.getElementById('srtJalan').value;
                        var notrans = document.getElementById('notrans_'+id);
                        var nopodata = document.getElementById('nopodata_'+id);
                        var kdbrgc= document.getElementById('kdbarang_'+id);

                        param = "method=deleteDetail";
                        param += "&notrans="+notrans.value;
                        param += "&srtJalan="+srtJln.value;
                        param += "&kdbrgc="+kdbrgc.value;


                        function respon(){
                                if (con.readyState == 4) {
                                        if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                                } else {
                                                        // Success Response
                                        //alert(id);
                                        //baris=row;
                                        //tabel=document.getElementById("detailBody");
                                        //tabel.removeChild(tabel.rows[id]);

                                        row = document.getElementById("detail_tr_"+id);
                                        if(row) 
                                        {
                                            row.style.display="none";
                                        } 
                                        else 
                                        {
                                                alert("Row undetected");
                                        }

                                        }
                                        } else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                                }
                        }

                                if(confirm('Are you sure delete this Data?'))
                                {
                                        post_response_text('log_slave_pengiriman_internal.php', param, respon);	
                                }
                                else
                                {
                                        return;
                                }
                }
}

function delData(notran)
{
        param='method=delData'+'&notrans='+notran;
        tujuan='log_slave_pengiriman_internal';
        if(confirm("Anda yakin ingin menghapus"))
        {
            post_response_text(tujuan+'.php', param, respon);
        }
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                        normalView();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function previewBapb(notransaksi,ev)
{
        param='srtJalan='+notransaksi;
        tujuan = 'log_slave_print_surat_jalan_pdf.php?'+param;	
 //display window
   title=notransaksi;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}
function suratJalan(notransaksi,ev)
{
        param='srtJalan='+notransaksi;
        tujuan = 'log_slave_print_surat_jalan_pdf.php?'+param;	

 //display window
   title=notransaksi;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}
function suratJalan2(notransaksi,ev)
{
        param='srtJalan='+notransaksi;
        tujuan = 'log_slave_print_surat_jalan_pdf2.php?'+param;	

 //display window
   title=notransaksi;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}
function cancelIsi()
{
    document.getElementById('dataListMnc').style.display='block';
    document.getElementById('vwListPenerimaan').style.display='none';
    document.getElementById('formInputanDt').style.display='none';
    document.getElementById('formInputan').innerHTML='';
    document.getElementById('statusInputan').value=0;
    loadData();
}
function kaliaja(){
    brt=document.getElementById('beratKg').value;
    bya=document.getElementById('biayaPerkg').value;
    byapcking=document.getElementById('biayapckng').value;
    kalidt=(parseFloat(brt)*parseFloat(bya))+parseFloat(byapcking);
    if(isNaN(kalidt)){
        kalidt=0;
    }
    document.getElementById('biaya').value=kalidt;
}
function ubahdata(){
    drt=document.getElementById('idPilihanExin').options[document.getElementById('idPilihanExin').selectedIndex].value;
    if(drt==2){
      l=document.getElementById('id_supplier');
      for(a=0;a<l.length;a++)
        {
            if(l.options[a].value=='')
                {
                    l.options[a].selected=true;
                }
        }
        document.getElementById('id_internal').disabled=false;
        l.disabled=true;
    }else{
      document.getElementById('id_supplier').disabled=false;
      l=document.getElementById('id_internal');
      for(a=0;a<l.length;a++)
        {
            if(l.options[a].value=='')
                {
                    l.options[a].selected=true;
                }
        }
    }
    l.disabled=true;
}