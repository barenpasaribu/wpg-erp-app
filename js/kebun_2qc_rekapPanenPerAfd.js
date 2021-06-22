function graph(ev)
{
	div=document.getElementById('div').value;
	per=document.getElementById('per').value;
	
	if(div=='' || per=='')
	{
		alert('Field was empty');return
	}
	else
	{
	}
	
   param='div='+div+'&per='+per;
   //alert(param);
   //document.getElementById('container').innerHTML="<img src='pabrik_slave_grafikProduksi.php?"+param+"'>";		
   tujuan='kebun_slave_2qc_rekapPanenPerAfd.php?'+param;
   title=div;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}


