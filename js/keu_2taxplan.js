/**
 * @author antoniuslouis
 */
function load_unit()
{
    pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    param='pt='+pt;
    tujuan='keu_slave_2taxplan.php?type=unit';
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('unit').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function getTax()
{
//    unit = unit.options[unit.selectedIndex].value;
    pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
    tanggaldari = document.getElementById('tanggaldari').value;
    tanggalsampai = document.getElementById('tanggalsampai').value;
    param='pt='+pt+'&unit='+unit+'&tanggaldari='+tanggaldari+'&tanggalsampai='+tanggalsampai;
//    alert(param);
    tujuan='keu_slave_2taxplan.php';
    post_response_text(tujuan, param, respog);
	
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    showById('printPanel');
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

function taxKeExcel(ev,tujuan)
{
        pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
        unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
        tanggaldari=document.getElementById('tanggaldari').value;
        tanggalsampai=document.getElementById('tanggalsampai').value;
	judul='Report Ms.Excel';	
        param='pt='+pt+'&unit='+unit+'&tanggaldari='+tanggaldari+'&tanggalsampai='+tanggalsampai+'&type=excel';
	printFile(param,tujuan,judul,ev)	
}

function prestasiKePDF(ev,tujuan)
{
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;
	judul='Report Ms.Excel';	
        param='tglAkhir='+tglAkhr+'&tglAwal='+tglAwl+'&type=pdf';
	printFile(param,tujuan,judul,ev)	
}

function detailExcel(ev,karyawan,tanggalmulai,tanggalsampai)
{
    param='karyawan='+karyawan+'&tanggalmulai='+tanggalmulai+'&tanggalsampai='+tanggalsampai;
    tujuan='it_slave_2prestasi_detail.php'+"?type=excel&"+param;  
    width='300';
    height='100';
  
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1('Detail Prestasi '+karyawan,content,width,height,ev); 
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='600';
   height='300';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function viewDetail(ev,karyawan)
{
    tanggalmulai=document.getElementById('tglAwal').value;
    tanggalsampai=document.getElementById('tglAkhir').value;
    param='karyawan='+karyawan+'&tanggalmulai='+tanggalmulai+'&tanggalsampai='+tanggalsampai;
    tujuan='it_slave_2prestasi_detail.php'+"?"+param;  
    width='800';
    height='400';

    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1('Detail Prestasi '+karyawan,content,width,height,ev); 
	
}




//function ambilPeriode(gudang)
//{
//	param='gudang='+gudang;
//	tujuan='log_slave_getPeriode.php';
//	post_response_text(tujuan, param, respog);
//	
//		function respog(){
//			if (con.readyState == 4) {
//				if (con.status == 200) {
//					busy_off();
//					if (!isSaveResponse(con.responseText)) {
//						alert('ERROR TRANSACTION,\n' + con.responseText);
//					}
//					else {
//						document.getElementById('periode').innerHTML=con.responseText;
//					}
//				}
//				else {
//					busy_off();
//					error_catch(con.status);
//				}
//			}
//		}	
//	
//}
//
//
//function printFile(param,tujuan,title,ev)
//{
//   tujuan=tujuan+"?"+param;  
//   width='700';
//   height='400';
//   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   showDialog1(title,content,width,height,ev); 	
//}
//
//function viewDetail(ev,karyawan)
//{
//    tanggalmulai=document.getElementById('tglAwal').value;
//    tanggalsampai=document.getElementById('tglAkhir').value;
//    param='karyawan='+karyawan+'&tanggalmulai='+tanggalmulai+'&tanggalsampai='+tanggalsampai;
//    tujuan='it_slave_2prestasi_detail.php'+"?"+param;  
//    width='800';
//    height='400';
//
//    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//    showDialog1('Detail Prestasi '+karyawan,content,width,height,ev); 
//	
//}
//
//function detailAlokasi(ev,kdvhc,hrgsat)
//{
//    tglAwl=document.getElementById('tglAwal').value;
//    tglAkhr=document.getElementById('tglAkhir').value;
//   param='kodevhc='+kdvhc+'&hrgaSatuan='+hrgsat;
//   param+='&tglAkhir='+tglAkhr+'&tglAwal='+tglAwl;
//   tujuan='vhc_slave_2biayaalokasiperkendaraandetail.php'+"?"+param;  
//   width='800';
//   height='500';
// //alert(param);
//   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   showDialog2('Detail Alokasi '+kdvhc,content,width,height,ev); 
//	
//}
//
//
//
//function detailData(ev,tujuan)
//{
//    width='300';
//   height='100';
//  
//   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   showDialog1('Detail Alokasi',content,width,height,ev); 
//}
//
//function ambilPeriode2(unit)
//{
//	param='unit='+unit;
//	tujuan='sdm_slave_getPeriode.php';
//	post_response_text(tujuan, param, respog);
//	
//		function respog(){
//			if (con.readyState == 4) {
//				if (con.status == 200) {
//					busy_off();
//					if (!isSaveResponse(con.responseText)) {
//						alert('ERROR TRANSACTION,\n' + con.responseText);
//					}
//					else {
//						document.getElementById('periode').innerHTML=con.responseText;
//					}
//				}
//				else {
//					busy_off();
//					error_catch(con.status);
//				}
//			}
//		}	
//	
//}
