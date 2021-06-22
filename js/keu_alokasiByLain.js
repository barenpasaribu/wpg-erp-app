/**
 * @author antoniuslouis
 */
function displayForm()
{
	periode=document.getElementById('periode').value;
	tipekaryawan=document.getElementById('tipekaryawan').value;
	if(trim(periode)=='' || trim(tipekaryawan)=='')
	{
		alert('Code is empty');
	}
	else
	{
            param='periode='+periode+'&tipekaryawan='+tipekaryawan;
            tujuan='keu_slave_getkaryawanUBiayaLain.php';
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

function simpanBy(x,periode)
{
    bylistrik   =document.getElementById('bylistrik'+x).value;
    byair       =document.getElementById('byair'+x).value;
    byklinik    =document.getElementById('byklinik'+x).value;
    bysosial    =document.getElementById('bysosial'+x).value;
    perumahan   =document.getElementById('perumahan'+x).value;
    natura      =document.getElementById('natura'+x).value;
    jms         =document.getElementById('jms'+x).value;
    karyawanid  =document.getElementById('karid'+x).innerHTML;
    subbagian   =document.getElementById('subbagian'+x).innerHTML;
    namakaryawan  =document.getElementById('namakaryawan'+x).innerHTML;

         
         param="bylistrik="+bylistrik+"&byair="+byair;
         param+="&byklinik="+byklinik+"&bysosial="+bysosial;
         param+="&perumahan="+perumahan+"&natura="+natura+"&jms="+jms;
         param+="&karyawanid="+karyawanid+'&subbagian='+subbagian;
         param+='&method=save&periode='+periode;
         tujuan='keu_slave_save_byunalocated.php';
         post_response_text(tujuan, param, respog);
         changeItsColor(x,'orange');
     
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
                           if(con.responseText!='deleted'){
                            //memastikan simpanan baris yang bukan nol semua
                            document.getElementById('cell'+x).innerHTML="<img class=dellicon onclick=posting('"+x+"','"+periode+"') id=btn"+x+" src='images/skyblue/posting.png'>";
                           }
                           changeItsColor(x,'#DEDEDE');
                      }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
              }	
	 }        
        
}

function changeItsColor(x,to)
{
    document.getElementById('bylistrik'+x).style.backgroundColor=to;
    document.getElementById('byair'+x).style.backgroundColor=to;
    document.getElementById('byklinik'+x).style.backgroundColor=to;
    document.getElementById('bysosial'+x).style.backgroundColor=to;
    document.getElementById('perumahan'+x).style.backgroundColor=to;
    document.getElementById('natura'+x).style.backgroundColor=to;
    document.getElementById('jms'+x).style.backgroundColor=to;
}                   

function posting(x,periode)
{
         karyawanid  =document.getElementById('karid'+x).innerHTML;
         namakaryawan  =document.getElementById('namakaryawan'+x).innerHTML;
         param='&method=post&periode='+periode+'&karyawanid='+karyawanid+'&namakaryawan='+namakaryawan;
         tujuan='keu_slave_save_byunalocated.php';
         if(confirm('Posting..?')){
             post_response_text(tujuan, param, respog);
             changeItsColor(x,'red');
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
                                document.getElementById('cell'+x).innerHTML="<img  id=btn"+x+" src='images/skyblue/posted.png'>";
                                document.getElementById('save'+x).style.display='none';
                               changeItsColor(x,'#DEDEDE');
                          }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
                  }	
             }      
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}

function dataKeExcel(ev)
{
        periode=document.getElementById('periode').value;
	tipekaryawan=document.getElementById('tipekaryawan').value;
	if(trim(periode)=='' || trim(tipekaryawan)=='')
	{
		alert('Code is empty');
	}
	else
	{
            param='periode='+periode+'&tipekaryawan='+tipekaryawan;		
	}
	param+='&proses=excel';
	tujuan='keu_slave_getkaryawanUBiayaLainExcel.php';
	judul='List Data in Excel';		
	printFile(param,tujuan,judul,ev)	
}
