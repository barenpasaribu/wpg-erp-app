/**
 * @author {antonius.louis@gmail.com}
 * jakarta indonesia
 */

function grafikProduksi(ev)
{
    periode=document.getElementById('periodePsr').options[document.getElementById('periodePsr').selectedIndex].value;
    psr=document.getElementById('psrId').options[document.getElementById('psrId').selectedIndex].value;
    param='periodePsr='+periode+'&psrId='+psr+'&proses=jpgraph';
    tujuan = 'pmn_slave_2hargapasar.php?'+param;
    //document.getElementById('container').innerHTML="<img src='pabrik_slave_grafikProduksi.php?"+param+"'>";
    tujuan='pmn_slave_2hargapasar.php?'+param;
    title=periode+" "+psr;
    width='700';
    height='400';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);
}

function grafikProduksi2(ev)
{
    //$arr2="##psrId2##komodoti##periodePsr2";
    periode=document.getElementById('periodePsr2').options[document.getElementById('periodePsr2').selectedIndex].value;
    psr=document.getElementById('psrId2').options[document.getElementById('psrId2').selectedIndex].value;
    kdti=document.getElementById('komodoti').options[document.getElementById('komodoti').selectedIndex].value;
    param='periodePsr2='+periode+'&psrId2='+psr+'&proses=jpgraph'+'&komodoti='+kdti;
    tujuan = 'pmn_slave_2hargapasar_2.php?'+param;
    //document.getElementById('container').innerHTML="<img src='pabrik_slave_grafikProduksi.php?"+param+"'>";		
    tujuan='pmn_slave_2hargapasar_2.php?'+param;
    title=periode+" "+psr;
    width='700';
    height='400';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);
}

function pilihTab(tipe){
    if(tipe == 'daily'){
        daily();
    }

    if(tipe == 'monthly'){
        montly();
    }

    if(tipe == 'chart'){
        chart();
    }

}

function daily() {
    param='proses=daily';
    tujuan='pmn_slave_2hargapasar';
    post_response_text(tujuan+'.php', param, respon);   
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('trendHarga').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function montly() {
    param='proses=montly';
    tujuan='pmn_slave_2hargapasar';
    post_response_text(tujuan+'.php', param, respon);   
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('trendHarga').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function chart() {
    param='proses=chart';
    tujuan='pmn_slave_2hargapasar';
    post_response_text(tujuan+'.php', param, respon);   
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById("trendHargaChart").style.display = "none";
                    //document.getElementById('trendHarga').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}