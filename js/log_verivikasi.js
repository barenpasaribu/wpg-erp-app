// JavaScript Document
function AddPur(id)
{
        nopp= document.getElementById('nopp_'+id).innerHTML;
        kdbrg=document.getElementById('kd_brg_'+id).innerHTML;
        purchase=document.getElementById('purchase_name_'+id).options[document.getElementById('purchase_name_'+id).selectedIndex].value;
        jmlh_realisai=document.getElementById('realisasi_'+id).value;
        met=document.getElementById('method').value;
        met='insert_detail_pp';
        document.getElementById('lokalpusat_'+id);
                lokal=document.getElementById('lokalpusat_'+id);
                if(lokal.checked==true)
                {
                        lokal.value=1;
                }
                else
                {
                        lokal.value=0;
                }
        param='nopp='+nopp+'&kdbrg='+kdbrg+'&purchase='+purchase+'&jmlh_realisai='+jmlh_realisai;
        param+='&method='+met+'&lokal='+lokal.value;
        tujuan='log_slave_save_verivikasi.php';

        //alert(param);
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                document.getElementById('lokalpusat_'+id).disabled=true;
                                document.getElementById('purchase_name_'+id).disabled=true;
                                document.getElementById('realisasi_'+id).disabled=true;
                                document.getElementById('contain').value=con.responseText;
                   //alert(con.reponseText);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan , param, respon);
}
function EditPur(id)
{
    a=confirm('Are you sure want to edit');
    if(a)
        {
            document.getElementById('lokalpusat_'+id).disabled=false;
            document.getElementById('purchase_name_'+id).disabled=false;
            document.getElementById('realisasi_'+id).disabled=false;
        }
        else
            {
                return;
            }

}
function searchBrg(id,title,content,ev)
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
                alert('Text too short');
        }
        else
        {
                param='txtfind='+txt+'&method=cariBarang';
                tujuan='log_slave_save_verivikasi.php';
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
function setBrg(kdBrng)
{
        nmr=document.getElementById('nomor').value;
        notrans_=document.getElementById('notrans_'+nmr).value;
        kdbrg=document.getElementById('kdbrg_'+nmr).value;
        kdBrang=kdBrng;
        tujuan='log_slave_save_verivikasi.php';
        param="nopp="+notrans_+"&kdbrg="+kdbrg+"&method=updateDtbarang"+"&kdBrgBaru="+kdBrang;
        //alert(param);
        if(confirm("Anda yakin mengubah barang ini"))
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
                                                        displayList();
                                                        closeDialog();

                                                        //document.getElementById('container').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	

}

function EditBrg(id)
{
        searchBrg(id);
}
function cariNopp()
{
        txtSearch=trim(document.getElementById('txtsearch').value);
        tglCari=trim(document.getElementById('tgl_cari').value);
        pur=document.getElementById('purId').options[document.getElementById('purId').selectedIndex].value;
        unitIdCr=document.getElementById('unitIdCr').options[document.getElementById('unitIdCr').selectedIndex].value;
        klmpKbrg=document.getElementById('klmpkBrg').options[document.getElementById('klmpkBrg').selectedIndex].value;
        kdBarangCari=document.getElementById('kdBarangCari').options[document.getElementById('kdBarangCari').selectedIndex].value;
        stat=document.getElementById('statPP').options[document.getElementById('statPP').selectedIndex].value;
        /*
		met=document.getElementById('method');
        met=met.value='cari_pp';
        met=trim(met);
		*/
		met = 'refresh_data';
        param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met+'&userid='+pur+'&unitIdCr='+unitIdCr+'&klmpKbrg='+klmpKbrg;
        param+='&kdBarangCari='+kdBarangCari+'&statPP='+stat;

        tujuan='log_slave_save_verivikasi.php';
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
function displayList()
{
        param='method=refresh_data';
        tujuan='log_slave_save_verivikasi.php';
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

                                                                document.getElementById('txtsearch').value='';
                                                                document.getElementById('tgl_cari').value='';
                                                                document.getElementById('purId').value='';
                                                                document.getElementById('klmpkBrg').value='';
                                                                document.getElementById('unitIdCr').value='';
                                                                document.getElementById('contain').innerHTML=con.responseText;
                                                                document.getElementById('kdBarangCari').innerHTML="<option value=''>"+semua+"</option>";
                                                                document.getElementById('statPP').value='2';	

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
                pur=document.getElementById('purId').options[document.getElementById('purId').selectedIndex].value;
                unitIdCr=document.getElementById('unitIdCr').options[document.getElementById('unitIdCr').selectedIndex].value;
                met=document.getElementById('method');
                met=met.value='cari_pp';
                met=trim(met);
                param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met+'&userid='+pur+'&unitIdCr='+unitIdCr;
                if(document.getElementById('statPP').checked==true)
                {
                 param+='&statPP=1';
                }

                //param='method=refresh_data';
                param+='&page='+num;
                tujuan = 'log_slave_save_verivikasi.php';
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
function cariData(num)
{		

                param='method=refresh_data';
                param+='&page='+num;
                tujuan = 'log_slave_save_verivikasi.php';
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

function loadPPChat(nopp,kodebarang,ev)
{
        title="Chat:"+nopp+" - "+kodebarang;
        content="<iframe frameborder=0 style='width:590px;height:390px;' src='log_slaveChatPP.php?nopp="+nopp+"&kodebarang="+kodebarang+"'></iframe>";
        width='600';
        height='400';
        showDialog1(title,content,width,height,ev);	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}

function dataKeExcel(ev)
{
        txtSearch=trim(document.getElementById('txtsearch').value);
        tglCari=trim(document.getElementById('tgl_cari').value);
        pur=document.getElementById('purId').options[document.getElementById('purId').selectedIndex].value;
        unitIdCr=document.getElementById('unitIdCr').options[document.getElementById('unitIdCr').selectedIndex].value;
        klmpKbrg=document.getElementById('klmpkBrg').options[document.getElementById('klmpkBrg').selectedIndex].value;
        kdBarangCari=document.getElementById('kdBarangCari').options[document.getElementById('kdBarangCari').selectedIndex].value;
        stat=document.getElementById('statPP').options[document.getElementById('statPP').selectedIndex].value;
        met=document.getElementById('method');
        met=met.value='excelData';
        met=trim(met);
        param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met+'&userid='+pur+'&unitIdCr='+unitIdCr+'&klmpKbrg='+klmpKbrg;
        param+='&kdBarangCari='+kdBarangCari+'&statPP='+stat;

        tujuan='log_slave_save_verivikasi.php';
        //alert(param);
        //param='nopp='+nopp+'&tglSdt='+tglSdt+'&statPP='+statPP;
        judul='PR List Spreadsheet';	
        //alert(param);
        //printFile(param,tujuan,judul,ev)	
        printFile(param,tujuan,judul,ev)	
}

function Summary()
{

}
function ajukanForm(pp)
{
        agree();
        met=document.getElementById('method').value;
        met='getForm';
        param='method='+met+'&nopp='+pp;
        tujuan='log_slave_save_verivikasi.php';
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
                                                                        /*alert(con.responseText);
                                                                        return;*/

                                                                                document.getElementById('container').innerHTML=con.responseText;
//										//return con.responseText;
//									
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
function cancel()
{
        closeDialog();
}
function agree()
{
        width='350';
        height='380';
        //nopp=document.getElementById('nopp_'+id).value;
        content="<div id=container></div>";
        ev='event';
        title="Submission Form";
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}
function get_data_po(rnopo)
{
        agree();
        met=document.getElementById('method').value;
        met='getFormTolak';
        param='method='+met+'&nopo='+rnopo;
        tujuan='log_slave_release_po.php';
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
                                                                        /*alert(con.responseText);
                                                                        return;*/

                                                                                document.getElementById('container').innerHTML=con.responseText;
//										//return con.responseText;
//									
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
function forwardPP()
{
        //kolom=document.getElementById('kolom').value;
        nik=document.getElementById('user_id').value;
        cmnt_hsl=document.getElementById('comment_fr').value;
        rnopp=document.getElementById('nopp').value;
        met=document.getElementById('method');
        if(cmnt_hsl=='')
        {
                alert('Please write a note');
                return;
        }
        document.getElementById('Ajukan').disabled=true;
        //document.getElementById('Tutup').disabled=true;
        met=met.value='insertFwrdpp';
        param='userid='+nik+'&cm_hasil='+cmnt_hsl+'&method='+met+'&nopp='+rnopp;
        tujuan='log_slave_save_verivikasi.php';
        /*alert(param);
        return;*/
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
                                                            //document.getElementById('contain').innerHTML=con.responseText;
                                                                displayList();
                                                                closeDialog();
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
function rejected_pp_proses()
{
        rnopp=trim(document.getElementById('rnopp').value);
        met=document.getElementById('method');
        met=met.value='rejected_pp_ex';
        comment=trim(document.getElementById('cmnt_tolak').value);
        klm=document.getElementById('kolom').value;
        usrid=document.getElementById('user_id').value;
        if(comment=='')
        {
                alert('Please leave a reason');	
        }	
        else
        {
                param='nopp='+rnopp+'&method='+met+'&comment='+comment+'&kolom='+klm+'&userid='+usrid;
                tujuan='log_slave_save_verivikasi.php';
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
                                                                        closeDialog();
                                                                        displayList();
                                                                        //alert('Berhasil');
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
function reject_some_pp()
{
        //closeDialog();
        width='850';
        height='450';
        content="<div id=container></div>";
        ev='event';
        title="Form Penolakan";
        showDialog1(title,content,width,height,ev);
}
function rejected_some_proses(nopp,klm)
{
        reject_some_pp();
        //met=document.getElementById('method').value;
        nop=nopp
        kolom=klm;
        met='get_form_rejected_some';
        param='method='+met+'&nopp='+nop+'&kolom='+kolom;
        //alert(param);
        tujuan='log_slave_save_verivikasi.php';
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
                                                                        document.getElementById('container').innerHTML=con.responseText;
                                                                        //return con.responseText;
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         } 	

}
function rejected_some(id,no,kolom)
{
		konfirmasi = confirm("Anda Yakin ?");
		if(konfirmasi){
			rnopp=id;
			kode_brg=document.getElementById('kdBrg_'+no).innerHTML;
			user_login=document.getElementById('user_id').value;
			alsn=document.getElementById('alsnDtolak_'+no).value;
			//kolom=document.getElementById('kolom').value;
			/*alert(nopp);
			return;*/			
			met='rejected_some_input_verifikasi';
			param='nopp='+rnopp+'&kd_brg='+kode_brg+'&method='+met+'&userid='+user_login+'&kolom='+kolom+'&alsnDtolk='+alsn;
			tujuan='log_slave_save_verivikasi.php';
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
															//alert('Berhasil');
															rejected_some_proses(id,kolom);
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
function rejected_some_done()
{
        //alert(kolom);
        closeDialog();
        displayList();
        //alert(kolom);
}

function summForm()
{
        //closeDialog();
        width='850';
        height='400';
        content="<div id=container style='overflow:auto;width:800px;height:350px;'></div>";
        ev='event';
        title="Summary";
        showDialog1(title,content,width,height,ev);
}
function displaySummary()
{
    summForm();
    param='method=getSummary';
    tujuan='log_slave_save_verivikasi.php'
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
                           // alert(con.responseText);
                            document.getElementById('container').innerHTML=con.responseText;
                            //return con.responseText;
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
function summForm2()
{
        //closeDialog();
        width='850';
        height='400';
        content="<div id=container2 style='overflow:auto;width:800px;height:350px;'></div>";
        ev='event';
        title="Detail Summary";
        showDialog2(title,content,width,height,ev);
}
function detailData(krywnId,period)
{
    summForm2();
    userid=krywnId;
    prd=period;
    param='method=detailSum'+'&userid='+userid+'&periode='+prd;
    tujuan='log_slave_save_verivikasi.php'
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
                           // alert(con.responseText);
                            document.getElementById('container2').innerHTML=con.responseText;
                            //return con.responseText;
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
function getSumData()
{
    prd=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
    param='method=getSummary'+'&periode='+prd;
    tujuan='log_slave_save_verivikasi.php'
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
                           // alert(con.responseText);
                            ar=con.responseText.split("###");
                            document.getElementById('isiContain').innerHTML=ar[0];
                            document.getElementById('tglPeriode').innerHTML=ar[1];
                            //return con.responseText;
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
function detailExcel(pur,pt,prd,ev)
{
    met=document.getElementById('method');
    usr=pur;
    kdpt=pt;
    period=prd;
    met=met.value='dataDetail';
    met=trim(met);
    param='method='+met+'&userid='+usr+'&kodeorg='+kdpt+'&periode='+period;
   // alert(param);
    tujuan='log_slave_save_verivikasi.php';
    judul='List Permintaan Barang';	
    printFile(param,tujuan,judul,ev)	
}


function ajukanForm(pp)
{
        agree();
        met=document.getElementById('method').value;
        met='getForm';
        param='method='+met+'&nopp='+pp;
        tujuan='log_slave_save_verivikasi.php';
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
                                                                        /*alert(con.responseText);
                                                                        return;*/

                                                                                document.getElementById('container').innerHTML=con.responseText;
//										//return con.responseText;
//									
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
function cancel()
{
        closeDialog();
}
function agree()
{
        width='350';
        height='380';
        //nopp=document.getElementById('nopp_'+id).value;
        content="<div id=container></div>";
        ev='event';
        title="Submission Form";
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}
function get_data_po(rnopo)
{
        agree();
        met=document.getElementById('method').value;
        met='getFormTolak';
        param='method='+met+'&nopo='+rnopo;
        tujuan='log_slave_release_po.php';
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
                                                                        /*alert(con.responseText);
                                                                        return;*/

                                                                                document.getElementById('container').innerHTML=con.responseText;
//										//return con.responseText;
//									
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
function forwardPP()
{
        //kolom=document.getElementById('kolom').value;
        nik=document.getElementById('user_id').value;
        cmnt_hsl=document.getElementById('comment_fr').value;
        rnopp=document.getElementById('nopp').value;
        met=document.getElementById('method');
        if(cmnt_hsl=='')
        {
                alert('Please leave a note');
                return;
        }
        document.getElementById('Ajukan').disabled=true;
        //document.getElementById('Tutup').disabled=true;
        met=met.value='insertFwrdpp';
        param='userid='+nik+'&cm_hasil='+cmnt_hsl+'&method='+met+'&nopp='+rnopp;
        tujuan='log_slave_save_verivikasi.php';
        /*alert(param);
        return;*/
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
                                                            //document.getElementById('contain').innerHTML=con.responseText;
                                                                displayList();
                                                                closeDialog();
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
function rejected_pp_proses()
{
        rnopp=trim(document.getElementById('rnopp').value);
        met=document.getElementById('method');
        met=met.value='rejected_pp_ex';
        comment=trim(document.getElementById('cmnt_tolak').value);
        klm=document.getElementById('kolom').value;
        usrid=document.getElementById('user_id').value;
        if(comment=='')
        {
                alert('Please loave a note');	
        }	
        else
        {
                param='nopp='+rnopp+'&method='+met+'&comment='+comment+'&kolom='+klm+'&userid='+usrid;
                tujuan='log_slave_save_verivikasi.php';
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
                                                                        closeDialog();
                                                                        displayList();
                                                                        //alert('Berhasil');
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
function reject_some_pp()
{
        //closeDialog();
        width='850';
        height='450';
        content="<div id=container></div>";
        ev='event';
        title="Form Penolakan";
        showDialog1(title,content,width,height,ev);
}
function rejected_some_proses(nopp,klm)
{
        reject_some_pp();
        //met=document.getElementById('method').value;
        nop=nopp
        kolom=klm;
        met='get_form_rejected_some';
        param='method='+met+'&nopp='+nop+'&kolom='+kolom;
        //alert(param);
        tujuan='log_slave_save_verivikasi.php';
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
                                                                        document.getElementById('container').innerHTML=con.responseText;
                                                                        //return con.responseText;
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         } 	

}

function rejected_some_done()
{
        //alert(kolom);
        closeDialog();
        displayList();
        //alert(kolom);
}

function summForm()
{
        //closeDialog();
        width='1100';
        height='500';
        content="<div id=container style='overflow:auto;width:100%;height:480px;'></div>";
        ev='event';
        title="Summary";
        showDialog1(title,content,width,height,ev);
}
function displaySummary()
{
    summForm();
    param='method=getSummary';
    tujuan='log_slave_save_verivikasi.php'
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
                           // alert(con.responseText);
                            document.getElementById('container').innerHTML=con.responseText;
                            //return con.responseText;
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
function summForm2()
{
        //closeDialog();
        width='1150';
        height='500';
        content="<div id=container2 style='overflow:auto;width:100%;height:480px;'></div>";
        ev='event';
        title="Detail Summary";
        showDialog2(title,content,width,height,ev);
}
function detailData(krywnId,period)
{
    summForm2();
    userid=krywnId;
    prd=period;
    param='method=detailSum'+'&userid='+userid+'&periode='+prd;
    tujuan='log_slave_save_verivikasi.php'
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
                           // alert(con.responseText);
                            document.getElementById('container2').innerHTML=con.responseText;
                            //return con.responseText;
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
function getSumData()
{
    prd=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
    param='method=getSummar'+'&periode='+prd;
    tujuan='log_slave_save_verivikasi.php'
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
                           // alert(con.responseText);
                           ar=con.responseText.split("###");
                            document.getElementById('isiContain').innerHTML=ar[0];
                            document.getElementById('tglPeriode').innerHTML=ar[1];
                            //return con.responseText;
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
function detailExcel2(pur,prd,ev)
{
    met=document.getElementById('method');
    usr=pur;
    period=prd;
    met=met.value='dataDetailEx';
    met=trim(met);
    param='method='+met+'&userid='+usr+'&periode='+period;
   // alert(param);
    tujuan='log_slave_save_verivikasi.php';
    judul='List User';	
    printFile(param,tujuan,judul,ev);
}
function formListPP()
{
        //closeDialog();
        width='750';
        height='450';
        content="<div id=container></div>";
        ev='event';
        title="List Item PP";
        showDialog1(title,content,width,height,ev);
}
function getDataPP(ppno)
{
        formListPP();
        nopp=ppno;
        param='method=listVerivikasiPP'+'&nopp='+nopp;
        tujuan='log_slave_save_verivikasi.php';
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
                           // alert(con.responseText);
                          document.getElementById('container').innerHTML=con.responseText;
                          document.getElementById('saveAll').disabled=false;
                          document.getElementById('purId2').disabled=false;
                          document.getElementById('lokId').disabled=false;
                            //return con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    
}
function getDataPP2(ppno)
{
        formListPP();
        nopp=ppno;
        param='method=listVerivikasiPP2'+'&nopp='+nopp;
        tujuan='log_slave_save_verivikasi.php';
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
                           // alert(con.responseText);
                          document.getElementById('container').innerHTML=con.responseText;
                          document.getElementById('saveAll2').disabled=false;
                          document.getElementById('purId2_2').disabled=false;
                          document.getElementById('lokId_2').disabled=false;
                            //return con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    
}


function getDataPP5(ppno)
{
        formListPP();
        nopp=ppno;
        param='method=listAddPP'+'&nopp='+nopp;
        tujuan='log_slave_save_verivikasi.php';
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
                           // alert(con.responseText);
                          document.getElementById('container').innerHTML=con.responseText;
//                          document.getElementById('saveAll').disabled=false;
//                          document.getElementById('purId2').disabled=false;
//                          document.getElementById('lokId').disabled=false;
                            //return con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    
}
function cariBarang()
{
    document.getElementById('listDataPP').style.display='none';
    document.getElementById('cariBarang').style.display='block';
    document.getElementById('no_brg').value='';
    document.getElementById('container5').innerHTML='';
}
function cariBarangGet()
{
        txt=trim(document.getElementById('no_brg').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Text too short');
        }
        else
        {
                param='txtfind='+txt+'&method=cariBarang'+'&pil=2';
                tujuan='log_slave_save_verivikasi.php';
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
                                                        document.getElementById('container5').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function setBrg2(kdbarang,nmbarang,sat)
{
    document.getElementById('nmBarang').value=nmbarang;
    document.getElementById('kdBarang').value=kdbarang;
    document.getElementById('satuanForm').value=sat;
    document.getElementById('listDataPP').style.display='block';
    document.getElementById('cariBarang').style.display='none';
}
function saveSemua(x)
{
        document.getElementById('saveAll').disabled=true;
        document.getElementById('purId2').disabled=true;
        document.getElementById('lokId').disabled=true;
        nopp=document.getElementById('ppno').value;
        purchaser=document.getElementById('purId2').options[document.getElementById('purId2').selectedIndex].value;
        lokal=document.getElementById('lokId').options[document.getElementById('lokId').selectedIndex].value;
        //lokal=document.getElementById('lokId').value;
        totlBrg=document.getElementById('totalBrg').innerHTML;
        kd_brg=document.getElementById('kdBrg_'+x).innerHTML;
        jmlh_realisai=document.getElementById('jmlh_'+x).innerHTML;
        param='method=insertPurchaser'+'&nopp='+nopp+'&purchase='+purchaser+'&lokal='+lokal;
        param+='&kdbrg='+kd_brg+'&jmlh_realisai='+jmlh_realisai;
        //alert(param);
        tujuan='log_slave_save_verivikasi.php';
        if(x==1 && confirm('Proceed ?'))
        post_response_text(tujuan, param, respog);
 else
        post_response_text(tujuan, param, respog);
                 document.getElementById('rew_'+x).style.backgroundColor='orange';
        function respog()
    {
          if(con.readyState==4)
          {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                                        document.getElementById('rew_'+x).style.backgroundColor='red';
                    }
                    else {
                           // alert(con.responseText);
                          //document.getElementById('container').innerHTML=con.responseText;
                            //return con.responseText;
                                                        b=x;
                                             row=x+1;
                                                 x=row;
                        if(x<=totlBrg)
                         {   
                                                         document.getElementById('rew_'+b).style.backgroundColor='green';
                             saveSemua(x);
                         }
                         else
                         {
                                    nummr=document.getElementById('halPage').value;
                                    document.getElementById('txtsearch').value='';
                                    document.getElementById('tgl_cari').value='';
                                    document.getElementById('statPP').checked=false;
                                    cariData(nummr);
                                     //displayList();
                                     cancel();
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
function tambahBarang()
{
    nopp=document.getElementById('noppAja').innerHTML;
    tglSdt=document.getElementById('tglSdt').innerHTML;
    kd_brg=document.getElementById('kdBarang').value;
    jmlh_realisai=document.getElementById('jmlhBrg').value;
    param='method=addBarangTopp'+'&nopp='+nopp+'&tglSdt='+tglSdt;
    param+='&kdbrg='+kd_brg+'&jmlh_realisai='+jmlh_realisai;
    tujuan='log_slave_save_verivikasi.php';
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
                                                        //document.getElementById('container5').innerHTML=con.responseText;
                                                        if(con.responseText==1)
                                                            {
                                                                alert('Done');
                                                                displayList();
                                                                document.getElementById('nmBarang').value='';
                                                                document.getElementById('satuanForm').value='';
                                                                document.getElementById('jmlhBrg').value='';
                                                                document.getElementById('kdBarang').value='';

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
function saveSemua2(x)
{
        document.getElementById('saveAll2').disabled=true;
        document.getElementById('purId2_2').disabled=true;
        document.getElementById('lokId_2').disabled=true;
        nopp=document.getElementById('ppno').value;
        purchaser=document.getElementById('purId2_2').options[document.getElementById('purId2_2').selectedIndex].value;
        lokal=document.getElementById('lokId_2').options[document.getElementById('lokId_2').selectedIndex].value;
        //lokal=document.getElementById('lokId').value;
        totlBrg=document.getElementById('totalBrg_2').innerHTML;
        kd_brg=document.getElementById('kdBrg_2_'+x).innerHTML;
        jmlh_realisai=document.getElementById('jmlh_2_'+x).innerHTML;
        param='method=insertPurchaser'+'&nopp='+nopp+'&purchase='+purchaser+'&lokal='+lokal;
        param+='&kdbrg='+kd_brg+'&jmlh_realisai='+jmlh_realisai;
        //alert(param);
        tujuan='log_slave_save_verivikasi.php';
        if(x==1 && confirm('Proceed?'))
        post_response_text(tujuan, param, respog);
 else
        post_response_text(tujuan, param, respog);
                 document.getElementById('rew_'+x).style.backgroundColor='orange';
        function respog()
    {
          if(con.readyState==4)
          {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                                        document.getElementById('rew_'+x).style.backgroundColor='red';
                    }
                    else {
                           // alert(con.responseText);
                          //document.getElementById('container').innerHTML=con.responseText;
                            //return con.responseText;
                                                        b=x;
                                             row=x+1;
                                                 x=row;
                        if(x<=totlBrg)
                         {   
                                                         document.getElementById('rew_'+b).style.backgroundColor='green';
                             saveSemua2(x);
                         }
                         else
                         {
                                                         displayTools();
                                                         cancel();
                           // alert('Done');
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
function displayTools()
{
    param='method=loadTools';
    tujuan='log_slave_save_verivikasi.php';

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
                           // alert(con.responseText);
                            document.getElementById('contain').innerHTML=con.responseText;
                            //return con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    
}
function detailPo(x)
{
    kodeorg=document.getElementById('kodeOrg_'+x).innerHTML;
    param='method=loadPPDetail'+'&kodeorg='+kodeorg+'&brsKe='+x;
    tujuan='log_slave_save_verivikasi.php';
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
                           // alert(con.responseText);
                            document.getElementById('dataPO_'+x).innerHTML=con.responseText;
                            //return con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	
}
function closeList(b)
{
    document.getElementById('dataPO_'+b).innerHTML='';
}
function getBarangCari()
{
    klmpKbrg=document.getElementById('klmpkBrg').options[document.getElementById('klmpkBrg').selectedIndex].value;
    param='method=loadBarang'+'&klmpKbrg='+klmpKbrg;
    tujuan='log_slave_save_verivikasi.php';
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
                           // alert(con.responseText);
                            document.getElementById('kdBarangCari').innerHTML=con.responseText;
                            //return con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	
}

function searchBrgCari(title,content,ev)
{
        klmpk=document.getElementById('klmpkBrg').options[document.getElementById('klmpkBrg').selectedIndex].value;
        if(klmpk=='')
            {
                alert("Material group required!!");
                return;
            }
            idKlmpk="<input type='hidden' id='idKlmpk' value='"+klmpk+"' />"
            content=content+idKlmpk;
        width='500';
        height='400';
        showDialog2(title,content,width,height,ev);
        //findBrg();
        //alert('asdasd');
}
function findBrg2()
{
    klmpKbrg=document.getElementById('idKlmpk').value;
    nmBrg=document.getElementById('nmBrg').value;

    param='klmpKbrg='+klmpKbrg+'&nmBrg='+nmBrg+'&method=getBarang';
    tujuan='log_slave_save_verivikasi.php';
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
                                           document.getElementById('containerBarang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
         }  

}
function setData(kdbrg,namaBarang,sat)
{
    ldata=document.getElementById('kdBarangCari');
    for(adr=0;adr<ldata.length;adr++)
    {
         if(ldata.options[adr].value==kdbrg)
            {
                ldata.options[adr].selected=true;
            }
    }

    closeDialog();
}
