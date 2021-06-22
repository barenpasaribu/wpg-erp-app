/**

 */

function simpan()
{
    tahunbudget =document.getElementById('tahunbudget').value;
    kodeorg	=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
    bagian	=document.getElementById('bagian').options[document.getElementById('bagian').selectedIndex].value;
    golongan 	=document.getElementById('golongan').options[document.getElementById('golongan').selectedIndex].value;
    jabatan 	=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    mingaji	=document.getElementById('mingaji').value;	
    maxgaji	=document.getElementById('maxgaji').value;	
    tanggalmasuk=document.getElementById('tanggalmasuk').value;	
    minumur	=document.getElementById('minumur').value;	
    maxumur	=document.getElementById('maxumur').value;	
    jeniskelamin=document.getElementById('jeniskelamin').options[document.getElementById('jeniskelamin').selectedIndex].value;
    pendidikan	=document.getElementById('pendidikan').options[document.getElementById('pendidikan').selectedIndex].value;
    pengalaman	=document.getElementById('pengalaman').value;	
    poh         =document.getElementById('poh').value;	
    jumlah	=document.getElementById('jumlah').value;	
    kunci	=document.getElementById('kunci').value;	
    if(
        tahunbudget=='' ||  kodeorg==''  || bagian=='' || golongan=='' || jabatan=='' || mingaji=='' || maxgaji=='' || tanggalmasuk=='' ||
        minumur=='' ||  maxumur==''  || jeniskelamin=='' || pendidikan=='' || pengalaman=='' || poh=='' || jumlah==''
    )
    {
        alert('Field harus diisi.');
    }
    else
    {
        if(kunci==''){
            param='kamar=save';
        }else{
            param='kamar=edit&kunci='+kunci;
        }
        param+='&tahunbudget='+tahunbudget+'&kodeorg='+kodeorg;
        param+='&bagian='+bagian+'&golongan='+golongan;
        param+='&jabatan='+jabatan+'&mingaji='+mingaji;
        param+='&maxgaji='+maxgaji+'&tanggalmasuk='+tanggalmasuk;
        param+='&minumur='+minumur+'&maxumur='+maxumur;
        param+='&jeniskelamin='+jeniskelamin+'&pendidikan='+pendidikan;
        param+='&pengalaman='+pengalaman+'&poh='+poh;
        param+='&jumlah='+jumlah;
        tujuan='sdm_slave_5mpp.php';
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
                }else {
            //							document.getElementById('container').innerHTML=con.responseText;
                    bersihkanForm();
//                    document.getElementById('kunci').value=''; ada di bersihkanForm;
                    updateTahun();
                    alert('Done.');
                    displayList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    } 		
}

function updateTahun()
{
    listtahunz = document.getElementById('pilihantahun').value;
    param='kamar=tahun';
    tujuan='sdm_slave_5mpp.php';
    post_response_text(tujuan, param, respog);
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('listtahun').innerHTML=con.responseText;
                               //bersihkanForm();
                               //alert('Done.');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }   
    document.getElementById('listtahun').value=listtahunz;     
}

function displayList()
{
    listtahun 	=document.getElementById('listtahun').options[document.getElementById('listtahun').selectedIndex].value;
    document.getElementById('pilihantahun').value=listtahun;
    param='kamar=list&listtahun='+listtahun;
    tujuan='sdm_slave_5mpp.php';
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
                    document.getElementById('container').innerHTML=con.responseText;
                   //bersihkanForm();
                   //alert('Done.');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }     
} 

function bersihkanForm()
{
    document.getElementById('kodeorg').value='';
    document.getElementById('bagian').value='';
    document.getElementById('golongan').value='';
    document.getElementById('jabatan').value='';
    document.getElementById('mingaji').value='';
    document.getElementById('maxgaji').value='';
    document.getElementById('tanggalmasuk').value='';
    document.getElementById('minumur').value='';
    document.getElementById('maxumur').value='';
    document.getElementById('jeniskelamin').value='';
    document.getElementById('pendidikan').value='';
    document.getElementById('pengalaman').value='';
    document.getElementById('poh').value='';
    document.getElementById('jumlah').value='';
    document.getElementById('kunci').value='';
}

function batal()
{
    document.getElementById('tahunbudget').value='';
    bersihkanForm();
    document.getElementById('kodetraining').disabled=false;
}

function del(kunci)
{
    param='kamar=delete&kunci='+kunci;
    if (confirm('Delete ..?')) {
        tujuan='sdm_slave_5mpp.php';
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
                } else {
//							document.getElementById('container').innerHTML=con.responseText;
                    bersihkanForm();
                    updateTahun();
                    alert('Done.');
                    displayList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }     
}

function desctraining(kode,ev)
{
    param='kamar=desc&kodetraining='+kode;
    tujuan = 'pabrik_slave_5rencanatraining.php?'+param;	
    //display window
    title='Desc '+kode;
    width='400';
    height='200';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);
}

function edit(tahunbudget,kodeorg,bagian,golongan,jabatan,mingaji,maxgaji,tanggalmasuk,minumur,maxumur,jeniskelamin,pendidikan,pengalaman,poh,jumlah,kunci)
{
    document.getElementById('tahunbudget').value=tahunbudget;
    document.getElementById('kodeorg').value=kodeorg;
    document.getElementById('bagian').value=bagian;
    document.getElementById('golongan').value=golongan;
    document.getElementById('jabatan').value=jabatan;
    document.getElementById('mingaji').value=mingaji;
    document.getElementById('maxgaji').value=maxgaji;
    document.getElementById('tanggalmasuk').value=tanggalmasuk;
    document.getElementById('minumur').value=minumur;
    document.getElementById('maxumur').value=maxumur;
    document.getElementById('jeniskelamin').value=jeniskelamin;
    document.getElementById('pendidikan').value=pendidikan;
    document.getElementById('pengalaman').value=pengalaman;
    document.getElementById('poh').value=poh;
    document.getElementById('jumlah').value=jumlah;
    document.getElementById('kunci').value=kunci;

}


