<?php

//ajax dos municipios
if(isset($_POST['estcod']) && !isset($_POST['orgid'])){
	header("Content-Type: text/html; charset=ISO-8859-1");
	$sql = "SELECT 
				mundescricao as descricao,
				muncod as codigo
			FROM
				territorios.municipio
			WHERE
				estuf = '{$_POST['estcod']}'
			ORDER BY
				descricao";
	
	$municipicos = $db->carregar($sql);

	$html = "<option value=''>Todas</option>";

	foreach ($municipicos as $valores) {
		$html .= "<option value='{$valores['codigo']}'>{$valores['descricao']}</option>";
	}
	
	echo $html;
	die;
}

//ajax das unidades implantadoras
if(isset($_POST['orgid']) && !isset($_POST['estcod'])){
	header("Content-Type: text/html; charset=ISO-8859-1");
	
	$sql = "SELECT 
				ee.entid as codigo, 
				upper(ee.entnome) as descricao 
			FROM
				entidade.entidade ee
			INNER JOIN 
				obras.obrainfraestrutura oi ON oi.entidunidade = ee.entid 
			WHERE
				orgid = {$_POST['orgid']} AND
				obsstatus = 'A'
			GROUP BY 
				ee.entnome, 
				ee.entid 
			ORDER BY 
				ee.entnome";
	
	$unidades = $db->carregar($sql);
	
	$html = "<option value=''>Todas</option>";

	foreach ($unidades as $valores) {
		$html .= "<option value='{$valores['codigo']}'>{$valores['descricao']}</option>";
	}
	
	echo $html;
	die;
}

//ajax das unidades implantadoras

$supervisao = new supervisao();

switch( $_REQUEST["requisicao"] ){
	
	case "pesquisa":
		$filtros = $supervisao->obrFiltraListaGrupos( );
	break;
	
	case "excluir":
		
		if( $supervisao->obrVerficaDadoRequisicao( $_REQUEST["gpdid"], "grupodistribuicao", "gpdid", "gpdstatus = 'A'" ) ){
			$supervisao->obrExcluirGrupo( $_REQUEST["gpdid"] );	
		}else{
			$supervisao->obrExibeMsgErro( "O grupo informado n�o existe!" );
		}
		
	break;
	
}

// cabecalho padr�o do simec
include APPRAIZ . "includes/cabecalho.inc";

// Monta as abas
print "<br/>";
$db->cria_aba( $abacod_tela, $url, $parametros );
monta_titulo( "Distribui��o de Lotes", "" );

?>
<script type="text/javascript" src="../includes/JQuery/jquery-1.4.2.js"></script>
<script type="text/javascript">
<!--
$(document).ready(function() {

	<?php if(isset($_POST['semos']) ){ ?>
	$('#obrFormPesLote :input:not(#Pesquisar, #Limpar, #semos, #requisicao)').attr('disabled', true);
	<?php } ?>
	
	$("#semos").click(function () {

		// verificando se os campos est�o desabilitados
 		if( $('#obrFormPesLote :input:not(#Pesquisar, #Limpar, #semos, #requisicao)').attr('disabled') ){
 	 		//habilitando os campos
			$('#obrFormPesLote :input:not(#Pesquisar, #Limpar, #semos, #requisicao)').attr('disabled', false);
 		}else{
 	 		//desabilitando os campos
			$('#obrFormPesLote :input:not(#Pesquisar, #Limpar, #semos, #requisicao)').attr('disabled', true);
 		}
		
	})
	
	// ajax da unidade implantadora
	$("#orgid").change(function () {
		$('#entid').html('<option>Aguarde...</option>');
	
		var orgid = $("#orgid").val();
		
		//se orgid n�o possuir valor ent�o eu saio da fun��o
		if(!orgid){
			$('#entid').attr('disabled', true);
			$('#entid').html('');
			return false;
		}
		
		//enviando o post
		$.post(caminho_atual + '?modulo=principal/supervisao/distribuicao&acao=A', { orgid : orgid },
			function(data){
				$('#entid').html(data);
				$('#entid').attr('disabled', false);
			});
	});

	// ajax do municipio
	$("#estcod").change(function () {
		$('#munid').html('<option>Aguarde...</option>');
	
		var estcod = $("#estcod").val();
		
		//se estcod n�o possuir valor ent�o eu saio da fun��o
		if(!estcod){
			$('#munid').attr('disabled', true);
			$('#munid').html('');
			return false;
		}
		
		//enviando o post
		$.post(caminho_atual + '?modulo=principal/supervisao/distribuicao&acao=A', { estcod : estcod },
			function(data){
				$('#munid').html(data);
				$('#munid').attr('disabled', false);
			});
	});

});

function limparCampos(){
    $('input:not(:submit,:hidden,:button,[readonly=""],[readonly="readonly"]),textarea,select').val("");

    //desabilitando o munic�pio
    $('#munid').attr('disabled', true);
	$('#munid').html('');

	//desabilitando a unidade implantadora
	$('#entid').attr('disabled', true);
	$('#entid').html('');
    
}

//-->
</script>


<form action="" method="post" name="formulario" id="obrFormPesLote">
	<input type="hidden" name="requisicao" id="requisicao" value="pesquisa"/>
	<table class="tabela" bgcolor="#f5f5f5" cellSpacing="1" cellPadding=3 align="center">
		<tr>
			<td class="SubTituloCentro" colspan="2">Argumentos de Pesquisa</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">N� de Controle:</td>
			<td>
				<?php 
					$gpdid = $_REQUEST["gpdid"];
					print campo_texto( "gpdid", "N", "S", "", 10, 9, "", "", "left", "", 0, "gpdid");
				?>
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">Empresa:</td>
			<td>
				<?php 

					$epcid = $_REQUEST["epcid"];
				
					$sql = "SELECT DISTINCT
								epcid as codigo,
								entnome as descricao
							FROM
								entidade.entidade ee
							INNER JOIN
								obras.empresacontratada ec ON ee.entid = ec.entid
							WHERE
								entstatus = 'A'
							ORDER BY
								entnome";

					$db->monta_combo("epcid", $sql, "S", "Todas", '', '', '', '', 'N','epcid');
									
				?>
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">Situa��o Supervis�o do Grupo (Empresa):</td>
			<td>
				<?php 

					$esdid = $_REQUEST["esdid"];
				
					$sql = "SELECT DISTINCT
								esdid as codigo,
								esddsc as descricao
							FROM
								workflow.estadodocumento ed
							WHERE
								tpdid = " . OBR_TIPO_DOCUMENTO . "
								AND esdid <> ".OBRENVREAVALSUPMEC."
								AND esdid <> ".OBREMAVALIASUPERVMEC."
								AND esdid <> ".OBREMSUPERVISAO." 
								AND esdid <> ".OBRREAJSUPVISAOEMP." 
								AND esdid <> ".OBRREAVSUPVISAO." 
								AND esdid <> ".OBRREDISTRIBUIDO." 
								AND esdstatus = 'A'
							ORDER BY
								esddsc";
					
					$db->monta_combo("esdid", $sql, "S", "Todas", '', '', '', '', 'N','esdid');
				
				?>
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">Situa��o Supervis�o da Obra (Empresa):</td>
			<td>
				<?php 

					$esdidobra = $_REQUEST["esdidobra"];
				
					$sql = "SELECT DISTINCT
								esdid as codigo,
								esddsc as descricao
							FROM
								workflow.estadodocumento ed
							WHERE
								tpdid = " . OBR_TIPO_DOCUMENTO_OBRA . "
							ORDER BY
								esddsc";
					
					$db->monta_combo("esdidobra", $sql, "S", "Todas", '', '', '', '', 'N','esdidobra');
				
				?>
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">�rg�o:</td>
			<td>
				<?php 

					$orgid = $_REQUEST["orgid"];
				
					$sql = "SELECT 
								orgid AS codigo, 
								orgdesc AS descricao
  							FROM 
  								obras.orgao ";
					
					$db->monta_combo("orgid", $sql, "S", "Todas", '', '', '', '', 'N','orgid');
				
				?>
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">Unidade Responsável pela Obra:</td>
			<td>
				<?php
					if($_REQUEST['orgid']){
						
						$entid = $_REQUEST['entid'];
						$sql = "SELECT 
									ee.entid as codigo, 
									upper(ee.entnome) as descricao 
								FROM
									entidade.entidade ee
								INNER JOIN 
									obras.obrainfraestrutura oi ON oi.entidunidade = ee.entid 
								WHERE
									orgid = {$_REQUEST['orgid']} AND
									obsstatus = 'A'
								GROUP BY 
									ee.entnome, 
									ee.entid 
								ORDER BY 
									ee.entnome";
						
						$db->monta_combo("entid", $sql, "S", "Todas", '', '', '', '', 'N','entid');
					}else{ ?>						
					<select id="entid" style="width: auto;" class="CampoEstilo" name="entid" disabled="disabled">
					</select>
					<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">UF:</td>
			<td>
				<?php 

					$estcod = $_REQUEST["estcod"];
				
					$sql = "SELECT 
								estuf as descricao,
								estuf as codigo
							FROM
								territorios.estado
							ORDER BY
								descricao;";
					
					$db->monta_combo("estcod", $sql, "S", "Todas", '', '', '', '', 'N','estcod');
				
				?>
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">Munic�pio:</td>
			<td>
				<?php 
//					/*if(isset($_REQUEST['estcod'])){*/
					if($_REQUEST['estcod']){
						
						$munid = $_REQUEST['munid'];
						$sql = "SELECT 
								mundescricao as descricao,
								muncod as codigo
							FROM
								territorios.municipio
							WHERE
								estuf = '{$_REQUEST['estcod']}'
							ORDER BY
								descricao";
						
						$db->monta_combo("munid", $sql, "S", "Todas", '', '', '', '', 'N','munid');
						
					}else{ ?>
						<select id="munid" style="width: auto;" class="CampoEstilo" name="munid" disabled="disabled">
						</select>
				<?php } ?>		
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita" width="190px">Id da Obra:</td>
			<td>
				<?php 
					 $obrid = $_REQUEST["obrid"];
					
				echo campo_texto('obrid','N','',10 ,9, '', '', '', 'left','',0,'');
				
				?>
			</td>
		</tr>
		<tr>	
			<td class="SubTituloDireita" width="190px">Vizualiza��o:</td>
			<td>
				<input type="radio" name="listarobra" id="grupo" value="1" checked="checked" > Grupo
				<input type="radio" name="listarobra" id="lista" value="0" <?if($_REQUEST['listarobra']=='0'){print "checked='checked'";}  ?> > Lista da Obra 
			</td>
		</tr>
		<tr>
			<td class="SubTituloDireita">Rotas Aprovadas:</td>
			<td><input type="checkbox" name="rotas" value="1"<?php if(isset($_POST['rotas']) ){ echo' checked="checked"'; }else{ echo''; } ?>></td>
		</tr>
		<tr>
			<td class="SubTituloDireita">Grupo sem OS:</td>
			<td><input type="checkbox" name="semos" id="semos" value="1"<?php if(isset($_POST['semos']) ){ echo' checked="checked"'; }else{ echo''; } ?>></td>
		</tr>
		<tr bgcolor="#D0D0D0">
			<td></td>
			<td>
				<input type="button" value="Pesquisar" id="Pesquisar" onclick="document.getElementById('obrFormPesLote').submit();" style="cursor: pointer;">
				&nbsp;
				<input type="button" value="Limpar" id="Limpar" onclick="limparCampos();">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="SubTituloCentro">Lista de Grupos</td>
		</tr>
	</table>
</form>
	
<?php 
	if($_POST['listarobra'] == '0'){ 

		function monta_sql(){
			
			$whereGpdid  =( $_REQUEST['gpdid'] != '' ) ? " AND ig.gpdid  = '{$_REQUEST['gpdid']}'  " : ""; 
			$whereEpcid  =( $_REQUEST['epcid'] != '' ) ? " AND gd.epcid  = '{$_REQUEST['epcid']}'  " : ""; 
			$whereEsdid  =( $_REQUEST['esdid'] != '' ) ? " AND we.esdid  = '{$_REQUEST['esdid']}'  " : "";
			$whereOrgid  =( $_REQUEST['orgid'] != '' ) ? " AND oi.orgid  = '{$_REQUEST['orgid']}'  " : "";
			$whereEntid  =( $_REQUEST['entid'] != '' ) ? " AND ee.entid  = '{$_REQUEST['entid']}'  " : "";
			$whereEstcod =( $_REQUEST['estcod']!= '' ) ? " AND tm.estuf  = '{$_REQUEST['estcod']}' " : "";
			$whereMunid  =( $_REQUEST['munid'] != '' ) ? " AND tm.muncod = '{$_REQUEST['munid']}'  " : "";
			$whereObrid  =( $_REQUEST['obrid'] != '' ) ? " AND oi.obrid  = '{$_REQUEST['obrid']}'  " : "";
			$whereEsdidObr =( $_REQUEST["esdidobra"] != '') ? " AND wdobr.esdid  = '{$_REQUEST["esdidobra"]}' " : "";		
		
			
			$sql = "SELECT
						oi.obrid as obra, 
						ig.itgid as grupo,
						ore.repid as id,
						'(' || oi.obrid || ') ' || obrdesc || ' ' || CASE WHEN oc.covid IS NOT NULL THEN '( n� do conv�nio:'|| covnumero || ')' 
						ELSE (CASE WHEN numconvenio is not null THEN numconvenio 
						ELSE '( n� do conv�nio: N�o Informado )' END) END AS obrdesc,
						obrqtdconstruida || ' ' || umdeesc as obra_qtd_construida ,
						ig.gpdid as grupo_obra,
						tm.estuf as uf,
						tm.mundescricao as municipio,
						ee.entnome,
						orgdesc,
						stodesc,
						oi.orgid,
						obrpercexec as percentual,
						CASE WHEN gd.epcid is not null THEN ee1.entnome ELSE 'N�o Informada' END as empresa
					FROM
						obras.itemgrupo ig
					INNER JOIN
						obras.repositorio ore ON ore.repid = ig.repid
					INNER JOIN
						obras.obrainfraestrutura oi ON oi.obrid = ore.obrid
													AND oi.obsstatus = 'A'
					INNER JOIN
						obras.unidademedida ou ON ou.umdid = oi.umdidobraconstruida
					INNER JOIN
						entidade.entidade ee ON ee.entid = oi.entidunidade
					INNER JOIN
						entidade.endereco ed ON ed.endid = oi.endid
					INNER JOIN
						territorios.municipio tm ON tm.muncod = ed.muncod
					INNER JOIN
						obras.orgao oo ON oo.orgid = oi.orgid
					INNER JOIN
						obras.situacaoobra so ON so.stoid = oi.stoid
													AND so.stostatus = 'A'
					LEFT JOIN 
						( SELECT max(frrid), obrid, covid FROM obras.formarepasserecursos GROUP BY obrid, covid ) of ON of.obrid = oi.obrid
					LEFT JOIN
						obras.conveniosobra oc ON oc.covid = of.covid
													AND oc.covstatus = 'A'	
					LEFT JOIN
						obras.grupodistribuicao gd ON gd.gpdid = ig.gpdid
													AND gd.gpdstatus = 'A'
					LEFT JOIN
						obras.empresacontratada ec ON ec.epcid = gd.epcid 
					LEFT JOIN
						entidade.entidade ee1 ON ee1.entid = ec.entid							
					LEFT JOIN
						workflow.documento wd ON wd.docid = gd.docid
					LEFT JOIN
						workflow.estadodocumento we ON we.esdid = wd.esdid
					LEFT JOIN
						workflow.documento wdobr ON wdobr.docid = oi.docid		
					WHERE
						 repstatus = 'A'
						$whereGpdid
						$whereEpcid   
						$whereEsdid 
						$whereOrgid 
						$whereEntid 
						$whereEstcod
						$whereMunid 
						$whereObrid
						$whereEsdidObr 
 					ORDER BY 
						tm.estuf ASC, 
						tm.mundescricao ASC ";
			
			return $sql;
		}		

		function monta_agrupador(){
	
			$agp = array(
						"agrupador" => array(),
						"agrupadoColuna" => array("empresa","grupo_obra","obra_qtd_construida","entnome","orgdesc","stodesc","percentual")
						);
						
			array_push($agp['agrupador'], array(
												"campo" => "uf",
										  		"label" => "UF")										
								   				);
			array_push($agp['agrupador'], array(
												"campo" => "municipio",
										  		"label" => "Munic�pio")										
								   				);
			array_push($agp['agrupador'], array(
												"campo" => "obrdesc",
										  		"label" => "Obra")										
								   				);
						
			return $agp;
			
		}

		function monta_coluna(){
			
			$coluna = array();
			
					
			array_push( $coluna, array("campo" 	  => "empresa",
							   		   "label" 	  => "Empresa",
							   		   "type"	  => "string") );
			
			array_push( $coluna, array("campo" 	  => "grupo_obra",
							   		   "label" 	  => "N�mero do Grupo",
							   		   "type"	  => "string") );
			
			array_push( $coluna, array("campo" 	  => "obra_qtd_construida",
							   		   "label" 	  => "�rea Constru�da",
							   		   "type"	  => "string") );
					
			array_push( $coluna, array("campo" 	  => "entnome",
							   		   "label" 	  => "Unidade",
							   		   "type"	  => "string") );
					
			array_push( $coluna, array("campo" 	  => "orgdesc",
							   		   "label" 	  => "Tipo de Ensino",
							   		   "type"	  => "string") );

			array_push( $coluna, array("campo" 	  => "stodesc",
							   		   "label" 	  => "Situa��o da Obra",
							   		   "type"	  => "string") );
					
			array_push( $coluna, array("campo" 	  => "percentual",
							   		   "label" 	  => "% Executado",
							   		   "type"	  => "string") );
			
			return $coluna;
		}		
		
		// Inclui componente de relat�rios
		include APPRAIZ. 'includes/classes/relatorio.class.inc';
		
		// instancia a classe de relat�rio
		$rel = new montaRelatorio();
		
		// monta o sql, agrupador e coluna do relat�rio
		$sql       = monta_sql(); //dbg($sql,1);
		$agrupador = monta_agrupador();
		$coluna    = monta_coluna();
		$dados 	   = $db->carregar( $sql );
		
		$rel->setAgrupador($agrupador, $dados); 
		$rel->setColuna($coluna);
		$rel->setTolizadorLinha(false);
		$rel->setMonstrarTolizadorNivel(true);

		$rel->setEspandir(false);
		
		echo $rel->getRelatorio();
		
	} else { 
		$supervisao->obrListaGrupos( $filtros );
?>
	<table class="tabela" bgcolor="#f5f5f5" cellSpacing="1" cellPadding=3 align="center">
		<tr bgcolor="#D0D0D0">
			<td>
				<input type="button" value="Criar Grupo" onclick="location.href='?modulo=principal/supervisao/criarLote&acao=A&requisicao=novo';" style="cursor: pointer;"/>
				<input type="button" value="Voltar" onclick="history.back(-1);" style="cursor: pointer;"/>
			</td>
		</tr>
<?php }?>
	</table>
