function muatUlang() {
	console.log("clear");
	document.location.reload();
}
function simpan()
{
	id=document.getElementById('id').value;        
	kodeorg=document.getElementById('kodeorg').value;
	produk=document.getElementById('produk').value;
	namaitem=document.getElementById('namaitem').value;
	standard=document.getElementById('standard').value;
	satuan=document.getElementById('satuan').value;
	faktor_konversi_1 = document.getElementById('faktor_konversi_1').value;
	faktor_konversi_2 = document.getElementById('faktor_konversi_2').value;
	faktor_konversi_3 = document.getElementById('faktor_konversi_3').value;
	losses_to_tbs=document.getElementById('losses_to_tbs').value;
	linked_to=document.getElementById('linked_to').value;
	method=document.getElementById('method').value;

	if(kodeorg=='' || produk=='' || namaitem=='' || standard=='')
	{
		alert('Field masih kosong');
		return;
	}
	
	param='kodeorg='+kodeorg+'&produk='+produk+'&namaitem='+namaitem+'&standard='+standard+'&satuan='+satuan+'&faktor_konversi_1='+faktor_konversi_1+'&faktor_konversi_2='+faktor_konversi_2+'&faktor_konversi_3='+faktor_konversi_3+'&losses_to_tbs='+losses_to_tbs+'&linked_to='+linked_to+'&method='+method+'&id='+id;
	
	tujuan='pabrik_slave_5kelengkapanloses.php';
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
					// document.location.reload();
					data = con.responseText;
					if (data == "berhasil") {
						Swal.fire("Simpan data berhasil.");
						loadData();
					} else {
						Swal.fire('Oops...', data, 'error');
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}	
	}
}


function del(id)
{
	param='method=delete'+'&id='+id;
	tujuan='pabrik_slave_5kelengkapanloses.php';

	Swal.fire({
		title: 'Yakin untuk menghapus?',
		text: "Data yang dihapus tidak dapat dikembalikan lagi!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.value) {
				post_response_text(tujuan, param, respog);
			}
		});


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
						data = JSON.parse(con.responseText);
						if (data == "berhasil") {
							Swal.fire("Hapus data berhasil.");
							loadData();
						} else {
							Swal.fire('Oops...', "Hapus data gagal.", 'error');
						}
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}

function edit(id,kodeorg,produk,namaitem,standard,satuan,faktor_konversi_1,faktor_konversi_2,faktor_konversi_3,losses_to_tbs,linked_to)
{
	document.getElementById('id').value=id;
	document.getElementById('kodeorg').value=kodeorg;
	document.getElementById('produk').value=produk;
	document.getElementById('namaitem').value=namaitem;
	document.getElementById('standard').value=standard;
	document.getElementById('satuan').value=satuan;        
	document.getElementById('faktor_konversi_1').value=faktor_konversi_1;
	document.getElementById('faktor_konversi_2').value=faktor_konversi_2;
	document.getElementById('faktor_konversi_3').value=faktor_konversi_3;
	document.getElementById('losses_to_tbs').value=losses_to_tbs;
	document.getElementById('linked_to').value=linked_to;
	document.getElementById('method').value='update';
}

function loadData()
{
    param='method=loadData';
    tujuan='pabrik_slave_5kelengkapanloses';
    post_response_text(tujuan + '.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					document.getElementById('tableKelengkapanLoses').innerHTML=con.responseText;
					$('#dataKelengkapanLoses').DataTable();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}