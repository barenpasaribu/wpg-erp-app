// JavaScript Document
function getTangki()
{
	
	kdPbrik=document.getElementById('kdPbrik').options[document.getElementById('kdPbrik').selectedIndex].value;
	param='kdPbrik='+kdPbrik+'&proses=getTangki';
	tujuan='pabrik_slave_4persediaan.php';
	post_response_text(tujuan+'?proses=getTangki', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('kdTangki').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function viewDetail(kodeorg,tanggal,barang,ev)
{
   param='kodeorg='+kodeorg+'&tanggal='+tanggal+'&barang='+barang;
   tujuan='pabrik_slave_4persediaan_detail.php'+"?"+param;  
   width='600';
   height='300';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1('Shipping Details '+kodeorg+' '+barang+' '+tanggal,content,width,height,ev); 
	
}