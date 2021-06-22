/**
 * @author antoniuslouis
 */
function simpanStdjabatan()
{
	kodejabatan=document.getElementById('kodejabatan');
        kodejabatan=kodejabatan.options[kodejabatan.selectedIndex].value;
        
	lokasi=document.getElementById('lokasi');
        lokasi=lokasi.options[lokasi.selectedIndex].value;
        
        tjjabatan   =document.getElementById('tjjabatan').value;
        tjkota      =document.getElementById('tjkota').value;
        tjtransport =document.getElementById('tjtransport').value;
        tjmakan     =document.getElementById('tjmakan').value;
        tjsdaerah   =document.getElementById('tjsdaerah').value;
        tjmahal     =document.getElementById('tjmahal').value;
        tjpembantu  =document.getElementById('tjpembantu').value;
        
        tjjabatan=tjjabatan==''?0:tjjabatan;
        tjkota=tjkota==''?0:tjkota;
        tjtransport=tjtransport==''?0:tjtransport;
        tjmakan=tjmakan==''?0:tjmakan;
        tjsdaerah=tjsdaerah==''?0:tjsdaerah;
        tjmahal=tjmahal==''?0:tjmahal;
        tjpembantu=tjpembantu==''?0:tjpembantu;

	param='kodejabatan='+kodejabatan+'&lokasi='+lokasi+'&tjjabatan='+tjjabatan;
        param+='&tjkota='+tjkota+'&tjtransport='+tjtransport+'&tjmakan='+tjmakan;
        param+='&tjsdaerah='+tjsdaerah+'&tjmahal='+tjmahal+'&tjpembantu='+tjpembantu;
	tujuan='sdm_slave_save_stdTunjangan.php';
        if(confirm('Are you sure..?')){
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
                            document.getElementById('container').innerHTML=con.responseText;
                            cancelStdTun();
                    }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
              }	
	 }
		
}

function fillField(kodejabatan,lokasi,tjjabatan,tjkota,tjtransport,tjmakan,tjsdaerah,tjmahal,tjpembantu)
{
       document.getElementById('tjjabatan').value=tjjabatan;
       document.getElementById('tjkota').value=tjkota;
       document.getElementById('tjtransport').value=tjtransport;
       document.getElementById('tjmakan').value=tjmakan;
       document.getElementById('tjsdaerah').value=tjsdaerah;
       document.getElementById('tjmahal').value=tjmahal;
       document.getElementById('tjpembantu').value=tjpembantu;
    jk=document.getElementById('kodejabatan');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==kodejabatan)
		{
			jk.options[x].selected=true;
		}
	}
    jk=document.getElementById('lokasi');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==lokasi)
		{
			jk.options[x].selected=true;
		}
	}           
       document.getElementById('kodejabatan').disabled=true;        
       document.getElementById('lokasi').disabled=true;        
}

function cancelStdTun()
{
       document.getElementById('tjjabatan').value=0;
       document.getElementById('tjkota').value=0;
       document.getElementById('tjtransport').value=0;
       document.getElementById('tjmakan').value=0;
       document.getElementById('tjsdaerah').value=0;
       document.getElementById('tjmahal').value=0;
       document.getElementById('tjpembantu').value=0;		
       document.getElementById('kodejabatan').disabled=false;        
       document.getElementById('lokasi').disabled=false;
}
