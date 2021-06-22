// JavaScript Document
function savehk(fileTarget,passParam) {
	//statFr=document.getElementById('statFr');
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	//alert(param);
  //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } 
				else 
				{
						loadData();
						cancelIsi();
                }
            } 
			else 
			{
                busy_off();
                error_catch(con.status);
            }
        }
    }
   
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
	param='method=loadData';
	tujuan='log_slave_budget_5harikerja';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function fillField(tahunbudget)
{			
	param='method=getData'+'&tahunbudget='+tahunbudget;
	tujuan='log_slave_budget_5harikerja';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					ar=con.responseText.split("###");
					document.getElementById('tahunbudget').value=ar[0];
					document.getElementById('hrsetahun').value=ar[1];
					document.getElementById('hrminggu').value=ar[2];
					document.getElementById('hrlibur').value=ar[3];
					document.getElementById('hrliburminggu').value=ar[4];
					document.getElementById('hkeffektif').value=ar[5];
					document.getElementById('oldtahunbudget').value=tahunbudget;
                                        tambah();
					}
	
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    
}

function cancelIsi()
{
	document.getElementById('tahunbudget').value='';
	document.getElementById('hrsetahun').value=365;
	document.getElementById('hrminggu').value='';
	document.getElementById('hrlibur').value='';
	document.getElementById('hrliburminggu').value='';
	document.getElementById('method').value="insert";
	document.getElementById('hkeffektif').value='';
}



function tambah()
{
	//document.getElementById('tahunbudget').value;
	a=document.getElementById('hrsetahun').value;
	b=document.getElementById('hrminggu').value;
	c=document.getElementById('hrlibur').value;
	d=document.getElementById('hrliburminggu').value;
        if(b=='')
          {  b=0;}
        if(c=='')
           { c=0;}
        if(d=='')
          {  d=0;}
		  if(parseFloat(d)>parseFloat(c))
		  {
			  alert("Jumlah Libur di Hari Minggu Lebih Besar dari Hari Libur");
			  document.getElementById('hrliburminggu').value=0;
			  return;
		  }
	e=(parseFloat(a))-(parseFloat(b)+parseFloat(c)-parseFloat(d));
	//e=parseFloat(a)-parseFloat(b)-parseFloat(c)+parseFloat(d);
      //  alert(a+"__"+b+"__"+c+"____"+d);
	document.getElementById('hkeffektif').value=e;
}