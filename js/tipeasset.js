/**
 * @author antoniuslouis
 */
function simpanTipeAset()
{
        kodetipe=document.getElementById('kodetipe').value;
        namatipe=document.getElementById('namatipe').value;
        namatipe1=document.getElementById('namatipe1').value;
        noakun=document.getElementById('noakun');;
        noakun=noakun.options[noakun.selectedIndex].value;
        noakunak=document.getElementById('noakunak');
        noakunak=noakunak.options[noakunak.selectedIndex].value;        
        tpasset=document.getElementById('tppenyusutan');
        tpasset=tpasset.options[tpasset.selectedIndex].value;        
        met=document.getElementById('method').value;
        if(trim(kodetipe)=='' || trim(namatipe)=='')
        {
                alert('Data inconsistent');
                document.getElementById('kodetipe').focus();
        }
        else
        {
                kodetipe=trim(kodetipe);
                namatipe=trim(namatipe);
                param='kodetipe='+kodetipe+'&namatipe='+namatipe+'&method='+met+'&tppenyusutan='+tpasset;
                param+='&noakun='+noakun+'&noakunak='+noakunak+'&namatipe1='+namatipe1;
                tujuan='sdm_slave_save_tipeasset.php';
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
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}

function fillField(kode,nama,nama1,noakun,noakunak,metodedep)
{
        document.getElementById('kodetipe').value=kode;
        document.getElementById('kodetipe').disabled=true;
        document.getElementById('namatipe').value=nama;
        document.getElementById('namatipe1').value=nama1;
		document.getElementById('tppenyusutan').value=metodedep;
        document.getElementById('method').value='update';
        x=document.getElementById('noakun');
        for(a=0;a<x.length;a++)
        {
                if(x.options[a].value==noakun)
                {
                        x.options[a].selected=true;
                }
        }
        x=document.getElementById('noakunak');
        for(a=0;a<x.length;a++)
        {
                if(x.options[a].value==noakunak)
                {
                        x.options[a].selected=true;
                }
        }        
}

function cancelTipeAsset()
{
        document.getElementById('kodetipe').disabled=false;
        document.getElementById('kodetipe').value='';
        document.getElementById('namatipe').value='';
        document.getElementById('namatipe').value1='';
        document.getElementById('method').value='insert';
        document.getElementById('noakun').options[0].selected=true;		
}
