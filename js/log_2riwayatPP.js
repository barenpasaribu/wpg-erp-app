// JavaScript Document
function displayList()
{
document.getElementById('txtNopp').value='';
document.getElementById('tgl_cari').value='';
document.getElementById('periode').value='';
document.getElementById('lokBeli').value='';
document.getElementById('statPP').value='';
document.getElementById('txtNmBrg').value='';
document.getElementById('supplier_id').value='';
loadData();
}

function searchPO(title,content,ev)
{
    width='500';
    height='400';
    showDialog1(title,content,width,height,ev);
    //alert('asdasd');
}
function setDataPO(nopo)
{
    // l=document.getElementById('cariNopo').options[document.getElementById('cariNopo').selectedIndex].value;

    l=document.getElementById('txtNopp');
    console.log(nopo);
    for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==nopo)
        {
            l.options[a].selected=true;
            break;
        }
    }

    closeDialog();
}
function findPO()
{
    nmPO=document.getElementById('nmPO').value;
    param='proses=getPO'+'&nmPO='+nmPO;
    tujuan='log_slave_2riwayat.php';

    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('containerPO').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function loadData()
{
        param='proses=getData';
        tujuan='log_slave_2riwayat.php';
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
function cariBast(num)
{
                param='proses=getData';
                param+='&page='+num;
                tujuan = 'log_slave_2riwayat.php';
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
function savePil()
{
        nopp=document.getElementById('txtNopp').value;
        tglSdt=document.getElementById('tgl_cari').value;
        txNmbrg=document.getElementById('txtNmBrg').value;
        statusPP=document.getElementById('statPP').options[document.getElementById('statPP').selectedIndex].value;
        period=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
        lokasi=document.getElementById('lokBeli').options[document.getElementById('lokBeli').selectedIndex].value;
        suppl=document.getElementById('supplier_id').options[document.getElementById('supplier_id').selectedIndex].value;
        purchsrId=document.getElementById('purchaserId').options[document.getElementById('purchaserId').selectedIndex].value;
        param='nopp='+nopp+'&proses=cariData'+'&tglSdt='+tglSdt+'&statusPP='+statusPP+'&periode='+period+'&lokBeli='+lokasi;
        param+='&supplier_id='+suppl+'&txNmbrg='+txNmbrg+'&purchsrId='+purchsrId;
        tujuan='log_slave_2riwayat.php';
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
function gantiPil()
{
        document.getElementById('nopp').disabled=false;
        document.getElementById('nopp').value='';
        document.getElementById('contain').innerHTML='';
}
function dataKeExcel(ev,tujuan)
{
        nopp=document.getElementById('txtNopp').value;
        tglSdt=document.getElementById('tgl_cari').value;
        statPP=document.getElementById('statPP').options[document.getElementById('statPP').selectedIndex].value;
        period=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
        param='nopp='+nopp+'&tglSdt='+tglSdt+'&statPP='+statPP+'&periode='+period;
        judul='Riwayat Permintaan Barang Excel';	
        //alert(param);
        //printFile(param,tujuan,judul,ev)	
        printFile(param,tujuan,judul,ev)	
}
function dataKePDF(ev)
{
        //nopp=document.getElementById('nopp').value;
        nopp=document.getElementById('txtNopp').value;
        tglSdt=document.getElementById('tgl_cari').value;
        statPP=document.getElementById('statPP').options[document.getElementById('statPP').selectedIndex].value;
        period=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
        param='proses=pdf'+'&nopp='+nopp+'&tglSdt='+tglSdt+'&statPP='+statPP+'&periode='+period;
        //alert(param);
        tujuan='log_slave_2riwayatPPpdf.php';
        judul='Report PDF';		

        //alert(param);
        printFile(param,tujuan,judul,ev)		
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function previewDetail(nopP,ev)
{
        showDetail(nopP,ev);
        rnopp=nopP;
        param='rnopp='+rnopp+'&method=getDetailPP';
        tujuan='log_slave_save_log_pp.php';
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
                                                        document.getElementById('contDetail').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  

}
function showDetail(noPP,ev)
{
        title="Detail Permintaan Pembelian";
        content="<fieldset><legend>"+noPP+"</legend><div id=contDetail ></div></fieldset><input type=hidden id=datPP name=datPP value="+noPP+" />";
        width='800';
        height='600';
        showDialog1(title,content,width,height,ev);	
}

//================================================================

function loadPPChat(nopp,kodebarang,ev)
{
        title="Chat:"+nopp+" - "+kodebarang;
        content="<iframe frameborder=0 style='width:590px;height:490px;' src='log_slaveChatPP.php?nopp="+nopp+"&kodebarang="+kodebarang+"'></iframe>";
        width='600';
        height='450';
        showDialog1(title,content,width,height,ev);	
}
function searchSupplier(title,content,ev)
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
function setData(kdSupp)
{
    l=document.getElementById('supplier_id');

    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==kdSupp)
                {
                    l.options[a].selected=true;
                }
        }

       closeDialog();
           get_supplier();
}