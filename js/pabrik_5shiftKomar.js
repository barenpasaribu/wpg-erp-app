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



function delRow(kodeorg, shift){

    var param = "kodeorg="+kodeorg+"&shift="+shift;

    

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

	    post_response_text('pabrik_slave_5shift.php?proses=deleteshift', param, respon);

	}

}



function addData(){

    
	loriolah = document.getElementById('loriolah').value;

	loridalamrebusan = document.getElementById('loridalamrebusan').value;

    lorirestandepanrebusan = document.getElementById('lorirestandepanrebusan').value;	
    
    lorirestanbelakangrebusan = document.getElementById('lorirestanbelakangrebusan').value;

	var param = 'loriolah='+loriolah+'&loridalamrebusan='+loridalamrebusan+'&lorirestandepanrebusan='+lorirestandepanrebusan+'&lorirestanbelakangrebusan='+lorirestanbelakangrebusan;

    post_response_text('pabrik_slave_5shiftKomar.php?proses=addshift', param);	

}

function ambilkegiatan(){

    noakun=document.getElementById('kodeorg');

    noakun=noakun.options[noakun.selectedIndex].value;

    param="ngapain=ambilkegiatan";

    param+="&noakun="+noakun;

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                    var res = document.getElementById('loriolah');

                    res.value = con.responseText;

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }    

    post_response_text('setup_slave_kegiatan_kegiatanKomar.php', param, respon);

}