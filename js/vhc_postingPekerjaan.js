// JavaScript Document
function displayList()
{
        document.getElementById('txtsearch').value='';
        document.getElementById('tgl_cari2').value='';
        document.getElementById('tgl_cari').value='';
        document.getElementById('kdvhc').value='';
        document.getElementById('statId').value='';
        //document.getElementById('proses').value='insert';
        load_data();
}
function load_data()
{
        param='proses=load_data_header';
        tujuan='vhc_slave_postingPekerjaan.php';
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
                                                        //alert(con.responseText);
                                                        document.getElementById('contain').innerHTML=con.responseText;
                                                        //load_data();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}
function cariTransaksi()
{
        txtTglCr=document.getElementById('tgl_cari2').value;
        txtSearch=document.getElementById('txtsearch').value;
        txtTgl=document.getElementById('tgl_cari').options[document.getElementById('tgl_cari').selectedIndex].value;
        statId=document.getElementById('statId').options[document.getElementById('statId').selectedIndex].value;
        kdvh=document.getElementById('kdvhc').value;
        param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cari_transaksi';
        param+='&statId='+statId+'&txtTglCr='+txtTglCr+'&kdVhc='+kdvh;
        //alert(param);
        tujuan='vhc_slave_postingPekerjaan.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {				
                                        document.getElementById('contain').innerHTML=con.responseText;
                                                //load_new_data();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function cariData(num)
{
                txtTglCr=document.getElementById('tgl_cari2').value;
                txtSearch=document.getElementById('txtsearch').value;
                txtTgl=document.getElementById('tgl_cari').options[document.getElementById('tgl_cari').selectedIndex].value;
                statId=document.getElementById('statId').options[document.getElementById('statId').selectedIndex].value;	
                param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cari_transaksi';
                param+='&statId='+statId+'&txtTglCr='+txtTglCr;
                param+='&page='+num;
                tujuan = 'vhc_slave_postingPekerjaan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}

function cariBast(num)
{
                param='proses=load_data_header';
                param+='&page='+num;
                tujuan = 'vhc_slave_postingPekerjaan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function selectAll()
{
        drt=document.getElementById('chkAll');
    if(drt.checked==true)
    {
            chk=true;
                        document.getElementById('tmblPosting').style.display='block';
                        document.getElementById('btnNextSmua').style.display='none';
    }
        else
            {
                chk=false;
                                document.getElementById('tmblPosting').style.display='none';
                                document.getElementById('btnNextSmua').style.display='block';
            }
    var tbl = document.getElementById("contentIsi");
    var row = tbl.rows.length;
     row=row-2;
    var ard=0;
    for(i=1;i<=row;i++)
    {
        ard++;
        document.getElementById('jmlhBaris').value=ard;
        document.getElementById('checkDt_'+i).checked=chk;
    }
}
function postingData()
{   
    row=document.getElementById('jmlhBaris').value;
    if(row!=20)
    {
        row=row-1;
    }
        strUrl = '';
    for(i=1;i<=row;i++)
    {
        ard=document.getElementById('checkDt_'+i);
        if(ard.checked==true)
            {
                try{
                    if(strUrl != '')
                    {
                            strUrl +='&notransaksi[]='+trim(document.getElementById('notransaksi_'+i).innerHTML)
                                   +'&kdVhc[]='+trim(document.getElementById('kdvhc_'+i).innerHTML)
                                                                   +'&tglData[]='+trim(document.getElementById('tgl_data_'+i).innerHTML);
                    }
                    else
                    {
                           strUrl +='&notransaksi[]='+trim(document.getElementById('notransaksi_'+i).innerHTML)
                                  +'&kdVhc[]='+trim(document.getElementById('kdvhc_'+i).innerHTML)
                                                                  +'&tglData[]='+trim(document.getElementById('tgl_data_'+i).innerHTML);
                    }
                }
                 catch(e){}
            }
                        else
                        {
                                displayList();
                        }
    }
        param='proses=postingDa';
        param+=strUrl;
        tujuan='vhc_slave_postingPekerjaan.php';
                if(confirm("Choosen transaction wil be confirmed, are you sure?"))
                {
                        post_response_text(tujuan, param, respog);	
                }
                else
                {
                        return;
                }
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //document.getElementById('contain').innerHTML=con.responseText;		
                                                displayList();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}

function ByJrkBrt()
{
        //alert("masuk Jarak Berat");
        noTrans=document.getElementById('noTransForm').value;
        param='no_trans='+noTrans+'&proses=postingByBeratJrk';
        tujuan='vhc_slave_postingPekerjaan.php';
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //document.getElementById('contain').innerHTML=con.responseText;
                                                closeDialog();
                                                load_data();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
        if(confirm("Are you sure..?"))
                {
                        post_response_text(tujuan, param, respog);	
                }
                else
                {
                        return;
                }
}
function postData(brsDt)
{
        notran=document.getElementById('notransaksi_'+brsDt).innerHTML;
        kdcv=document.getElementById('kdvhc_'+brsDt).innerHTML;
        trg=document.getElementById('tgl_data_'+brsDt).innerHTML;
        param='notransaksi='+notran+'&kdVhc='+kdcv+'&tglData='+trg;
        param+='&proses=postSat';

        tujuan='vhc_slave_postingPekerjaan.php';
                if(confirm("Anda Yakin Ingin Memposting Yang Terpilih!!"))
                {
                        post_response_text(tujuan, param, respog);	
                }
                else
                {
                        document.getElementById('checkDt_'+brsDt).checked=false;
                        return;
                }
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //document.getElementById('contain').innerHTML=con.responseText;		
                                                displayList();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}
function test(noTrans)
{
        notrans=noTrans;
        param='no_trans='+notrans+'&proses=postData';
        //alert(param);
        tujuan='vhc_slave_postingPekerjaan.php';
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //document.getElementById('contain').innerHTML=con.responseText;
                                                load_data();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
                if(confirm("Choosen transaction wil be confirmed, are you sure?"))
                {
                        post_response_text(tujuan, param, respog);	
                }
                else
                {
                        return;
                }
}