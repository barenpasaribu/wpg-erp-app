// JavaScript Document
function cariBast(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'kebun_slave_5premipanen.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//displayList();
						
						document.getElementById('container').innerHTML=con.responseText;
						//loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

function loadData()
{
    param='method=loadData';
    tujuan='kebun_slave_5premipanen';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function simpan() {
    id=document.getElementById('dataid').value;
    bulanawal_ =document.getElementById('bulanawal');
    bulanawal =bulanawal_.options[bulanawal_.selectedIndex].value;

    bulanakhir_ =document.getElementById('bulanakhir');
    bulanakhir =bulanakhir_.options[bulanakhir_.selectedIndex].value;

    //kodeorg = document.getElementById('kodeorg').value;
    kodeorg = document.getElementById('kodeorg');
    kodeorg = kodeorg.options[kodeorg.selectedIndex].value;
    hasil = document.getElementById('hasil').value;
    tahuntanam = document.getElementById('tahuntanam').value;
    lebihbasis=document.getElementById('lebihbasis').value;
    rupiah=document.getElementById('rupiah').value;
    premirajin = document.getElementById('premirajin').value;
    //hslpanen = document.getElementById('hslpanen').value;
    hslpanen = document.getElementById('hslpanen').value;
    premihadir = document.getElementById('premihadir').value;
    brondolanperkg = document.getElementById('brondolanperkg').value;
    method=trim(document.getElementById('method').value);

    param = 'id=' + id + '&kodeorg=' + kodeorg + '&hasil=' + hasil + '&tahuntanam=' + tahuntanam+
        '&bulanawal=' + bulanawal + '&bulanakhir=' + bulanakhir +
        '&lebihbasis=' + lebihbasis + '&rupiah=' + rupiah + '&premirajin=' + premirajin +'&premihadir=' + premihadir +'&brondolanperkg=' + brondolanperkg + '&method=' + method + '&hslpanen=' + hslpanen;
    tujuan = 'kebun_slave_5premipanen.php';
    if (kodeorg == '') {
        alert('Regional Belum Dipilih.');
    }
    else if((tahuntanam == '') || (tahuntanam == '0')){
        alert('Tahun Tanam Belum Diisi.');
    }
    else if ((hasil == '') || (hasil == '0')) {
        alert('Basis (KG) Belum Diisi.');
    }
    else if ((lebihbasis == '') || (lebihbasis == '0')) {
        alert('Lebih Basis (Kg) Belum Diisi.');
    }
    else if ((rupiah == '') || (rupiah == '0')) {
        alert('Rupiah Belum Diisi.');
    }
    /*else if ((premirajin == '') || (premirajin == '0')) {
        alert('Premi Rajin (Rp) Belum Diisi.');
    }
    else if ((tahuntanam == '') || (tahuntanam == '0')) {
        alert('Tahun Tanam Belum Diisi.');
    }*/
    else {
        if (confirm('Yakin Ingin Menambah Data Baru ?')) {
            post_response_text(tujuan, param, respon);
        }
        //post_response_text(tujuan, param, respon);
    }
    

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loadData();
                    cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(fileTarget+'.php', param, respon);
}

function fillField(id, kodeorg, hasil, lebihbasis, rupiah, premirajin, tahuntanam, bulanawal, bulanakhir, premihadir, brondolanperkg,hasilpanen)
{
    document.getElementById('dataid').value=id;
    //document.getElementById('kodeorg').value = kodeorg;
    jk = document.getElementById('kodeorg');
    for (x = 0; x < jk.length; x++) {
        if (jk.options[x].value == kodeorg) {
            jk.options[x].selected = true;
        }
    }
    document.getElementById('hasil').value=hasil;
    document.getElementById('lebihbasis').value=lebihbasis;
    document.getElementById('rupiah').value=rupiah;
    document.getElementById('premirajin').value = premirajin;
    document.getElementById('tahuntanam').value = tahuntanam;
    document.getElementById('bulanawal').value = bulanawal;
    document.getElementById('bulanakhir').value = bulanakhir;
    document.getElementById('premihadir').value = premihadir;
    document.getElementById('brondolanperkg').value = brondolanperkg;
    document.getElementById('hslpanen').value = hasilpanen;
    jka = document.getElementById('hslpanen');
    for (q = 0; q < jka.length; q++) {
        if (jka.options[q].value == hasilpanen) {
            jka.options[q].selected = true;
        }
    }
    document.getElementById('method').value="update";
}

function cancelIsi()
{
    document.getElementById('dataid').value='';
    document.getElementById('kodeorg').value = '';
    document.getElementById('tahuntanam').value = '';
    document.getElementById('hasil').value='0';
    document.getElementById('lebihbasis').value='0';
    document.getElementById('rupiah').value='0';
    document.getElementById('premirajin').value = '0';
    document.getElementById('premihadir').value = '0';
    document.getElementById('brondolanperkg').value = '0';
    document.getElementById('hslpanen').value = '0';
    document.getElementById('method').value="insert";
}

function del(id)
{
	param='method=delete'+'&id='+id;
	//alert(param);
	tujuan='kebun_slave_5premipanen.php';
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
					else 
					{
						loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}
