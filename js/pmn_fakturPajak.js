function timbangan()
{
    customer=document.getElementById('customer').options[document.getElementById('customer').selectedIndex].value;
    param='customer='+customer+'&proses=kodetimb'; 
//    alert(param);
    tujuan='pmn_slave_fakturPajak.php';
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
                    document.getElementById('kodetimbangan').value=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}
function loadkontrak()
{
    komoditi=document.getElementById('komoditi').options[document.getElementById('komoditi').selectedIndex].value;
    param='komoditi='+komoditi+'&proses=loadkontrak'; 
    tujuan='pmn_slave_fakturPajak.php';
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
                    document.getElementById('kontrak').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}
function loadcurr()
{
    kontrak=document.getElementById('kontrak').options[document.getElementById('kontrak').selectedIndex].value;
    param='kontrak='+kontrak+'&proses=loadcurr'; 
    tujuan='pmn_slave_fakturPajak.php';
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
                    document.getElementById('curr').value=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}
function loadfaktur()
{
    kodept=document.getElementById('kodept').options[document.getElementById('kodept').selectedIndex].value;
    tgl=document.getElementById('tgl').value;
    jenispajak=document.getElementById('jenispajak').options[document.getElementById('jenispajak').selectedIndex].value;
    param='kodept='+kodept+'&tgl='+tgl+'&jenispajak='+jenispajak+'&proses=loadfaktur'; 
//    alert(param);
    tujuan='pmn_slave_fakturPajak.php';
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
                    document.getElementById('nofaktur').value=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}
function loadvol()
{
    kodept=document.getElementById('kodept').options[document.getElementById('kodept').selectedIndex].value;
    curr=document.getElementById('curr').value;
    valas=document.getElementById('valas').value;
    kurs=document.getElementById('kurs').value;
    komoditi=document.getElementById('komoditi').options[document.getElementById('komoditi').selectedIndex].value;
    kontrak=document.getElementById('kontrak').options[document.getElementById('kontrak').selectedIndex].value;
    timbangan=document.getElementById('timbangan').options[document.getElementById('timbangan').selectedIndex].value;
    biaya=document.getElementById('biaya').options[document.getElementById('biaya').selectedIndex].value;
    dari=document.getElementById('dari').value;
    sd=document.getElementById('sd').value;
    customer=document.getElementById('customer').value;
    kodetimbangan=document.getElementById('kodetimbangan').value;
    
    param='kodept='+kodept+'&timbangan='+timbangan+'&biaya='+biaya+'&dari='+dari+'&sd='+sd+'&curr='+curr+'&valas='+valas;
    param+='&kurs='+kurs+'&komoditi='+komoditi+'&kontrak='+kontrak+'&customer='+customer+'&kodetimbangan='+kodetimbangan;
    param+='&proses=loadvol'; 
//    alert(param);
    tujuan='pmn_slave_fakturPajak.php';
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
                    pisah=con.responseText.split('###');
                    document.getElementById('vol').value=pisah[0];
                    document.getElementById('jml').value=pisah[1];
                    document.getElementById('potum').value=pisah[2];
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}
function fungsippn()
{
    jenispajak=document.getElementById('jenispajak').options[document.getElementById('jenispajak').selectedIndex].value;
    jml=document.getElementById('jml').value;
    jml=remove_comma_var(jml);
    potharga=document.getElementById('potharga').value;
    potharga=remove_comma_var(potharga);
    potum=document.getElementById('potum').value;
    potum=remove_comma_var(potum);
    persenppn=document.getElementById('persenppn').value;
    param='jenispajak='+jenispajak+'&jml='+jml+'&potharga='+potharga+'&potum='+potum+'&persenppn='+persenppn+'&proses=ppn'; 
//    alert(param);
    tujuan='pmn_slave_fakturPajak.php';
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
                    pisah=con.responseText.split('###');
                    document.getElementById('dasarpajak').value=pisah[0];
                    document.getElementById('ppn').value=pisah[1];
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}
function saveForm()
{
    kodept=document.getElementById('kodept').value;
    customer=document.getElementById('customer').value;
    tgl=document.getElementById('tgl').value;
    komoditi=document.getElementById('komoditi').options[document.getElementById('komoditi').selectedIndex].value;
    kontrak=document.getElementById('kontrak').options[document.getElementById('kontrak').selectedIndex].value;
    curr=document.getElementById('curr').value;
    kurs=document.getElementById('kurs').value;
    jenispajak=document.getElementById('jenispajak').options[document.getElementById('jenispajak').selectedIndex].value;
    nofaktur=document.getElementById('nofaktur').value;
    timbangan=document.getElementById('timbangan').options[document.getElementById('timbangan').selectedIndex].value;
    biaya=document.getElementById('biaya').options[document.getElementById('biaya').selectedIndex].value;
    dari=document.getElementById('dari').value;
    sd=document.getElementById('sd').value;
    valas=document.getElementById('valas').value;
    vol=document.getElementById('vol').value;
    jml=document.getElementById('jml').value;
    jml=remove_comma_var(jml);
    potharga=document.getElementById('potharga').value;
    potharga=remove_comma_var(potharga);
    potum=document.getElementById('potum').value;
    potum=remove_comma_var(potum);
    dasarpajak=document.getElementById('dasarpajak').value;
    dasarpajak=remove_comma_var(dasarpajak);
    persenppn=document.getElementById('persenppn').value;
    persenppn=remove_comma_var(persenppn);
    ppn=document.getElementById('ppn').value;
    ppn=remove_comma_var(ppn);
    ttd=document.getElementById('ttd').options[document.getElementById('ttd').selectedIndex].value;
    kodetimbangan=remove_comma_var(kodetimbangan);
    
    param='kodept='+kodept+'&customer='+customer+'&tgl='+tgl+'&komoditi='+komoditi+'&kontrak='+kontrak+'&curr='+curr; 
    param+='&kurs='+kurs+'&jenispajak='+jenispajak+'&nofaktur='+nofaktur+'&timbangan='+timbangan+'&biaya='+biaya; 
    param+='&dari='+dari+'&sd='+sd+'&valas='+valas+'&vol='+vol+'&jml='+jml+'&kodetimbangan='+kodetimbangan; 
    param+='&potharga='+potharga+'&potum='+potum+'&dasarpajak='+dasarpajak+'&persenppn='+persenppn+'&ppn='+ppn+'&ttd='+ttd+'&proses=insert'; 
//    alert(param);
    tujuan='pmn_slave_fakturPajak.php';
    
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
                    alert('Done.');
                    loaddata();
                    document.getElementById('kodept').value='';
                    document.getElementById('customer').value='';
                    document.getElementById('tgl').value='';
                    document.getElementById('komoditi').value='';
                    document.getElementById('kontrak').value='';
                    document.getElementById('curr').value='';
                    document.getElementById('jenispajak').value='';
                    document.getElementById('nofaktur').value='';
                    document.getElementById('timbangan').value='';
                    document.getElementById('biaya').value='';
                    document.getElementById('dari').value='';
                    document.getElementById('sd').value='';
                    document.getElementById('vol').value='';
                    document.getElementById('jml').value='';
                    document.getElementById('potharga').value='';
                    document.getElementById('potum').value='';
                    document.getElementById('dasarpajak').value='';
                    document.getElementById('ppn').value='';
                    document.getElementById('ttd').value='';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }     	
}
function cancelForm()
{
    document.getElementById('kodept').value='';
    document.getElementById('customer').value='';
    document.getElementById('tgl').value='';
    document.getElementById('komoditi').value='';
    document.getElementById('kontrak').value='';
    document.getElementById('curr').value='';
    document.getElementById('jenispajak').value='';
    document.getElementById('nofaktur').value='';
    document.getElementById('timbangan').value='';
    document.getElementById('biaya').value='';
    document.getElementById('dari').value='';
    document.getElementById('sd').value='';
    document.getElementById('vol').value='';
    document.getElementById('jml').value='';
    document.getElementById('potharga').value='';
    document.getElementById('potum').value='';
    document.getElementById('dasarpajak').value='';
    document.getElementById('ppn').value='';
    document.getElementById('ttd').value='';
    
}
function loaddata()
{
    param='proses=loaddata';
    tujuan='pmn_slave_fakturPajak.php';
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('isi').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
   post_response_text(tujuan, param, respon);
}
function daftarfaktur()
{
    nokontrak=document.getElementById('nokontrak').options[document.getElementById('nokontrak').selectedIndex].value;
    bulan=document.getElementById('bulan').options[document.getElementById('bulan').selectedIndex].value;
    rekanan=document.getElementById('rekanan').options[document.getElementById('rekanan').selectedIndex].value;
    param='nokontrak='+nokontrak+'&bulan='+bulan+'&rekanan='+rekanan+'&proses=daftarfaktur'; 
    tujuan='pmn_slave_fakturPajak.php';
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
                    document.getElementById('isi').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}
function posting(no)
{
    nofaktur=document.getElementById('nofaktur_'+no).innerHTML;
    param='proses=posting'+'&nofaktur='+nofaktur;
    tujuan='pmn_slave_fakturPajak.php';
//    alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
		    alert('Done');
                    javascript:location.reload(true);
                   // defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    if(confirm("Posting, are you sure..?"))
        post_response_text(tujuan, param, respon);
}
function deletefaktur(no)
{
    nofaktur=document.getElementById('nofaktur_'+no).innerHTML;
    param='proses=delete'+'&nofaktur='+nofaktur;
    tujuan='pmn_slave_fakturPajak.php';
//    alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
		    alert('Deleted');
                    javascript:location.reload(true);
                   // defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    if(confirm("Delete, are you sure..?"))
        post_response_text(tujuan, param, respon);
}
function printPDF(ev,no) {
    // Prep Param
    nofaktur=document.getElementById('nofaktur_'+no).innerHTML;
    param='proses=pdf'+'&nofaktur='+nofaktur;
    tujuan='pmn_slave_fakturPajak.php';
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='pmn_slave_fakturPajak.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
    
}
function pages(num)
{
    param='proses=loaddata';
    param+='&page='+num;
                
    tujuan = 'pmn_slave_fakturPajak.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('isi').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}