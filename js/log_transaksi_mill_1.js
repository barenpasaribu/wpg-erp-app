/* Function showDetail

 * Fungsi untuk pop up form detail

 * I : id table, primary key table

 * P : Ajax menyiapkan keseluruhan halaman detail

 * O : Halaman edit detail shift

 */

function showDetail(num,idStr,event) {

    var IDs = idStr.split('##');

    

    for(i=1;i<IDs.length;i++) {

        tmp = document.getElementById(IDs[i]+"_"+num);

        if(i==1) {

            var param = IDs[i]+"="+tmp.innerHTML;

        } else {

            param += "&"+IDs[i]+"="+tmp.innerHTML;

        }

    }

    

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                    showDialog1('Edit Detail',con.responseText,'800','300',event);

                    var dialog = document.getElementById('dynamic1');

                    dialog.style.top = '137px';

                    dialog.style.left = '30px%';

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

    

    post_response_text('pabrik_slave_5shift.php?proses=showDetail', param, respon);

}



function delRow(id){
 
    var param='id='+id;

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

					location.reload(true);

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }    

    if(confirm("Yakin akan hapus data ?")) {

	    post_response_text('pabrik_crud_mill.php?proses=deleteshift', param, respon);

	}

}


function editRow(sisaakhir,tbsolahafter,persenpotsortasi,kgpotsortasi,tbsmasukafter,totalbuah,tbsmasuk,sisaawal,ratabuahlori,totallori,notrans_tbsolah,kodeorg,tanggal,id,loriolah,loridalamrebusan,lorirestandepanrebusan,lorirestanbelakangrebusan,estimasidiperon,tbsolah){
   

//function editRow(kodeorg,tanggal){
 
    document.getElementById('nomertransaksi').value = notrans_tbsolah;
    document.getElementById('kodeorg').value = kodeorg;
    document.getElementById('tanggal').value = tanggal;
    document.getElementById('sisaawal').value = sisaawal;
    document.getElementById('tbsmasuk').value = tbsmasuk;
    document.getElementById('totalbuah').value = totalbuah;
    document.getElementById('loriolah').value = loriolah;	
    document.getElementById('ratabuahperlori').value = ratabuahlori;	
    document.getElementById('kgpotsortasi').value = kgpotsortasi;
    document.getElementById('persenpotsortasi').value = persenpotsortasi;
    document.getElementById('tbsmasukafter').value = tbsmasukafter;
    document.getElementById('tbsolahafter').value = tbsolahafter;
	document.getElementById('loridalamrebusan').value = loridalamrebusan;
	document.getElementById('lorirestandepanrebusan').value = lorirestandepanrebusan;
	document.getElementById('lorirestanbelakangrebusan').value = lorirestanbelakangrebusan;
    document.getElementById('estimasidiperon').value = estimasidiperon;
    document.getElementById('total2').value = totallori;
    document.getElementById('tbsolah').value = tbsolah;    
    document.getElementById('id').value = id;
    document.getElementById('sisaakhir').value = sisaakhir;
    
}


function simpan(){

    id = document.getElementById('id').value;
    nomertransaksi = document.getElementById('nomertransaksi').value;
    kodeorg = document.getElementById('kodeorg').value;
    tanggal = document.getElementById('tanggal').value;
    loriolah = document.getElementById('loriolah').value;
    loridalamrebusan = document.getElementById('loridalamrebusan').value;
    lorirestandepanrebusan = document.getElementById('lorirestandepanrebusan').value;	
    lorirestanbelakangrebusan = document.getElementById('lorirestanbelakangrebusan').value;
    estimasidiperon = document.getElementById('estimasidiperon').value;
    totallori = document.getElementById('total2').value;
    rataratabuahperlori = document.getElementById('ratabuahperlori').value;
    sisaawal = document.getElementById('sisaawal').value;
    tbsmasuk = document.getElementById('tbsmasuk').value;
    totalbuah = document.getElementById('totalbuah').value;
    tbsmasukafter = document.getElementById('tbsmasukafter').value;
    kgpotsortasi = document.getElementById('kgpotsortasi').value;
    persenpotsortasi = document.getElementById('persenpotsortasi').value;
    tbsolah = document.getElementById('tbsolah').value;
    tbsolahafter = document.getElementById('tbsolahafter').value;
    sisaakhir = document.getElementById('sisaakhir').value;
   

    var param = 'id='+id+'&nomertransaksi='+nomertransaksi+
    '&tbsolah='+tbsolah+'&kodeorg='+kodeorg+
    '&tanggal='+tanggal+'&loriolah='+loriolah+'&loridalamrebusan='+
    loridalamrebusan+'&lorirestandepanrebusan='+lorirestandepanrebusan+
    '&lorirestanbelakangrebusan='+lorirestanbelakangrebusan+
    '&estimasidiperon='+estimasidiperon+
    '&rataratabuahperlori='+rataratabuahperlori+
    '&sisaawal='+sisaawal+
    '&tbsmasuk='+tbsmasuk+
    '&totalbuah='+totalbuah+
    '&tbsmasukafter='+tbsmasukafter+
    '&kgpotsortasi='+kgpotsortasi+
    '&tbsolah='+tbsolah+
    '&tbsolahafter='+tbsolahafter+
    '&persenpotsortasi='+persenpotsortasi+
    '&totallori='+totallori+
    '&sisaakhir='+sisaakhir;


    if(id==""){

        function respon() {

            if (con.readyState == 4) {
    
                if (con.status == 200) {
    
                    busy_off();
    
                    if (!isSaveResponse(con.responseText)) {
    
                        alert('ERROR TRANSACTION,\n' + con.responseText);
    
                    } else {
    
                        // Success Response
    
                        var res = document.getElementById('kodeorg');
    
                        res.value = con.responseText;
    
    
    
                        
                        alert(con.responseText);
    
                        location.reload(true);			
    
                    }
    
                } else {
    
                    busy_off();
    
                    error_catch(con.status);
    
                }
    
            }
    
        }    
    
    
        post_response_text('pabrik_crud_mill.php?proses=addshift', param,respon);	 


    }

    else {
        
        
        function respon() {

            if (con.readyState == 4) {
    
                if (con.status == 200) {
    
                    busy_off();
    
                    if (!isSaveResponse(con.responseText)) {
    
                        alert('ERROR TRANSACTION,\n' + con.responseText);
    
                    } else {
    
                        // Success Response
    
                        var res = document.getElementById('kodeorg');
    
                        res.value = con.responseText;
    
                        alert(con.responseText);
    
                        location.reload(true);			
    
                    }
    
                } else {
    
                    busy_off();
    
                    error_catch(con.status);
    
                }
    
            }
    
        }    
    
    
        post_response_text('pabrik_crud_mill.php?proses=edit', param,respon);	

    }

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                    var res = document.getElementById('kodeorg');

                    res.value = con.responseText;



                    
					alert(con.responseText);

					location.reload(true);			

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }    


}

function getPotsortasi()

{
        kodeorg=document.getElementById('kodeorg').value;
        tanggal=document.getElementById('tanggal').value;
        param='kodeorg='+kodeorg+'&tanggal='+tanggal;
      
        post_response_text('setup_mill_potongansortasi.php', param, respon);

        function respon(){

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                                               // ar=con.responseText.split("###");
                                               var res = document.getElementById('kgpotsortasi');
                                                res.value = con.responseText;
                                              //  document.getElementById('vhc_code').innerHTML=ar[0];

                     }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }


}


function getSisaawal()

{

        kodeorg=document.getElementById('kodeorg').value;
        tanggal=document.getElementById('tanggal').value;
        param='kodeorg='+kodeorg+'&tanggal='+tanggal;
      
        post_response_text('setup_mill_generate_sisaawal.php', param, respon);

        function respon(){

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                                               // ar=con.responseText.split("###");
                                               var res = document.getElementById('sisaawal');
                                                res.value = con.responseText;
                                              //  document.getElementById('vhc_code').innerHTML=ar[0];

                     }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

      

    }
   // getPotsortasi();

}

function getNotrans(notran,kdJenis)

{



        if((notran!=0)&&(kdJenis!=0))

        {

                kdOrg=document.getElementById('kodeorg').value;

                kdjenis=kdJenis;

                notrans=notran;

                param='proses=generate_no'+'&kodeorg='+kdOrg+'&kdjenis='+kdjenis+'&notrans='+notrans;

        }

        else

        {
                kdOrg=document.getElementById('kodeorg').value;

                param='proses=generate_no'+'&kodeorg='+kdOrg;

        }
      
      
        post_response_text('setup_mill_generate_transaction.php', param, respon);

        function respon(){

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                                               // ar=con.responseText.split("###");
                                               var res = document.getElementById('nomertransaksi');
                                                res.value = con.responseText;
                                              //  document.getElementById('vhc_code').innerHTML=ar[0];

                     }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }


}

function ambilisiform(){
    
    var kodeorg = new Number(document.getElementById("kodeorg").value);
    var tanggal = new Number(document.getElementById("tanggal").value);
    var sisaawal = new Number(document.getElementById("sisaawal").value);
    var tbsmasuk = new Number(document.getElementById("tbsmasuk").value);
    var total = Math.round(sisaawal+tbsmasuk);
    var res = document.getElementById('totalbuah').value =total;
                    
    var loriolah = new Number(document.getElementById("loriolah").value);
    var loridalamrebusan = new Number(document.getElementById("loridalamrebusan").value);
    var lorirestandepanrebusan = new Number(document.getElementById("lorirestandepanrebusan").value);
    var lorirestanbelakangrebusan = new Number(document.getElementById("lorirestanbelakangrebusan").value);
    var estimasidiperon = new Number(document.getElementById("estimasidiperon").value);
    var total2 = Math.round(loriolah + loridalamrebusan + lorirestandepanrebusan + lorirestanbelakangrebusan + estimasidiperon);
    var res = document.getElementById('total2').value =total2;
    
    var totallori=loriolah + loridalamrebusan + lorirestandepanrebusan + lorirestanbelakangrebusan + estimasidiperon;
    var rataratabuahperlori = Math.round(total/totallori);
    var res = document.getElementById('ratabuahperlori').value =rataratabuahperlori;
    var kgpotsortasi = new Number(document.getElementById("kgpotsortasi").value);
    var tbsmasukafter = Math.round(tbsmasuk - kgpotsortasi);
    // kg TBS Masuk After
    var res = document.getElementById('tbsmasukafter').value =tbsmasukafter;
    var potonganpersen = (kgpotsortasi/tbsmasuk*100);
    var potonganpersen2=parseFloat(Math.round(potonganpersen * 100) / 100).toFixed(2);
    var res = document.getElementById('persenpotsortasi').value =potonganpersen2;
    var tbsolah = loriolah*rataratabuahperlori;

    var tbsolahafter=(tbsolah-(tbsmasuk-tbsmasukafter));

    var res = document.getElementById('tbsolahafter').value =tbsolahafter;

    var res = document.getElementById('tbsolah').value =tbsolah;
    var sisaakhir = total-tbsolah;
    var res = document.getElementById('sisaakhir').value =sisaakhir;

    
/*
    noakun=document.getElementById('kodeorg');

    noakun=noakun.options[noakun.selectedIndex].value;

    param="ngapain=ambilkegiatan";

    param+='&proses=gettbs'+'&tanggal='+tanggal;

    param+="&noakun="+noakun;
*/
    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    var kodeorg = new Number(document.getElementById("kodeorg").value);
                    var tanggal = new Number(document.getElementById("tanggal").value);
                    var sisaawal = new Number(document.getElementById("sisaawal").value);
                    var tbsmasuk = new Number(document.getElementById("tbsmasuk").value);
                    var total = Math.round(sisaawal+tbsmasuk);
                    var res = document.getElementById('totalbuah').value =total;
                                    
                    var loriolah = new Number(document.getElementById("loriolah").value);
                    var loridalamrebusan = new Number(document.getElementById("loridalamrebusan").value);
                    var lorirestandepanrebusan = new Number(document.getElementById("lorirestandepanrebusan").value);
                    var lorirestanbelakangrebusan = new Number(document.getElementById("lorirestanbelakangrebusan").value);
                    var estimasidiperon = new Number(document.getElementById("estimasidiperon").value);
                    var total2 = Math.round(loriolah + loridalamrebusan + lorirestandepanrebusan + lorirestanbelakangrebusan + estimasidiperon);
                    var res = document.getElementById('total2').value =total2;
                    
                    var totallori=loriolah + loridalamrebusan + lorirestandepanrebusan + lorirestanbelakangrebusan + estimasidiperon;
                    var rataratabuahperlori = Math.round(total/totallori);
                    var res = document.getElementById('ratabuahperlori').value =rataratabuahperlori;
                    var kgpotsortasi = new Number(document.getElementById("kgpotsortasi").value);
                    var tbsmasukafter = Math.round(tbsmasuk - kgpotsortasi);
                    // kg TBS Masuk After
                    var res = document.getElementById('tbsmasukafter').value =tbsmasukafter;
                    var potonganpersen = (kgpotsortasi/tbsmasuk*100);
                    var potonganpersen2=parseFloat(Math.round(potonganpersen * 100) / 100).toFixed(2);
                    var res = document.getElementById('persenpotsortasi').value =potonganpersen2;
                    var tbsolah = loriolah*rataratabuahperlori;
                
                    var tbsolahafter=(tbsolah-(tbsmasuk-tbsmasukafter));
                
                    var res = document.getElementById('tbsolahafter').value =tbsolahafter;
                
                    var res = document.getElementById('tbsolah').value =tbsolah;
                    var sisaakhir = total-tbsolah;
                    var res = document.getElementById('sisaakhir').value =sisaakhir;
                

                    res.value = con.responseText;

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }    


}