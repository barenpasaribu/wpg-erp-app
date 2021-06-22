/**
 * @author antoniuslouis
 */

function loadData()
{
        nik=document.getElementById('karyidCari').options[document.getElementById('karyidCari').selectedIndex].value;
        
	param='proses=loadData'+'&pelaksana='+nik;
	tujuan='it_slave_requestResponse.php';
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
     nik=document.getElementById('karyidCari').options[document.getElementById('karyidCari').selectedIndex].value;
     param='proses=loadData'+'&pelaksana='+nik;
		param+='&page='+num;
		tujuan = 'it_slave_requestResponse.php';
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

function slsi(notrans)
{
    srn=document.getElementById('saranPelaksana').value;
    param='notransaksi='+notrans+'&proses=updateData'+'&saran='+srn;
    tujuan = 'it_slave_requestResponse.php';
    if(confirm("Anda Yakin Sudah Selesai"))
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











function showAppTolak(tgl,karywn,ev)
{
	title="Alasan Tolak";
	content="<fieldset><legend>Alasan Tolak</legend>\n\
    <table><tr><td><textarea id=koments onkeypress=return tanpa_kutip(event)></textarea></td></tr><tr><td align=center><button class=mybutton id=dtlForm onclick=appDitolak('"+tgl+"','"+karywn+"')>"+tolak+"</button>";
	width='220';
	height='120';
	showDialog1(title,content,width,height,ev);	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function previewPdf(tgl,karywn,ev)
{
        tglijin=tgl;
        krywnId=karywn;
        param='proses=prevPdf'+'&tglijin='+tglijin+'&krywnId='+krywnId;
	tujuan = 'it_slave_requestResponse.php.php?'+param;	
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

function detailData(ev,tujuan,prm)
{
    width='450';
    height='350';

    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"?proses=getDetail&notransaksi="+prm+"'></iframe>"
    showDialog1('Detail ',content,width,height,ev); 
}

function appSetujuHRD(tgl,krywnid)
{
    tglijin=tgl;
    krywnId=krywnid;
    param='proses=appSetujuHRD'+'&tglijin='+tglijin+'&krywnId='+krywnId+'&stat=1';
    tujuan = 'sdm_slave_laporan_ijin_meninggalkan_kantor.php';
    
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

function showAppTolakHRD(tgl,karywn,ev)
{
	title="Alasan Tolak";
	content="<fieldset><legend>Alasan Tolak</legend>\n\
                 <table><tr><td><textarea id=koments onkeypress=return tanpa_kutip(event)></textarea></td></tr><tr><td align=center><button class=mybutton id=dtlForm onclick=appDitolakHRD('"+tgl+"','"+karywn+"')>"+tolak+"</button>";
	width='220';
	height='120';
	showDialog1(title,content,width,height,ev);	
}

function appDitolakHRD(tgl,krywnid)
{
    tglijin=tgl;
    krywnId=krywnid;
    ket=document.getElementById('koments').value;
    param='proses=appSetujuHRD'+'&tglijin='+tglijin+'&krywnId='+krywnId+'&stat=2'+'&ket='+ket;
    tujuan = 'sdm_slave_laporan_ijin_meninggalkan_kantor.php';
    
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
