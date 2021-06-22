/**
 * @author antoniuslouis
 */

 function showPenguhi()
 {
 	x=document.getElementById('kodeorg');
	kodeorg=x.options[x.selectedIndex].value;
	param='kodeorg='+kodeorg;
		post_response_text('sdm_slaveLaporanPenghuniRumah.php', param, respon);
		    function respon(){
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
 
 function showPrabot()
 {
 	x=document.getElementById('kodeorg');
	kodeorg=x.options[x.selectedIndex].value;
	param='kodeorg='+kodeorg;
		post_response_text('sdm_slaveLaporanPrabotRumah.php', param, respon);
		    function respon(){
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
 
 
 
 function showTenant(kodeorg,blok,norumah,ev)
 {
 	param='kodeorg='+kodeorg+'&blok='+blok+'&norumah='+norumah;
	tujuan = 'sdm_slave_getPenghuniRumah.php';	
 //display window
   title='Tenant';
   width='500';
   height='200';

	post_response_text(tujuan, param, respon);
		    function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							content=con.responseText;
							showDialog1(title,content,width,height,ev);
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    }			
 }

 
function showAsset(kodeorg,blok,norumah,ev)
 {
 	param='kodeorg='+kodeorg+'&blok='+blok+'&norumah='+norumah;
	tujuan = 'sdm_slave_getAssetRumah.php';	
 //display window
   title='Asset Inside:';
   width='500';
   height='200';

	post_response_text(tujuan, param, respon);
		    function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							content=con.responseText;
							showDialog1(title,content,width,height,ev);
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    }			
 }