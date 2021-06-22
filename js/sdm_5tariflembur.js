/**
 * @author Developer
 */
function simpanJ()
{
          
		cnsimpan = document.getElementById('cnfsmpn').value;
		dttdklkp = document.getElementById('tdklkp').value;
		dtsmpn = document.getElementById('dtsmpn').value;
		ids = document.getElementById('ids').value;
		org=document.getElementById('org');
        org=org.options[org.selectedIndex].value;
		gol=document.getElementById('gol');
        gol=gol.options[gol.selectedIndex].value;		
		  	
        tarif=remove_comma(document.getElementById('tarif'));
        met=document.getElementById('method').value;
        if(org=='' || gol==''|| tarif=='')
        {
                alert(dttdklkp);
        }
        else
        {
			 if(confirm(cnsimpan)){
				param='method='+met+'&ids='+ids;
                param+='&org='+org+'&gol='+gol+'&tarif='+tarif;
                tujuan='sdm_slave_save_5tariflembur.php';
				post_response_text(tujuan, param, respog);
			 }
               		
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
														alert(dtsmpn);
                                                        cancelJ();
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

function fillField(ids,loktugA,golA,tarifA)
{
	loktug=document.getElementById('org');
	for(x=0;x<loktug.length;x++)
	{
			if(loktug.options[x].value==loktugA)
			{
					loktug.options[x].selected=true;
			}
	}
	
	golongan=document.getElementById('gol');
	for(x=0;x<golongan.length;x++)
	{
			if(golongan.options[x].value==golA)
			{
					golongan.options[x].selected=true;
			}
	}
        
  
    
    document.getElementById('tarif').value=tarifA;
    document.getElementById('method').value='update';
    document.getElementById('ids').value=ids;
}


function delField(ids)
{
		cnhapus = document.getElementById('cnfhps').value;
        param='method=delete';
        param+='&ids='+ids;
        tujuan='sdm_slave_save_5tariflembur.php';
        
        if(confirm(cnhapus)){
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
                                                        cancelJ();
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

function cancelJ()
{

        document.getElementById('org').options[0].selected=true;
        document.getElementById('gol').options[0].selected=true;
        document.getElementById('tarif').value='0';		
        document.getElementById('method').value='insert';		
}
function loadData()
{
        param='method=loadData'
        tujuan='sdm_slave_save_5tariflembur.php';
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
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }
         }
}

