/**
 * @author Developer
 */

function loadData()
{
        param='proses=loadData';
        tujuan='sdm_slave_persetujuan_matriks_pelatihan.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
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

function loadDataParents()
{
        param='proses=loadData';
        tujuan='sdm_slave_persetujuan_matriks_pelatihan.php';
        parent.post_response_text(tujuan, param, respog);

                function respog(){
                        if (parent.con.readyState == 4) {
                                if (parent.con.status == 200) {
                                        parent.busy_off();
                                        if (!parent.isSaveResponse(parent.con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + parent.con.responseText);
                                        }
                                        else {
                                                document.getElementById('container').innerHTML=parent.con.responseText;
                                        }
                                }
                                else {
                                        parent.busy_off();
                                        error_catch(parent.con.status);
                                }
                        }
                }	

}

function cariBast(num)
{
                param='proses=loadData';
                param+='&page='+num;
                tujuan = 'sdm_slave_persetujuan_matriks_pelatihan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
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
function dtReset()
{
    document.getElementById('karyidCari').value='';
    loadData();

}
function getCariDt()
{
    kary=document.getElementById('TrnidCari').options[document.getElementById('TrnidCari').selectedIndex].value;
    param='proses=cariData'+'&TrnidCari='+kary;
    tujuan='sdm_slave_persetujuan_matriks_pelatihan.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
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
function cariData(num)
{
                kary=document.getElementById('karyidCari').options[document.getElementById('karyidCari').selectedIndex].value;
     
                param='proses=cariData'+'&karyidCari='+kary;
                param+='&page='+num;
                tujuan = 'sdm_slave_persetujuan_matriks_pelatihan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
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
function showappProses(id,sayaadalah)
{
    param='proses=showappProses'+'&ids='+id+'&stat=1';
    

   tujuan='sdm_slave_persetujuan_matriks_pelatihan.php'+"?"+param;  
   width='600';
   height='500';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Persetujuan '+sayaadalah,content,width,height,'event');	

}


//Tambahan untuk fungsi save all ==Jo 05-12-2016==
function trainingColorP(no,color)
{
        document.getElementById('namakryA' + no).style.backgroundColor=color;
        document.getElementById('remarkA' + no).style.backgroundColor=color;
        document.getElementById('cekA' + no).style.backgroundColor=color;
}
function trainingColorH(no,color)
{
        document.getElementById('namakryH' + no).style.backgroundColor=color;
        document.getElementById('remarkH' + no).style.backgroundColor=color;
        document.getElementById('cekH' + no).style.backgroundColor=color;
}

function appProses(max){
	jumldata=0;
	dosaveProses(max);
}


function dosaveProses(max)
{
	
    jumldata+=1;
        if (jumldata <= max) {
				
                if (document.getElementById('cekA' + jumldata).checked) {
					setuju=document.getElementById('stj').value;
                }
                else
                {
					setuju=document.getElementById('tlk').value;	
                }
				ids=document.getElementById('idsA'+jumldata).value;
				idkry=document.getElementById('idkryA'+jumldata).value;
				catatan=document.getElementById('remarkA'+jumldata).value;
				trainingColorP(jumldata, 'orange');
						
				param = 'krywnId=' + idkry + '&ids=' + ids+ '&alasannya=' + catatan+ '&setuju=' + setuju + '&proses=appProses';
				
				parent.post_response_text('sdm_slave_persetujuan_matriks_pelatihan.php', param, respon);
        }
        else
        {
			saveHeaderP(ids);
			//parent.window.location.reload();
        }
   function respon(){
		if (parent.con.readyState == 4) {
			if (parent.con.status == 200) {
				parent.busy_off();
				/*if (!isSaveResponse(parent.con.responseText)) {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
										trainingColorP(jumldata,'red');
								}
				else {
							trainingColorP(jumldata,'#E8F2FC');
							dosaveProses(max);
						}*/
				if (parent.con.responseText==''){
					trainingColorP(jumldata,'#E8F2FC');
				dosaveProses(max);
				}
				else {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
					trainingColorP(jumldata,'red');
				}
				
			}
			else {
				parent.busy_off();
				parent.error_catch(parent.con.status);
			}
		}
	}
	
}

function saveHeaderP(id)
{
	alselesai = document.getElementById('alselesai').value;
    param = 'ids=' + id + '&proses=appProsesAH';
	parent.post_response_text('sdm_slave_persetujuan_matriks_pelatihan.php', param, respon);
   function respon(){
		if (parent.con.readyState == 4) {
			if (parent.con.status == 200) {
				parent.busy_off();
				/*if (!isSaveResponse(parent.con.responseText)) {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
				}
				else {
						parent.window.location.reload();
					}*/
				//parent.window.location.reload();
				if (parent.con.responseText==''){
					alert(alselesai)
					parent.window.location.reload();
				}
				else {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
				}
			}
			else {
				parent.busy_off();
				parent.error_catch(parent.con.status);
			}
		}
	}
	
}

function showappHRD(id,sayaadalah)
{
    param='proses=showappHRD'+'&ids='+id+'&stat=1';
    

   tujuan='sdm_slave_persetujuan_matriks_pelatihan.php'+"?"+param;  
   width='600';
   height='500';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Persetujuan '+sayaadalah,content,width,height,'event');	

}

function appProsesHRD(max){
	jumldata=0;
	dosaveProsesHRD(max);
}
function dosaveProsesHRD(max)
{
    jumldata+=1;
        if (jumldata <= max) {
				
                if (document.getElementById('cekH' + jumldata).checked) {
					setuju=document.getElementById('stj').value;
                }
                else
                {
					setuju=document.getElementById('tlk').value;	
                }
				ids=document.getElementById('idsH'+jumldata).value;
				idkry=document.getElementById('idkryH'+jumldata).value;
				catatan=document.getElementById('remarkH'+jumldata).value;
				trainingColorH(jumldata, 'orange');
						
				param = 'krywnId=' + idkry + '&ids=' + ids+ '&alasannya=' + catatan+ '&setuju=' + setuju + '&proses=appProsesHRD';
				
				parent.post_response_text('sdm_slave_persetujuan_matriks_pelatihan.php', param, respon);
        }
        else
        {
			saveHeaderH(ids);
			//parent.window.location.reload();
        }
   function respon(){
		if (parent.con.readyState == 4) {
			if (parent.con.status == 200) {
				parent.busy_off();
				/*if (!parent.isSaveResponse(parent.con.responseText)) {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
										trainingColorH(jumldata,'red');
								}
				else {
							trainingColorH(jumldata,'#E8F2FC');
							dosaveProsesHRD(max);
						}*/
				if (parent.con.responseText==''){
					trainingColorH(jumldata,'#E8F2FC');
					dosaveProsesHRD(max);
				}
				else {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
					trainingColorH(jumldata,'red');	
				}
				
				
			}
			else {
				parent.busy_off();
				parent.error_catch(parent.con.status);
			}
		}
	}
	
}

function saveHeaderH(id)
{
	alselesai = document.getElementById('alselesai').value;
    param = 'ids=' + id + '&proses=appProsesHH';
	parent.post_response_text('sdm_slave_persetujuan_matriks_pelatihan.php', param, respon);
   function respon(){
		if (parent.con.readyState == 4) {
			if (parent.con.status == 200) {
				parent.busy_off();
				/*if (!parent.isSaveResponse(parent.con.responseText)) {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
				}
				else {
						parent.window.location.reload();
					}*/
					
				if (parent.con.responseText==''){
					alert(alselesai);
					parent.window.location.reload();
				}
				else {
					alert('ERROR TRANSACTION,\n' + parent.con.responseText);
				}
				//parent.window.location.reload();
				
			}
			else {
				parent.busy_off();
				parent.error_catch(parent.con.status);
			}
		}
	}
	
}



function showAppForward(id,ev)
{
        //title="Forward Approval";
		titles=document.getElementById('trskpd').value;
        title=titles+": ";
        content="<div id=contentForm></div>";
        width='350';
        height='110';
        showDialog1(title,content,width,height,ev);	
}


function showAppForw(id,krywnId,ev)
{
    showAppForward(id,ev)
	
    param='proses=formForward'+'&ids='+id+'&krywnId='+krywnId;
    tujuan = 'sdm_slave_persetujuan_matriks_pelatihan.php';

                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                            document.getElementById('contentForm').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }

}
function AppForw()
{
    alselesai=document.getElementById('alselesai').value;
    krywnId=document.getElementById('karyaid').value;
    ids=document.getElementById('idpl').value;
    ats=document.getElementById('karywanId').options[document.getElementById('karywanId').selectedIndex].value;
    param='proses=forwardData'+'&krywnId='+krywnId+'&ids='+ids+'&atasan='+ats;
    tujuan = 'sdm_slave_persetujuan_matriks_pelatihan.php';

                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                            alert(alselesai);
                                            closeDialog();
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
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function previewPdf(id,ev)
{
        
        param='proses=prevPdf'+'&ids='+id;
        tujuan = 'sdm_slave_persetujuan_matriks_pelatihan.php?'+param;
 //display window
   title='Print PDF';
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}

function detailExcel(ev,tujuan)
{
    width='300';
    height='100';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+'?proses=getExcel'+"'></iframe>"
    showDialog1('Print Excel',content,width,height,ev); 
}

function detailData(ev,tujuan)
{
    width='300';
   height='100';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Allocation',content,width,height,ev); 
}


