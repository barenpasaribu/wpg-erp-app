/**
 * @author antoniuslouis
 */

function loadData()
{
        txt=document.getElementById('txtCari').value;
        if(txt=='')
            {
                param='proses=loadData';
            }
            else
                {
                    param='proses=loadData'+'&txtCari='+txt;
                }
	//alert(txt);
	tujuan='pta_slave_persetujuan.php';
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

function cariBast(num)
{
                txt=document.getElementById('txtCari').value;
		param='proses=loadData';
		param+='&page='+num+'&txtCari='+txt;
		tujuan = 'pta_slave_persetujuan.php';
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
function appSetuju(notrans,krywnid,ke)
{
    notransaksi=notrans;
    krywnId=krywnid;
    ket=document.getElementById('koment2').value;
    param='proses=appSetuju'+'&notransaksi='+notransaksi+'&krywnId='+krywnId+'&stat=1'+'&perKe='+ke+'&ket='+ket;
    tujuan = 'pta_slave_persetujuan.php';
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
function appDitolak(notrans,krywni,ke)
{
    notransaksi=notrans;
    krywnId=krywni;
    ket=document.getElementById('koments').value;
    param='proses=appSetuju'+'&notransaksi='+notransaksi+'&krywnId='+krywnId+'&stat=2'+'&perKe='+ke+'&ket='+ket;
    tujuan = 'pta_slave_persetujuan.php';
    
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
function showAppTolak(notrans,karywn,ke,ev)
{
	title="Alasan Menolak";
	content="<fieldset><legend>Alasan Menolak</legend>\n\
    <table><tr><td><textarea id=koments onkeypress=return tanpa_kutip(event)></textarea></td></tr><tr><td align=center><button class=mybutton id=dtlForm onclick=appDitolak('"+notrans+"','"+karywn+"','"+ke+"','"+ke+"')>"+tolak+"</button>";
	width='220';
	height='120';
	showDialog1(title,content,width,height,ev);	
}
function showAppSetuju(notrans,karywn,ke,ev)
{
	title="Form  Persetujuan";
	content="<div id=isiAjukan><fieldset><legend>Catatan</legend>\n\
    <table align=center><tr><td  align=center><textarea id=koment2 onkeypress=return tanpa_kutip(event)></textarea></td></tr>\n\
    <tr><td align=center><button class=mybutton id=dtlForm onclick=appSetuju('"+notrans+"','"+karywn+"','"+ke+"')>"+setujuak+"</button>";
    content+="<button class=mybutton id=dtlForm onclick=appAjukan('"+notrans+"','"+karywn+"','"+ke+"')>"+ajukan+"</button></td></tr></table></div>";
	width='320';
	height='150';
	showDialog1(title,content,width,height,ev);	
}
function appAjukan(notrans,karywn,ke,ev)
{
    notransaksi=notrans;
    krywnId=karywn;
    param='proses=getForm'+'&notransaksi='+notransaksi+'&krywnId='+krywnId+'&stat=3'+'&perKe='+ke;
    tujuan = 'pta_slave_persetujuan.php';
    
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
                                            document.getElementById('isiAjukan').innerHTML=con.responseText;
                                        
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function saveAjukan(notrans,ke)
{
    notransaksi=notrans;
    krywnId=document.getElementById('dtKary').options[document.getElementById('dtKary').selectedIndex].value;
    kt=document.getElementById('koments').value;
    param='proses=appSetuju'+'&notransaksi='+notransaksi+'&krywnId='+krywnId+'&stat=3'+'&perKe='+ke+'&ket='+kt;
    tujuan = 'pta_slave_persetujuan.php';
    
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
                                            document.getElementById('isiAjukan').innerHTML=con.responseText;
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

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function previewPdf(notrans,ev)
{
        notransaksi=notrans;
        param='proses=prevPdf'+'&notransaksi='+notransaksi;
	tujuan = 'pta_slave_persetujuan.php?'+param;	
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
   showDialog1('Detail Alokasi',content,width,height,ev); 
}

