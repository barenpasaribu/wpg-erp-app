/**

 * @author Developer

 */

function simpanVhc()

{

        jenisvhc=document.getElementById('jenisvhc').value;

        namajenisvhc=document.getElementById('namajenisvhc').value;

		noakun=document.getElementById('noakun').value;	

        kelompok=document.getElementById('kelompokvhc').options[document.getElementById('kelompokvhc').selectedIndex].value;	

        met=document.getElementById('method').value;

        if(trim(jenisvhc)=='')

        {

                alert('Type is empty');

                document.getElementById('jenisvhc').focus();

        }

        else

        {

                if (confirm('Saving..?')) {

                        jenisvhc = trim(jenisvhc);

                        namajenisvhc = trim(namajenisvhc);

						noakun = trim(noakun);

                        param = 'jenisvhc=' + jenisvhc + '&noakun=' + noakun + '&namajenisvhc=' + namajenisvhc + '&method=' + met;

                        param += '&kelompok=' + kelompok;

                        tujuan = 'vhc_slave_save_jenisvhc.php';

                        //alert(param);

                        post_response_text(tujuan, param, respog);

                }	

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

function fillField(kode,nama,noakun,kelompok)

{

        ob=document.getElementById('kelompokvhc');

        for(x=0;x<ob.length;x++)

        {

                if(ob.options[x].value==kelompok)

                {

                        ob.options[x].selected=true;

                }

        }

        document.getElementById('jenisvhc').value=kode;

        document.getElementById('jenisvhc').disabled=true;

        document.getElementById('namajenisvhc').value=nama;

        document.getElementById('noakun').value=noakun;		

        document.getElementById('method').value='update';

}

function cancelVhc()

{

    document.getElementById('jenisvhc').disabled=false;

        document.getElementById('jenisvhc').value='';

        document.getElementById('namajenisvhc').value='';

//	document.getElementById('noakun').value='';	

        document.getElementById('method').value='insert';		

}
///==============mastre VHC============================================

function getList()

{

  org=document.getElementById('kodeorg');

  kodeorg=org.options[org.selectedIndex].value;

  kelompokvhc=document.getElementById('kelompokvhc');

  kelompokvhc=kelompokvhc.options[kelompokvhc.selectedIndex].value;

  jenisvhc=	document.getElementById('jenisvhc');

  jenisvhc=jenisvhc.options[jenisvhc.selectedIndex].value;

  param='kelompokvhc='+kelompokvhc+'&jenisvhc='+jenisvhc+'&kodeorg='+kodeorg;

  tujuan='vhc_slave_save_vhc.php';

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

function fillMasterField(kodeorg,kelompokvhc,jenisvhc,kodevhc,beratkosong,nomorrangka,nomormesin,tahunperolehan,

kodebarang,kepemilikan,kodetraksi,tglakhirstnk,tglakhirkir,tglakhirijinbm,tglakhirijinang)

{

        ob=document.getElementById('kodeorg');

        for(x=0;x<ob.length;x++)

        {

                if(ob.options[x].value==kodeorg)

                {

                        ob.options[x].selected=true;
                        break;
                }

        }

        ob=document.getElementById('kelompokvhc');

        for(x=0;x<ob.length;x++)

        {

                if(ob.options[x].value==kelompokvhc)

                {

                        ob.options[x].selected=true;
                        break;

                }

        }	

        ob=document.getElementById('jenisvhc');

        for(x=0;x<ob.length;x++)

        {

                if(ob.options[x].value==jenisvhc)

                {

                        ob.options[x].selected=true;
                        break;

                }

        }	

        ob=document.getElementById('kodebarang');

        for(x=0;x<ob.length;x++)

        {

                if(ob.options[x].value==kodebarang)

                {

                        ob.options[x].selected=true;
                        break;

                }

        }

        ob=document.getElementById('kepemilikan');

        for(x=0;x<ob.length;x++)

        {
                console.log(kepemilikan+'');
                console.log(ob.options[x]);
                console.log(ob.options[x].value);
                if(ob.options[x].value==kepemilikan+'')

                {

                        ob.options[x].selected=true;
                        break;

                }

        }



        ob=document.getElementById('kodetraksi');

        for(x=0;x<ob.length;x++)

        {

                if(ob.options[x].value==kodetraksi)

                {

                        ob.options[x].selected=true;

                }

        }



         document.getElementById('kodevhc').disabled=true;

         document.getElementById('kodevhc').value=kodevhc;

         document.getElementById('tahunperolehan').value=tahunperolehan;

         document.getElementById('beratkosong').value=beratkosong;

         document.getElementById('nomorrangka').value=nomorrangka;

         document.getElementById('nomormesin').value=nomormesin;
        //  document.getElementById('kepemilikan').value=kepemilikan;
        //  var kepemilikan__=document.getElementById('kepemilikan');
        //  kepemilikan__.selectedIndex = (kepemilikan==0 ? 1 : 0);
        // //  kepemilikan__.value=kepemilikan;
        //  console.log( kepemilikan__.selectedIndex);
        //  console.log(kepemilikan);

         document.getElementById('detailvhc').value='';

         document.getElementById('method').value='update';

         document.getElementById('tglakhirstnk').value=tglakhirstnk;

         document.getElementById('tglakhirkir').value=tglakhirkir;

         document.getElementById('tglakhirijinbm').value=tglakhirijinbm;

         document.getElementById('tglakhirijinang').value=tglakhirijinang;

}

function cancelMasterVhc()

{

         document.getElementById('kodevhc').disabled=false;

         document.getElementById('kodevhc').value='';

         document.getElementById('tahunperolehan').value='';

//	 document.getElementById('noakun').value='';

         document.getElementById('beratkosong').value='';

         document.getElementById('nomorrangka').value='';

         document.getElementById('nomormesin').value='';

         document.getElementById('detailvhc').value='';

         document.getElementById('method').value='insert';	



}

function loadJenis(kelompok)

{

        param='kelompok='+kelompok;

        tujuan='vhc_slave_get_jenis.php';

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

                                                        document.getElementById('jenisvhc').innerHTML=con.responseText;

                                                   getList();

                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }	

}

function simpanMasterVhc()

{

                ob=document.getElementById('kodeorg');

        kodeorg=ob.options[ob.selectedIndex].value;

                ob=document.getElementById('kelompokvhc');

        kelompokvhc=ob.options[ob.selectedIndex].value;

                ob=document.getElementById('jenisvhc');

        jenisvhc	=ob.options[ob.selectedIndex].value;

                ob=document.getElementById('kodebarang');

        kodebarang	=ob.options[ob.selectedIndex].value;



                ob=document.getElementById('kepemilikan');

        kepemilikan	=ob.options[ob.selectedIndex].value;

                ob=document.getElementById('kodetraksi');

        kodetraksi	=ob.options[ob.selectedIndex].value;        



        kodevhc		=trim(document.getElementById('kodevhc').value);

        tahunperolehan=trim(document.getElementById('tahunperolehan').value);

//	noakun		=trim(document.getElementById('noakun').value);

        beratkosong	=trim(document.getElementById('beratkosong').value);

        nomorrangka	=trim(document.getElementById('nomorrangka').value);

        nomormesin	=trim(document.getElementById('nomormesin').value);

        detailvhc	=trim(document.getElementById('detailvhc').value);

        method		=trim(document.getElementById('method').value);

        tglakhirstnk	=document.getElementById('tglakhirstnk').value;

        tglakhirkir	=document.getElementById('tglakhirkir').value;

        tglakhirijinbm	=document.getElementById('tglakhirijinbm').value;

        tglakhirijinang	=document.getElementById('tglakhirijinang').value;





        if(trim(kodevhc)=='' || trim(kodeorg)=='' || trim(kelompokvhc)=='' || trim(jenisvhc)=='' || kodebarang=='')

        {

                alert('Code,Organization,Group,Material name or Type is obligatory');

                document.getElementById('jenisvhc').focus();

        }

        else if(tahunperolehan.length!=4)

        {

                alert('Year must four digits');

        }

        else

        {	

                if (confirm('Saving..?')) {

                        param = 'kodeorg=' + kodeorg + '&kelompokvhc=' + kelompokvhc + '&method=' + method;

                        param += '&jenisvhc=' + jenisvhc + '&kodevhc=' + kodevhc;

                        param += '&tahunperolehan=' + tahunperolehan ;

                        param += '&beratkosong=' + beratkosong + '&nomorrangka=' + nomorrangka;

                        param += '&nomormesin=' + nomormesin + '&detailvhc=' + detailvhc;

                        param += '&kodebarang='+kodebarang+'&kepemilikan='+kepemilikan+'&kodetraksi='+kodetraksi;

                        param += '&tglakhirstnk='+tglakhirstnk+'&tglakhirkir='+tglakhirkir;

                        param += '&tglakhirijinbm='+tglakhirijinbm+'&tglakhirijinang='+tglakhirijinang;

                        tujuan = 'vhc_slave_save_vhc.php';

//			alert(param);

                        post_response_text(tujuan, param, respog);

                }

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

function deleteMasterVhc(kodeorg,kelompokvhc,jenisvhc,kodevhc)

{

                method='delete';

                if (confirm('Deleting '+kodevhc +' ..?')) {

                        if (confirm('Are you sure..?')) {

                                param = 'kodeorg=' + kodeorg + '&kelompokvhc=' + kelompokvhc + '&method=' + method;

                                param += '&jenisvhc=' + jenisvhc + '&kodevhc=' + kodevhc;

                                tujuan = 'vhc_slave_save_vhc.php';

                                //alert(param);

                                post_response_text(tujuan, param, respog);

                        }

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

function dataKeExcel(ev,tujuan)

{    

    kodeorg=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;

    kodebarang=document.getElementById('kodebarang').options[document.getElementById('kodebarang').selectedIndex].value;

    kodetraksi=document.getElementById('kodetraksi').options[document.getElementById('kodetraksi').selectedIndex].value;

    kodevhc=document.getElementById('kodevhc').value;

    method=trim(document.getElementById('method').value);

    judul='Report Ms.Excel';

    param ='kodeorg='+kodeorg+'&kodevhc='+kodevhc+'&kodebarang='+kodebarang+'&kodetraksi='+kodetraksi+'&method=excel';

    printFile(param,tujuan,judul,ev)	

}

function printFile(param,tujuan,title,ev)

{

   tujuan=tujuan+"?"+param;  

   width='600';

   height='300';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"

   showDialog1(title,content,width,height,ev); 	

}

function deAktif(kdvhc,stat){

       method='deactive';

       dert="";

       if(stat=='1'){

           dert="Deactivate";

       }else{

           dert="Actived";

       }

        if (confirm(dert+' '+kdvhc +' ..?')) {

                if (confirm('Are you sure..?')) {

                        param = 'method=' + method+ '&kodevhc=' + kdvhc;

                        param+='&status='+stat;

                        tujuan = 'vhc_slave_save_vhc.php';

                        //alert(param);

                        post_response_text(tujuan, param, respog);

                }

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