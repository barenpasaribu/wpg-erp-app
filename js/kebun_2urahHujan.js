function getPeriode()
{
    unit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
    param='proses=getPeriode'+'&kdUnit='+unit;
    tujuan='kebun_2slaveCurahHujan.php';

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
                    //	alert(con.responseText);
                    var result = con.responseText;
                    // console.log(con.responseText);
                    // console.log(result);
                    document.getElementById('periodeUnit').innerHTML=result;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respog);
}

function getPeriodeOrg()
{
    unit=document.getElementById('kdUnitOrg').options[document.getElementById('kdUnitOrg').selectedIndex].value;
    param='proses=getPeriode'+'&kdUnitOrg='+unit;
    tujuan='kebun_2slaveCurahHujanOrg.php';
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
                    //	alert(con.responseText);
                    document.getElementById('periodeDt').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}



////// JavaScript Document
//function save_pil()
//{
//	document.getElementById('company_id').disabled=true;
//	document.getElementById('period').disabled=true;
//
//	cmpId=document.getElementById('company_id').value;
//	period=document.getElementById('period').value;
//	param='cmpId='+cmpId+'&period='+period+'&proses=GetData';
//	tujuan='kebun_2slaveCurahHujan.php';
//	post_response_text(tujuan, param, respog);
//	function respog()
//	{
//		      if(con.readyState==4)
//		      {
//			        if (con.status == 200) {
//						busy_off();
//						if (!isSaveResponse(con.responseText)) {
//							alert('ERROR TRANSACTION,\n' + con.responseText);
//						}
//						else {
//						//	alert(con.responseText);
//						document.getElementById('contain').innerHTML=con.responseText;
//						}
//					}
//					else {
//						busy_off();
//						error_catch(con.status);
//					}
//		      }
//	 }
//
//}
//function ganti_pil()
//{
//	document.getElementById('company_id').disabled=false;
//	document.getElementById('period').disabled=false;
//	document.getElementById('contain').innerHTML='';
//}
//function dataKeExcel(ev,tujuan)
//{
//	cmpId=document.getElementById('company_id').value;
//	period=document.getElementById('period').value;
//	param='cmpId='+cmpId+'&period='+period;
//	judul='Report Ms.Excel';
//	//alert(param);
//	printFile(param,tujuan,judul,ev)
//}
//function dataKePDF(ev)
//{
//	cmpId=document.getElementById('company_id').value;
//	period=document.getElementById('period').value;
//	param='cmpId='+cmpId+'&period='+period+'&proses=pdf';
//	//alert(param);
//	tujuan='kebun_2curahHujanPdf.php';
//	judul='Report PDF';
//
//	//alert(param);
//	printFile(param,tujuan,judul,ev)
//}
//function printFile(param,tujuan,title,ev)
//{
//   tujuan=tujuan+"?"+param;
//   width='700';
//   height='400';
//   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   showDialog1(title,content,width,height,ev);
//}