/**
 * @author antoniuslouis
 */
function getKPIdata()
{
     tahun=document.getElementById('tahun').value;

        if(trim(tahun)=='')
        {
                alert('Please specify year');
                document.getElementById('tahun').focus();
        }
        else
        {
            param='tahun='+tahun;
            tujuan='sdm_slave_kpiData.php';
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
function lihatDetail(unit,jenisData,periode,ev)
{
    param='unit='+unit+'&jenisdata='+jenisData+'&periode='+periode+'&proses=getDetail';
    tujuan='sdm_slave_kpiData.php';
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
                                                        content="<div style=\"height:380px;width:490px;overflow:scroll;\">"+con.responseText+"</div>";
                                                         title='Detail posting data '+unit+' perioe '+periode+' Jenis Data '+jenisData;
                                                         width='500';
                                                         height='400';
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