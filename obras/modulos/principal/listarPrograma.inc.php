<?
/*
define("AD"   ,26101);
define("CAPES",26291);
define("INEP" ,26290);
define("FNDE" ,26298);
define("FIES" ,74902);
*/

$atiid = $_POST[atiid] ? $_POST[atiid] : $_GET[atiid]; 
$prgcod = $_POST['prgcod'];
$acacod = $_POST['acacod'];
$sbaidFiltro = "";
$prgano = date("Y");


/*
 * Solicitado pelo Henrique Xavier
 * Feito por Alexandre Dourado
 * Adapta��o para listar + as a��es da unidade or�ament�ria 
 */
if($_SESSION['obra']['obrid']) {
	$sql = "SELECT entidunidade FROM obras.obrainfraestrutura WHERE obrid='".$_SESSION['obra']['obrid']."'";
	$entidunidade = $db->pegaUm($sql);
	if($entidunidade) {
		$sql_unidadeorc = " OR ent.entid='".$entidunidade."'";
	}
}
/*
 * FIM
 * Adapta��o para listar + as a��es da unidade or�ament�ria 
 */


// verificando se � undidade ou unidade gestora

$where .= $_REQUEST['sbaid'] ? "AND sad.sbaid = '".$_REQUEST['sbaid']."'" : '';
$where .= $_POST['prgcod'] ? "AND UPPER(dtl.prgcod) LIKE('%".strtoupper($_POST['prgcod'])."%')" : '';
$where .= $_POST['acacod'] ? "AND UPPER(dtl.acacod) LIKE('%".strtoupper($_POST['acacod'])."%')" : '';
$where .= $_POST['buscalivre'] ? "AND (trim(dtl.prgcod||'.'||dtl.acacod||'.'||dtl.loccod||' - '||dtl.acadsc) ilike('%".$_POST['buscalivre']."%') OR dtl.ptres ilike '%".$_POST['buscalivre']."%')" : '';

$sql_lista = "SELECT '<input type=\"checkbox\" id=\"chk_'||dtl.ptres||'\" onclick=\"resultado(this,\''||dtl.acaid||'\',\''||dtl.ptres||'\');\">' as checkbox,
					 dtl.ptres,
					 trim(dtl.prgcod||'.'||dtl.acacod||'.'||dtl.unicod||'.'||dtl.loccod||' - '||dtl.acadsc) as descricao,
					 ent.entnome,
					 sum(ptr.ptrdotacao) as dotacaoinicial,
					 CASE WHEN SUM(sad.sadvalor)!=0 THEN SUM(sad.sadvalor) ELSE '0.00' END as dotacaosubacao,
 					 CASE WHEN SUM(dtl.valorpi)!=0 THEN SUM(dtl.valorpi) ELSE '0.00' END as detalhamento,
					 CASE WHEN (SUM(sad.sadvalor)-coalesce(SUM(dtl.valorpi),0))!=0 THEN (SUM(sad.sadvalor)-coalesce(SUM(dtl.valorpi),0)) ELSE '0.00' END as diferenca 
					 FROM ( select dtl.acaid, dtl.ptres, dtl.prgano, dtl.prgcod, dtl.acacod, dtl.unicod, dtl.loccod, dtl.acadsc, sum(dtl.valorpi) as valorpi	
							from monitora.v_detalhepiptres dtl
							group by dtl.acaid, dtl.ptres, dtl.prgano, dtl.prgcod, dtl.acacod, dtl.unicod, dtl.loccod, dtl.acadsc ) dtl  
					LEFT JOIN entidade.entidade ent ON ent.entunicod = dtl.unicod AND ent.entungcod IS NULL 
					LEFT JOIN monitora.ptres ptr ON dtl.acaid = ptr.acaid
					LEFT JOIN monitora.subacaodotacao sad ON ptr.ptrid = sad.ptrid 
					WHERE
						  (sad.sbaid = '".$_SESSION['obras_var']['sbaid']."' ".$sql_unidadeorc.") AND
						  dtl.prgano = '".$prgano."'  
					";
$sql_lista .= $where ? $where : ''; 
$sql_lista .= " GROUP BY dtl.ptres,checkbox,descricao,ent.entnome 
				ORDER BY 1;";
?>
<html>
<head>

<script type="text/javascript" src="../includes/funcoes.js"></script>
<link rel="stylesheet" type="text/css" href="../includes/Estilo.css" />
<link rel='stylesheet' type='text/css' href='../includes/listagem.css'/>
</head>
<body leftmargin="0" topmargin="0" bottommargin="0" marginwidth="0">
<?php monta_titulo($entnome, '&nbsp'); ?>
<form action="" method="post" name="formulario">

<table width="100%" class="tabela" bgcolor="#f5f5f5" border="0" cellSpacing="1" cellPadding="3" align="center">
<tr>
	<td class="SubTituloDireita" align="right">Programa:</td>
	<td>
	<?
	$sql = "SELECT p.prgcod as codigo, (p.prgcod || ' - ' || p.prgdsc) as descricao
			FROM monitora.programa p
			WHERE p.prgano = '".$prgano."' ORDER BY prgcod";
	$db->monta_combo('prgcod', $sql, 'S','Selecione','','','',400); 
	?>
	</td>
</tr>	
<tr>
	<td class="SubTituloDireita" align="right">A��o:</td>
	<td>
	<?
	$sql = "SELECT ac.acacod as codigo, (ac.acacod || ' - ' || ac.acadsc) as descricao
 			FROM monitora.acao ac 
 			WHERE ac.unicod = '".$unicod."' AND ac.prgano = '".$prgano."' AND ac.acastatus = 'A' AND ac.acasnrap = false  
 			GROUP BY ac.acacod, ac.acadsc 
 			ORDER BY ac.acacod";
	$db->monta_combo('acacod', $sql, 'S', 'Selecione','','','',400); 
	?>
	</td>
</tr>
<tr>
	<td class="SubTituloDireita" align="right">Buscar:</td>
	<td>
	<? echo campo_texto('buscalivre', "N", "S", "", 67, 150, "", "", '', '', 0, '' ); ?>
	</td>
</tr>
<tr style="background-color: #cccccc">
	<td align='right' style="vertical-align:top; width:25%;">&nbsp;</td>
	<td>
	<input type="submit" name="botao" value="Pesquisar"/>
	<input type="button" name="botao" value="Todos" onclick="window.location='?modulo=principal/planotrabalho/listarPrograma&acao=A';"/>
	<input type="button" name="close" value="Fechar" onclick="window.close();">	
	</td>
</tr>		
</table>

</form>
<br>
<?

$cabecalho = array ("", "PTRES", "A��o", "Unidade Or�ament�ria", "Dota��o inicial", "Dota��o SubA��o", "Detalhado no PI", "Dota��o Dispon�vel" );
$db->monta_lista($sql_lista,$cabecalho,60,20,'','','');

?>
<script type="text/javascript">
/* CARREGANDO OS DADOS DE PTRES */
var tabelaorigem = window.opener.document.getElementById('orcamento');
for(i=2;i<tabelaorigem.rows.length-2;i++) {
	if(document.getElementById("chk_"+tabelaorigem.rows[i].cells[0].innerHTML)) {
		document.getElementById("chk_"+tabelaorigem.rows[i].cells[0].innerHTML).checked=true;
	}
}
/* FIM CARREGANDO OS DADOS DE PTRES */

function resultado(dados, acaid, ptres){

	if(!ptres) {
		alert('N�o existe PTRES. Entre em contato com o administrador do sistema.');
		return false;
	}


	if(dados.checked) {

	var linhaTbl = dados.parentNode.parentNode;
	var tabelaorigem = window.opener.document.getElementById('orcamento');
	if(eval(tabelaorigem.rows.length%2)) {
		var cor = "";
	} else {
		var cor = "#DCDCDC";
	}
	var linha = tabelaorigem.insertRow(2);
	linha.id = "ptres_"+ptres;
	linha.style.backgroundColor = cor;
	linha.style.height = '30px';
	
		// setando o ptres
		var celula1 = tabelaorigem.rows[2].insertCell(0);
		celula1.style.textAlign = "center";
		celula1.innerHTML = ptres;
		
		var celula2 = tabelaorigem.rows[2].insertCell(1);
		celula2.style.textAlign = "left";
		celula2.innerHTML = linhaTbl.cells[2].innerHTML+"<input type='hidden' name='acaid["+ptres+"]' value='"+acaid+"'>";
	
		var celula3 = tabelaorigem.rows[2].insertCell(2);
		celula3.style.textAlign = "right";
		celula3.innerHTML = linhaTbl.cells[4].innerHTML;
		
		var celula4 = tabelaorigem.rows[2].insertCell(3);
		celula4.style.textAlign = "right";
		celula4.innerHTML = linhaTbl.cells[5].innerHTML;
		
		var celula5 = tabelaorigem.rows[2].insertCell(4);
		celula5.style.textAlign = "right";
		celula5.innerHTML = "<a href=javascript:detfin('"+ptres+"')>"+linhaTbl.cells[6].innerHTML+"</a>";
		
		var celula6 = tabelaorigem.rows[2].insertCell(5);
		celula6.style.textAlign = "right";
		celula6.innerHTML = linhaTbl.cells[7].innerHTML;
	
		var celula7 = tabelaorigem.rows[2].insertCell(6);
		celula7.style.textAlign = "center";
		celula7.innerHTML = "<input type=\"text\" name=\"plivalor["+ptres+"]["+acaid+"]\" size=\"28\" maxlength=\"\" value=\"\" onKeyUp=\"this.value=mascaraglobal('###.###.###.###,##',this.value);calculovalorPI();\" class=\"normal\"  onmouseover=\"MouseOver(this);\" onfocus=\"MouseClick(this);this.select();\" onmouseout=\"MouseOut(this);\" onblur=\"MouseBlur(this);\" style=\"text-align : right; width:25ex;\" title='' />";


	switch(ptres) {
		case '001703':
			var unidgest = window.opener.document.getElementById('000000003_0');
			var unidresp = window.opener.document.getElementById('000000003_1');
			unidgest.parentNode.parentNode.style.display = 'none';
			unidresp.parentNode.parentNode.style.display = 'none';
			window.opener.document.getElementById('geradorcompleto').style.display = '';
			window.opener.document.getElementById('regraespecial1').options[1] = new Option("2773","2773");
			window.opener.document.getElementById('regraespecial1').options[2] = new Option("2774","2774");
			window.opener.document.getElementById('btn_selecionar_acaptres').disabled = true;
			window.close();
			break;
		case '001704':
			var unidgest = window.opener.document.getElementById('000000003_0');
			var unidresp = window.opener.document.getElementById('000000003_1');
			unidgest.parentNode.parentNode.style.display = 'none';
			unidresp.parentNode.parentNode.style.display = 'none';
			window.opener.document.getElementById('geradorcompleto').style.display = '';
			window.opener.document.getElementById('regraespecial1').options[1] = new Option("2339","2339");
			window.opener.document.getElementById('regraespecial1').options[2] = new Option("2783","2783");
			window.opener.document.getElementById('regraespecial1').options[3] = new Option("2778","2778");
			window.opener.document.getElementById('btn_selecionar_acaptres').disabled = true;
			window.close();
			break;
	}

	} else {

	var tabelaorigem = window.opener.document.getElementById('orcamento');
	tabelaorigem.deleteRow(window.opener.document.getElementById('ptres_'+ptres).rowIndex);
	window.opener.calculovalorPI();

	}

}


</script>
</body>
</html>