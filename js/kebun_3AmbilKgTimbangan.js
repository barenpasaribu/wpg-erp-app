// JavaScript Document

function saveData()
{
	
	kdOrg=document.getElementById('idKbn').value;
	tgl=document.getElementById('tglData').value;
	if(tgl=='')
	{
		alert("Please Insert Date/Tanggal");
		return;
	}
	param='kdOrg='+kdOrg+'&tgl='+tgl+'&proses=getData';
	//alert(param);
	tujuan='kebun_slave_3AmbilKgTimbangan.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					document.getElementById('result').style.display='block';
					document.getElementById('list_ganti').innerHTML=con.responseText;
					document.getElementById('idKbn').disabled=true;
					document.getElementById('tglData').disabled=true;
					document.getElementById('dtl_pem').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function prosesData(nospb,notrans)
{
	noSpb=nospb;
	noTrans=notrans;
	param='noSpb='+noSpb+'&noTrans='+noTrans+'&proses=PostingData';
	tujuan='kebun_slave_3AmbilKgTimbangan.php';
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
					/*document.getElementById('tglData').value='';
					document.getElementById('idKbn').value='';*/
					saveData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	if(confirm("Are You Sure Want Posting This Data"))
	post_response_text(tujuan, param, respon);
}
function cancelSave()
{
	document.getElementById('list_ganti').innerHTML='';
	document.getElementById('idKbn').disabled=false;
	document.getElementById('tglData').disabled=false;
	document.getElementById('dtl_pem').disabled=false;
	document.getElementById('idKbn').value='';
	document.getElementById('tglData').value='';
	document.getElementById('result').style.display='none';
	
}
function viewData(param,title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	ar=param.split("###");
	dataDetail(ar[0],ar[1]);
	
	//alert('asdasd');
}
function dataDetail(nospb,notrans)
{
	noSpb=nospb;
	noTrans=notrans;
	
	param='noSpb='+noSpb+'&noTrans='+noTrans+'&proses=ShowData';
	tujuan='kebun_slave_3AmbilKgTimbangan.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					/*document.getElementById('tglData').value='';
					document.getElementById('idKbn').value='';*/
					document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}