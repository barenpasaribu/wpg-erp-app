// JavaScript Document

function editData(idRow,jmlhbrs)
{
    for(adr=1;adr<=jmlhbrs;adr++)
    {
        if(adr!=idRow)
        {
            document.getElementById('lsdDt_'+adr).style.display='block';
            document.getElementById('sbsFrm_'+adr).style.display='none';
            document.getElementById('editBtn_'+adr).style.display='block';
            document.getElementById('insertBtn_'+adr).style.display='none';
            document.getElementById('jmlhDt_'+adr).style.display='block';
            document.getElementById('jmlhForm_'+adr).style.display='none';
        }
        else
        {
            document.getElementById('lsdDt_'+idRow).style.display='none';
            document.getElementById('sbsFrm_'+idRow).style.display='block';
            document.getElementById('editBtn_'+idRow).style.display='none';
            document.getElementById('insertBtn_'+idRow).style.display='block';
            document.getElementById('jmlhForm_'+idRow).style.display='block';
            document.getElementById('jmlhDt_'+adr).style.display='none';
        }
    }
    
}

function saveData(notrans,idRow)
{
    alsan=document.getElementById('sebabDt_'+idRow).value;
    unit=document.getElementById('kdOrg_'+idRow).innerHTML;
    tanggal=document.getElementById('tgl_'+idRow).innerHTML;
    jmlh=document.getElementById('dtJmlh_'+idRow).value;
    notransaksi=notrans;
    tujuan='kebun_slave_riwayat_sisipan.php';
    param='alasan='+alsan+'&notransaksi='+notransaksi+'&proses=update';
    param+='&tgl='+tanggal+'&kdOrg='+unit+'&jumlah='+jmlh;
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
                                 //	alert(con.responseText);
                                  
                                    loadData();

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
    unt=document.getElementById('unitId').options[document.getElementById('unitId').selectedIndex].value;
    tglsatu=document.getElementById('tgl1').value;
    tgldua=document.getElementById('tgl2').value;
    tujuan='kebun_slave_riwayat_sisipan.php';
    param='unitId='+unt+'&tgl1='+tglsatu+'&proses=preview'+'&tgl2='+tgldua;
    //alert(param);
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
                                 //	alert(con.responseText);
                                    document.getElementById('printContainer').innerHTML=con.responseText;
                              
//                                    document.getElementById('lsdDt_'+idRow).style.display='block';
//                                    document.getElementById('sbsFrm_'+idRow).style.display='none';
//                                    document.getElementById('editBtn_'+idRow).style.display='block';
//                                    document.getElementById('insertBtn_'+idRow).style.display='none';

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
}