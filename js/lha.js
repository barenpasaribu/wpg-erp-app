// JavaScript Document

function getAfd()
{
	kdOrg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
	param='&kdOrg='+kdOrg;
	tujuan='lha_slave_print';
        if(kdOrg=='')
        {}
        else
	post_response_text(tujuan+'.php?proses=getAfdAll', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		     document.getElementById('kdAfd').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
}

function Clear1()
{
	document.getElementById('tgl1').value='';
	document.getElementById('kdOrg').value=''; 
        getAfd();
	document.getElementById('printContainer').innerHTML='';
}


function detailExcel(ev,tujuan)
{
    width='200';
   height='100';
//   alert(tujuan);
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('LHA',content,width,height,ev); 
}