function graph(ev)
{
	reg=document.getElementById('reg').value;
	per=document.getElementById('per').value;
	
	if(reg=='' || per=='')
	{
		alert('Field was empty');return
	}
	else
	{
	}
	
   param='reg='+reg+'&per='+per;
   //alert(param);
   //document.getElementById('container').innerHTML="<img src='pabrik_slave_grafikProduksi.php?"+param+"'>";		
   tujuan='kebun_slave_2qc_rekapPanenPerDivisi.php?'+param;
   title=reg;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}


