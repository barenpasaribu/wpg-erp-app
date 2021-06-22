/**
 * @author Developer
 */

function loadData()
{
        param='proses=loadData';
        tujuan='sdm_slave_persetujuan_pelatihan_eksternal.php';
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
        tujuan='sdm_slave_persetujuan_pelatihan_eksternal.php';
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
                tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';
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
    kary=document.getElementById('karyidCari').options[document.getElementById('karyidCari').selectedIndex].value;
    param='proses=cariData'+'&karyidCari='+kary;
    tujuan='sdm_slave_persetujuan_pelatihan_eksternal.php';
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
                tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';
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
function showappSetuju(kode,krywnid,sayaadalah)
{
    kode=kode;
    krywnId=krywnid;
    param='proses=showappSetuju'+'&kode='+kode+'&krywnId='+krywnId+'&stat=1';
    

   tujuan='sdm_slave_persetujuan_pelatihan_eksternal.php'+"?"+param;  
   width='600';
   height='500';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Persetujuan '+sayaadalah,content,width,height,'event');	

}

function appSetuju(kode,krywnid){
	alselesai = document.getElementById('alselesai').value;
    kode=kode;
    krywnId=krywnid;
	alasannya=document.getElementById('alasannya').value;
    param='proses=appSetuju'+'&kode='+kode+'&krywnId='+krywnId+'&alasannya='+alasannya+'&stat=1';
    tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';
	
	parent.post_response_text(tujuan, param, respog);			
	function respog(){
			if (parent.con.readyState == 4) {
					if (parent.con.status == 200) {
							parent.busy_off();
							if (!parent.isSaveResponse(parent.con.responseText)) {
									alert('ERROR TRANSACTION,\n' + parent.con.responseText);
							}
							else {
									alert(alselesai);
									//parent.window.location = 'sdm_persetujuan_pelatihan_eksternal.php'; 
									parent.window.location.reload();
									
							}
					}
					else {
							parent.busy_off();
							parent.error_catch(con.status);
					}
			}
	}


}


/*function appDitolak(kode,krywnid)
{
    kode=kode;
    krywnId=krywnid;
    param='proses=appSetuju'+'&kode='+kode+'&krywnId='+krywnId+'&stat=2';
    tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';

                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                        alert("Done");
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
function showAppTolak(kode,karywn,ev)
{
        title="Reason for rejection";
        content="<fieldset><legend>Reason for rejection</legend>\n\
    <table><tr><td><textarea id=koments onkeypress=return tanpa_kutip(event)></textarea></td></tr><tr><td align=center><button class=mybutton id=dtlForm onclick=appDitolak('"+kode+"','"+karywn+"')>"+tolak+"</button>";
        width='220';
        height='120';
        showDialog1(title,content,width,height,ev);	
}*/
function showAppTolak(kode,krywnid,sayaadalah)
{
    kode=kode;
    krywnId=krywnid;
    param='proses=showappTolak'+'&kode='+kode+'&krywnId='+krywnId+'&stat=1';
    

   tujuan='sdm_slave_persetujuan_pelatihan_eksternal.php'+"?"+param;  
   width='600';
   height='500';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Persetujuan '+sayaadalah,content,width,height,'event');	

}

function appDitolak(kode,krywnid)
{	
	alselesai = document.getElementById('alselesai').value;
    kode=kode;
    krywnId=krywnid;
	alasannya=document.getElementById('alasannya').value;//munculkan alasan ==Jo 20-03-2017==
    param='proses=appSetuju'+'&kode='+kode+'&krywnId='+krywnId+'&alasannya='+alasannya+'&stat=2';
    tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';

       parent.post_response_text(tujuan, param, respog);			
		function respog(){
			if (parent.con.readyState == 4) {
					if (parent.con.status == 200) {
							parent.busy_off();
							if (!parent.isSaveResponse(parent.con.responseText)) {
									alert('ERROR TRANSACTION,\n' + parent.con.responseText);
							}
							else {
									alert(alselesai);
									parent.window.location.reload();
									
							}
					}
					else {
							parent.busy_off();
							parent.error_catch(con.status);
					}
			}
		}	

}

function showAppForward(kode,karywn,ev)
{
	titles=document.getElementById('trskpd').value;
        title=titles;
        content="<div id=contentForm></div>";
        width='350';
        height='110';
        showDialog1(title,content,width,height,ev);	
}


function showAppForw(kode,karywn,ev)
{
    showAppForward(kode,karywn,ev)
    kode=kode;
    krywnId=karywn;

    param='proses=formForward'+'&kode='+kode+'&krywnId='+krywnId;
    tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';

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
	alselesai = document.getElementById('alselesai').value;
    krywnId=document.getElementById('karyaid').value;
    kode=document.getElementById('kodepl').value;
    ats=document.getElementById('karywanId').options[document.getElementById('karywanId').selectedIndex].value;
    param='proses=forwardData'+'&krywnId='+krywnId+'&kode='+kode+'&atasan='+ats;
    tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';

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
function previewPdf(kode,karywn,ev)
{
        kode=kode;
        krywnId=karywn;
        param='proses=prevPdf'+'&kode='+kode+'&krywnId='+krywnId;
        tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php?'+param;
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

function showappSetujuHRD(kode,krywnid,sayaadalah)
{
    kode=kode;
    krywnId=krywnid;
    param='proses=showappSetujuHRD'+'&kode='+kode+'&krywnId='+krywnId+'&stat=1';
    

   tujuan='sdm_slave_persetujuan_pelatihan_eksternal.php'+"?"+param;  
   width='600';
   height='500';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Persetujuan '+sayaadalah,content,width,height,'event');	

}

function appSetujuHRD(kode,krywnid)
{
    kode=kode;
    krywnId=krywnid;
	alasannya=document.getElementById('alasannya').value;
	alselesai = document.getElementById('alselesai').value;
    param='proses=appSetujuHRD'+'&kode='+kode+'&alasannya='+alasannya+'&krywnId='+krywnId+'&stat=1';
    tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';

		parent.post_response_text(tujuan, param, respog);			
		function respog(){
				if (parent.con.readyState == 4) {
						if (parent.con.status == 200) {
								parent.busy_off();
								if (!parent.isSaveResponse(parent.con.responseText)) {
										alert('ERROR TRANSACTION,\n' + parent.con.responseText);
								}
								else {
										alert(alselesai);
										parent.window.location.reload();
										
								}
						}
						else {
								parent.busy_off();
								parent.error_catch(con.status);
						}
				}
		}
	}

function showappTolakHRD(kode,karywn,sayaadalah)
{
	kode=kode;
    krywnId=karywn;
	
    param='proses=showappTolakHRD'+'&kode='+kode+'&krywnId='+krywnId+'&stat=2';
    

   tujuan='sdm_slave_persetujuan_pelatihan_eksternal.php'+"?"+param;  
   width='600';
   height='500';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Persetujuan '+sayaadalah,content,width,height,'event');	
        /*title="Reason for rejection";
        content="<fieldset><legend>Reason for rejection</legend>\n\
                 <table><tr><td><textarea id=koments onkeypress=return tanpa_kutip(event)></textarea></td></tr><tr><td align=center><button class=mybutton id=dtlForm onclick=appDitolakHRD('"+kode+"','"+karywn+"')>"+tolak+"</button>";
        width='220';
        height='120';
        showDialog1(title,content,width,height,ev);	
		kode=kode;
		krywnId=karywn;
		param='proses=showappTolakHRD'+'&kode='+kode+'&krywnId='+krywnId+'&stat=1';

	   tujuan='sdm_slave_persetujuan_pelatihan_eksternal.php'+"?"+param;  
	   width='600';
	   height='500';
	  
	   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
	   showDialog1('Persetujuan '+sayaadalah,content,width,height,'event');	*/
}

function appDitolakHRD(kode,krywnid)
{
    kode=kode;
    krywnId=krywnid;
	alasannya=document.getElementById('alasannya').value;
	alselesai = document.getElementById('alselesai').value;
    param='proses=appSetujuHRD'+'&kode='+kode+'&alasannya='+alasannya+'&krywnId='+krywnId+'&stat=2';
    tujuan = 'sdm_slave_persetujuan_pelatihan_eksternal.php';

        parent.post_response_text(tujuan, param, respog);			
		function respog(){
			if (parent.con.readyState == 4) {
					if (parent.con.status == 200) {
							parent.busy_off();
							if (!parent.isSaveResponse(parent.con.responseText)) {
									alert('ERROR TRANSACTION,\n' + parent.con.responseText);
							}
							else {
									alert(alselesai);
									parent.window.location.reload();
									
							}
					}
					else {
							parent.busy_off();
							parent.error_catch(con.status);
					}
			}
		}	

}
