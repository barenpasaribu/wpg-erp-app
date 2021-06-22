 

 //update ind

function gettbs()

{


	ambilSisaTbsKemarin()
	ambilTbsmasuk()
	ambilTbsolah()

	tanggal=document.getElementById('tanggal').value;

	param='proses=gettbs'+'&tanggal='+tanggal;

	tujuan = 'pabrik_slave_produksi_gettbs.php';

	

	//alert(param);

		

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

					//	alert(con.responseText);

					  document.getElementById('tbsmasuk').value=con.responseText;

                    	hitungSisa();

//
/*
var res = document.getElementById('loriolah');

					res.value = con.responseText;
					*/
//



					}

                }

                else {

                    busy_off();

                    error_catch(con.status);

                    

                }

      }	

     } 	

}

 

 



function simpanProduksi()

{

	kodeorg 	=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;

	tanggal  	=document.getElementById('tanggal').value;

	sisatbskemarin	=document.getElementById('sisatbskemarin').value;

	tbsmasuk	=document.getElementById('tbsmasuk').value;

	tbsdiolah	=document.getElementById('tbsdiolah').value;

	sisahariini	=document.getElementById('sisa').value;	

		

	oer			=document.getElementById('oercpo').value;	

	dirt		=document.getElementById('dirtcpo').value;

	kadarair	=document.getElementById('kadaraircpo').value;

	ffa		=document.getElementById('ffacpo').value;

        dbi             =document.getElementById('dobi').value;



	oerpk		=document.getElementById('oerpk').value;	

	dirtpk		=document.getElementById('dirtpk').value;

	kadarairpk	=document.getElementById('kadarairpk').value;

	ffapk		=document.getElementById('ffapk').value;

        intipecah       =document.getElementById('intipecah').value;

        btk             =document.getElementById('batu').value;

        

        usbb		=document.getElementById('usbbefore').value;

        usbaf		=document.getElementById('usbafter').value;

        oildil		=document.getElementById('oildiluted').value;

        oilin		=document.getElementById('oilin').value;

        oilinhe		=document.getElementById('oilinheavy').value;

        caco		=document.getElementById('caco').value;

        

        //cpo loses

        fruit		=document.getElementById('fruitineb').value;

        ebstalk		=document.getElementById('ebstalk').value;

        fibre		=document.getElementById('fibre').value;

        nut		=document.getElementById('nut').value;

        efflue		=document.getElementById('effluent').value;

        solidd		=document.getElementById('soliddecanter').value;

        

        //kernel loses

        fruitiker	=document.getElementById('fruitinebker').value;

        cycl		=document.getElementById('cyclone').value;

        ltds = document.getElementById('ltds').value;

        ltds2 = document.getElementById('ltds2').value;

        claybath	=document.getElementById('claybath').value;

	statSound	=document.getElementById('statSounding').value;	



	// cangkang



	cangkang				=document.getElementById('cangkang').value;

    ffa_cangkang			=document.getElementById('ffa_cangkang').value;

    kadarair_cangkang 		= document.getElementById('kadarair_cangkang').value;

    kotoran_cangkang 		= document.getElementById('kotoran_cangkang').value;



        

		

	if(kodeorg=='' ||  tanggal==''  || sisahariini=='' || sisahariini==null || sisatbskemarin=='' || sisatbskemarin==null || tbsmasuk=='' ||tbsmasuk==null || tbsdiolah=='' ||tbsdiolah==null || oer=='' ||oer==null || kadarair=='' ||kadarair==null || ffa==''  ||ffa==null  || dirt==''  ||dirt==null  || oerpk=='' ||oerpk==null || kadarairpk=='' ||kadarairpk==null || ffa==''  ||ffa==null  || dirtpk==''||dirtpk==null || cangkang == null || cangkang == '' || ffa_cangkang == null || ffa_cangkang == '' || kadarair_cangkang == null || kadarair_cangkang == '' || kotoran_cangkang == null || kotoran_cangkang =='')

	{

		alert('Semua field harus diisi');

	}

	else

	{

            if(parseFloat(statSound)!=0){

                alert("Please check sounding production data!!");

                return;

            }

		param='kodeorg='+kodeorg+'&tanggal='+tanggal;

		param+='&tbsmasuk='+tbsmasuk+'&tbsdiolah='+tbsdiolah;

		param+='&sisahariini='+sisahariini+'&sisatbskemarin='+sisatbskemarin;

		param+='&dirt='+dirt+'&kadarair='+kadarair;

		param+='&oer='+oer+'&ffa='+ffa;

		param+='&dirtpk='+dirtpk+'&kadarairpk='+kadarairpk;

		param+='&oerpk='+oerpk+'&ffapk='+ffapk+'&intipecah='+intipecah;

                

                param+='&usbbefore='+usbb+'&usbafter='+usbaf;

                param+='&oildiluted='+oildil+'&oilin='+oilin;

                param+='&oilinheavy='+oilinhe+'&caco='+caco;

                //cpo loses

                param+='&fruitineb='+fruit+'&ebstalk='+ebstalk;

                param+='&fibre='+fibre+'&nut='+nut+'&dobi='+dbi;

                param+='&effluent='+efflue+'&soliddecanter='+solidd;

                

                //kernel loses

                param+='&fruitinebker='+fruitiker+'&cyclone='+cycl;

                param += '&ltds=' + ltds + '&ltds2=' + ltds2 + '&claybath=' + claybath + '&batu=' + btk;



                // cangkang



                param +='&cangkang='+cangkang+'&ffa_cangkang='+ffa_cangkang+'&kadarair_cangkang='+kadarair_cangkang+'&kotoran_cangkang='+kotoran_cangkang;

		tujuan='pabrik_slave_save_produksi.php';

		

	// alert(param);	

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

						else {;

							document.getElementById('container').innerHTML=con.responseText;

						        bersihkanForm();

						}

					}

					else {

						busy_off();

						error_catch(con.status);

					}

		      }	

	 } 		

}



function bersihkanForm()

{

	document.getElementById('tanggal').value='';

	document.getElementById('sisatbskemarin').value='0';

	document.getElementById('tbsmasuk').value='0';

	document.getElementById('tbsdiolah').value='0';

	document.getElementById('sisa').value='0';

	

	document.getElementById('oercpo').value='0';

	document.getElementById('dirtcpo').value='0';

	document.getElementById('kadaraircpo').value='0';

	document.getElementById('ffacpo').value='0';

	document.getElementById('oerpk').value='0';

	document.getElementById('dirtpk').value='0';

	document.getElementById('kadarairpk').value='0';

	document.getElementById('ffapk').value='0';

	document.getElementById('intipecah').value='0';

        document.getElementById('dobi').value='0';

        document.getElementById('batu').value='0';

	

        document.getElementById('usbbefore').value='0';

        document.getElementById('usbafter').value='0';

        document.getElementById('oildiluted').value='0';

        document.getElementById('oilin').value='0';

        document.getElementById('oilinheavy').value='0';

        document.getElementById('caco').value='0';

        

        //cpo loses

        document.getElementById('fruitineb').value='0';

        document.getElementById('ebstalk').value='0';

        document.getElementById('fibre').value='0';

        document.getElementById('nut').value='0';

        document.getElementById('effluent').value='0';

        document.getElementById('soliddecanter').value='0';

        

        //kernel loses

        document.getElementById('fruitinebker').value='0';

        document.getElementById('cyclone').value='0';

        document.getElementById('ltds').value='0';

        document.getElementById('claybath').value='0';

        

        

}



function delProduksi(kodeorg,tanggal)

{

		param='kodeorg='+kodeorg+'&tanggal='+tanggal;

		param+='&del=true';

		if (confirm('Yakin akan hapus data ?')) {

			tujuan = 'pabrik_slave_save_produksi.php';

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

						else {;

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


function ambilSisaTbsKemarin(){

    noakun=document.getElementById('kodeorg');

    noakun=noakun.options[noakun.selectedIndex].value;
	tanggal=document.getElementById('tanggal').value;
	
	param="ngapain=ambilkegiatan";
	
	param+="&tanggal="+tanggal;

    param+="&noakun="+noakun;

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                  //  var res = document.getElementById('totallori');
					var res = document.getElementById('tbsmasuk');
                    res.value = con.responseText;

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }    

    post_response_text('setup_mill_generate_tbsmasuk.php', param, respon);

}


function ambilTbsmasuk(){

    noakun=document.getElementById('kodeorg');

    noakun=noakun.options[noakun.selectedIndex].value;
	tanggal=document.getElementById('tanggal').value;
	
	param="ngapain=ambilkegiatan";
	
	param+="&tanggal="+tanggal;

    param+="&noakun="+noakun;

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                  //  var res = document.getElementById('totallori');
					var res = document.getElementById('tbsmasuk');
                    res.value = con.responseText;

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }    

    post_response_text('setup_mill_generate_tbsmasuk.php', param, respon);

}

function ambilTbsolah(){

    noakun=document.getElementById('kodeorg');

    noakun=noakun.options[noakun.selectedIndex].value;
	tanggal=document.getElementById('tanggal').value;
	
	param="ngapain=ambilkegiatan";
	
	param+="&tanggal="+tanggal;

    param+="&noakun="+noakun;

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                  //  var res = document.getElementById('totallori');
					var res = document.getElementById('tbsdiolah');
                    res.value = con.responseText;

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }    

    post_response_text('setup_mill_generate_tbsolah.php', param, respon);

}

function ambilTbsolah1()

{

	sisatbskemarin	=parseInt(document.getElementById('sisatbskemarin').value);

	tbsmasuk		=parseInt(document.getElementById('tbsmasuk').value);

	tbsdiolah		=parseInt(document.getElementById('tbsdiolah').value);

	sisa=(sisatbskemarin+tbsmasuk)-tbsdiolah;

	if (sisa >= 0) {

		document.getElementById('sisa').value = sisa;

	}

	else

	{

		alert('TBS Kemarin + TBS Masuk, kurang dari TBS Diolah');

		document.getElementById('tbsdiolah').value=0;

	}	

}


function hitungSisa()

{

	sisatbskemarin	=parseInt(document.getElementById('sisatbskemarin').value);

	tbsmasuk		=parseInt(document.getElementById('tbsmasuk').value);

	tbsdiolah		=parseInt(document.getElementById('tbsdiolah').value);

	sisa=(sisatbskemarin+tbsmasuk)-tbsdiolah;

	if (sisa >= 0) {

		document.getElementById('sisa').value = sisa;

	}

	else

	{

		alert('TBS Kemarin + TBS Masuk, kurang dari TBS Diolah');

		document.getElementById('tbsdiolah').value=0;

	}	

}



function periksaCPO(obj)

{

  	dirt		=parseFloat(document.getElementById('dirtcpo').value);

	kadarair	=parseFloat(document.getElementById('kadaraircpo').value);

	ffa			=parseFloat(document.getElementById('ffacpo').value);

	x=dirt+kadarair+ffa;

	if(x>50)//yang tidak terpakai lebih besar di dalam cpo

	{

		alert('Kotoran + Kadar Air + FFA CPO terlalu besar, lebih dari 50');

		//obj.focus();

		obj.value=0;

				

	} 	

}

function periksaPK(obj)

{

	oerpk		=parseFloat(document.getElementById('oerpk').value);	

	dirtpk		=parseFloat(document.getElementById('dirtpk').value);

	kadarairpk	=parseFloat(document.getElementById('kadarairpk').value);

	ffapk		=parseFloat(document.getElementById('ffapk').value);	

	x=dirtpk+kadarairpk+ffapk;

	if(x>50)//yang tidak terpakai lebih besar di dalam pk

	{

		alert('Kotoran + Kadar Air + FFA PK terlalu besar, lebih dari 50');

		//obj.focus();

		obj.value=0;

		

	}	

}



function periksaOERCPO(obj)

{

	oercpo		=parseFloat(document.getElementById('oercpo').value);

	if(oercpo<1)

	{

		alert('OER CPO kurang dari atau sama dengan 0');

		//obj.focus();

		obj.value=0;

		

	}	

}

function periksaOERPK(obj)

{

	oerpk		=parseFloat(document.getElementById('oerpk').value);

	if(oerpk<1)

	{

		alert('OER PK kurang dari atau sama dengan 0');

		//obj.focus();

		obj.value=0;

		

	}	

}



function getLaporanPrdPabrik()

{

	periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;

	tampil=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].text;

    pabrik=document.getElementById('pabrik').options[document.getElementById('pabrik').selectedIndex].text;

	param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;

    tujuan='pabrik_slave_3produksiHarian.php';

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

						else {;

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





function laporanPDF(periode,tampil,pabrik,ev)

{

    param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;

   tujuan = 'pabrik_slave_printProduksi_pdf.php?'+param;	

 //display window

   title=periode;

   width='700';

   height='400';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"

   showDialog1(title,content,width,height,ev);

}



function grafikProduksi(periode,tampil,pabrik,ev)

{

   param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;

   //document.getElementById('container').innerHTML="<img src='pabrik_slave_grafikProduksi.php?"+param+"'>";		

   tujuan='pabrik_slave_grafikProduksi.php?'+param;

   title=periode;

   width='700';

   height='400';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"

   showDialog1(title,content,width,height,ev);

}



function laporanEXCEL(periode,tampil,pabrik,ev)

{

    param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;

   tujuan = 'pabrik_slave_printProduksi_excel.php?'+param;	

 //display window

   title=periode;

   width='700';

   height='400';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"

   showDialog1(title,content,width,height,ev);

}

function showDetail(tgl,kdorg,ev)

{

  

	title="Data Detail";

	content="<fieldset><legend>Unit : "+kdorg+", Tanggal "+tgl+"</legend><div id=contDetail style='overflow:auto; width:750px; height:320px;' ></div></fieldset>";

	width='800';

	height='370';

	showDialog1(title,content,width,height,ev);	

}

function previewDetail(tgl,kdorg,ev)

{

	showDetail(tgl,kdorg,ev);

	param='kdorg='+kdorg+'&method=getDetailPP'+'&tgl='+tgl;

	tujuan='pabrik_slave_produksi.php';

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

							document.getElementById('contDetail').innerHTML=con.responseText;

						}

					}

					else {

						busy_off();

						error_catch(con.status);

					}

		      }	

	 }  

	

}

function getKgCpo(){

        tgl=document.getElementById('tanggal').value;

        tgldt=tgl.split("-");

        tglhrini=new Date();

        var bulanarray=new Array("01","02","03","04","05","06","07","08","09","10","11","12"); 

        var tahun=tglhrini.getFullYear()

        var hari=tglhrini.getDay()

        var bulan=tglhrini.getMonth()

        var tanggal=tglhrini.getDate() 

        tglsystemdt=tahun+"-"+bulanarray[bulan]+"-"+tanggal;

        tglinputan=tgldt[2]+tgldt[1]+tgldt[0];

        tglSystemHrini=tahun+bulanarray[bulan]+tanggal;

        if(tglinputan>=tglSystemHrini){

            alert("Please use date before "+tanggal+"-"+bulanarray[bulan]+"-"+tahun);

            return;

        }

        

        param='tanggal='+tgl+'&method=getCpo';

	tujuan='pabrik_slave_produksi.php';

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

                                                        dert=con.responseText.split("####");

                                                        if(parseFloat(dert[0])==0){

                                                            document.getElementById('oercpo').value=dert[1];

                                                        }else{

                                                            document.getElementById('statSounding').value=dert[0];

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