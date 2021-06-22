// JavaScript Document
/* Function getAfdeling
 * Fungsi untuk mengambil data afdeling sesuai dengan kebunnya
 * I : element kebun,id elemen afdeling
 * P : Ajax untuk mengambil data yang sesuai
 * O : Drop down afdeling terisi dengan data yang sesuai
 */
function getAfdeling(currEls,targetId,file) {
    var kebun = currEls;
    var afdeling = document.getElementById(targetId);
    
    // If blank, quit
    if(kebun.options[kebun.options.selectedIndex].value=='') {
        return;
    }
    
    // Clear Afdeling
    afdeling.options.length=0;
    tujuan=file;
    var param = "kebun="+kebun.options[kebun.options.selectedIndex].value+
        "&afdelingId="+targetId+'&proses=getAfd';
    //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                   // eval(con.responseText);
					//con.responseText;
					//alert(con.responseText);
//					return;
				document.getElementById('afdeling').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text(tujuan+'.php', param, respon);
}

function getThnTnm(file)
{
	var afd = document.getElementById('afdeling').options[document.getElementById('afdeling').selectedIndex].value;
	param="afdeling="+afd+"&proses=getThn";
	//alert(param);
	tujuan=file;
	post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                   // eval(con.responseText);
					//con.responseText;
					//alert(con.responseText);
//					return;
				document.getElementById('tahuntanam').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
/* Function getAfdeling
 * Fungsi untuk mengambil data afdeling sesuai dengan kebunnya
 * I : element kebun,id elemen afdeling
 * P : Ajax untuk mengambil data yang sesuai
 * O : Drop down afdeling terisi dengan data yang sesuai
 */
//function getThn(curr,targetId,file) {
//	//alert(curr);
//    var afdeling = curr;
//    var thnTnm = document.getElementById(targetId);
//    
//    // If blank, quit
//    if(afdeling.options[afdeling.options.selectedIndex].value=='') {
//        return;
//    }
//    
//    // Clear Afdeling
//    thnTnm.options.length=0;
//    tujuan=file;
//    var param = "afdeling="+afdeling.options[afdeling.options.selectedIndex].value+
//        "&thnTnmId="+thnTnm+'&proses=getThn';
//  //	alert(param);
//    function respon() {
//        if (con.readyState == 4) {
//            if (con.status == 200) {
//                busy_off();
//                if (!isSaveResponse(con.responseText)) {
//                    alert('ERROR TRANSACTION,\n' + con.responseText);
//                } else {
//                    // Success Response
//                   // eval(con.responseText);
//					//con.responseText;
//					//alert(con.responseText);
////					return;
//				document.getElementById('tahuntanam').innerHTML=con.responseText;
//                }
//            } else {
//                busy_off();
//                error_catch(con.status);
//            }
//        }
//    }
//    
//    post_response_text(tujuan+'.php', param, respon);
//}