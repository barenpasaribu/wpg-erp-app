/*@uth:nangkoel@gmail.com
 * 
 */
//gform        =document.getElementById('GRAPHFORM');
//ginfo          =document.getElementById('GRAPHINFO');
//gresult       =document.getElementById('GRAPHRESULT');

function loadGraphForm()
{
    glist            =document.getElementById('GRAPHOPTLIST');
    gform        =document.getElementById('GRAPHFORM');
    document.getElementById('GRAPHRESULT').innerHTML='';
    dest=glist.options[glist.selectedIndex].value;
    if(dest=='')
     {}
     else
    {     
        param='id='+dest;
        tujuan='bi_slave_getForm.php';
        post_response_text(tujuan, param, respog);
    }
    function respog()
    {
              if(con.readyState==4)
              {
                            if (con.status == 200) 
                            {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else 
                                    {
                                            gform.innerHTML=con.responseText;
                                    }
                            }
                            else 
                            {
                                    busy_off();
                                    error_catch(con.status);
                            }
              }	
     }  
}

function get001(dest)
{
    tahun=document.getElementById('tahun');
    tahun=tahun.options[tahun.selectedIndex].value;
    pks=document.getElementById('pks'); 
    pks=pks.options[pks.selectedIndex].value;
    param=dest+'.php?tahun='+tahun+'&pks='+pks+'&jenis=global';
    if(tahun=='')
        alert('Tahun belum diisi');
    else
    getGraph(param);
}

function get002(dest)
{
    awal=document.getElementById('dari');
    awal=awal.options[awal.selectedIndex].value;
    sampai=document.getElementById('sampai');
    sampai=sampai.options[sampai.selectedIndex].value;   
    pks=document.getElementById('pks'); 
    pks=pks.options[pks.selectedIndex].value; 
    param=dest+'.php?awal='+awal+'&pks='+pks+'&sampai='+sampai+'&jenis=global';
    if(awal =='' || sampai =='' || sampai<=awal)
        alert('Periode salah');
    else
    getGraph(param);
}

function get003(dest)
{
    tahun=document.getElementById('tahun');
    tahun=tahun.options[tahun.selectedIndex].value;
    tahun1=document.getElementById('tahun1');
    tahun1=tahun1.options[tahun1.selectedIndex].value;
    pks=document.getElementById('pks'); 
    pks=pks.options[pks.selectedIndex].value;
    param=dest+'.php?tahun='+tahun+'&tahun1='+tahun1+'&pks='+pks+'&jenis=global';
    if(tahun=='' || tahun>tahun1)
        alert('Periode tahun salah');
    else
    getGraph(param);
}
function get005(dest,passParam)
{
    var passP = passParam.split('##');
    var param2 = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param2 += passP[i]+"="+getValue(passP[i]);
        } else {
            param2 += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param=dest+'.php?'+param2;
    getGraph(param);
}
function getGraph(dest)
{
   gresult       =document.getElementById('GRAPHRESULT');
    gresult.innerHTML="<iframe width=1000px height=560px frameborder=no src="+param+"></iframe>";
}