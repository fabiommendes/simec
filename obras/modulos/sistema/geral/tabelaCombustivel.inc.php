<? 
$obComb = new CombustivelController();

//$_REQUEST['evento'] = 'salvar';
//$_POST['cbtid'] = 2;
//$_REQUEST['estuf'] = "DF";
//$_REQUEST['cbtvalor'] = "2,38";

switch ($_REQUEST['evento']){
	case 'salvar':
		ob_clean();
		$retorno = $obComb->salvar($_POST['cbtid']);
		echo "<retornoOperacao>";
		echo json_encode( $retorno );
		echo "</retornoOperacao>";
		echo "<iniciolista>";
		$obComb->listaCombustivel();
		echo "</fimlista>";
		echo "<selectEstuf>";
		$db->monta_combo( 'estuf', $obComb->buscaDadosUF(), 'S', 'Selecione...', '', '', '', 100, '', 'estuf' );
		echo "</selectEstuf>";
		die;
	break;
	case 'editar':
		ob_clean();
		$retorno = $obComb->editar( $_POST['cbtid'] );
		echo "<retornoOperacao>";
		echo json_encode( $retorno );
		echo "</retornoOperacao>";		
		echo "<selectEstuf>";
		$estuf = $_POST['estuf'];
		$db->monta_combo( 'estuf', $obComb->buscaDadosUF( array("estuf" => $_POST['estuf']) ), 'N', 'Selecione...', '', '', '', 100, '', 'estuf' );
		echo "</selectEstuf>";
		die;
	break;
	case 'excluir':
		ob_clean();
		$retorno = $obComb->excluir($_POST['cbtid']);
		echo "<retornoOperacao>";
		echo json_encode( $retorno );
		echo "</retornoOperacao>";
		echo "<iniciolista>";
		$obComb->listaCombustivel();
		echo "</fimlista>";
		echo "<selectEstuf>";
		$db->monta_combo( 'estuf', $obComb->buscaDadosUF(), 'S', 'Selecione...', '', '', '', 100, '', 'estuf' );
		echo "</selectEstuf>";
		die;
	break;
}

?>
<script type="text/javascript" src="../includes/JQuery/jquery2.js"></script>
<script type="text/javascript">
function validaForm(){
	var msg 	= '';
	var retorno = true;
	
	if ( jQuery('[name=estuf]').val() == '' ){
		msg += 'O campo "UF" � obrigat�rio \n';
	}
	
	if ( jQuery('[name=cbtvalor]').val() == '' ){
		msg += 'O campo "Pre�o" � obrigat�rio \n';
	}
		
	if (msg != ''){
		alert(msg);
		retorno = false;
	}
	return retorno;
}

function salvar(){
	if ( validaForm() ){
		divCarregando();
		var tableList;
		var param = {"tabelas"  : "precocombustivel",
					 "evento"   : "salvar",
					 "cbtid"    : jQuery('[name=cbtid]').val(),
					 "cbtvalor" : jQuery('[name=cbtvalor]').val(),
					 "estuf"    : jQuery('[name=estuf]').val()};
					 
		jQuery.ajax({url   	 : "?modulo=sistema/geral/tabelas_de_apoio&acao=A", 
					 type	 : "POST",
					 async 	 : false, 
					 data  	 : param,
					 success : function (txtRetorno){
					 				var retorno;
					 				retorno = (pegaRetornoAjax('<retornoOperacao>', '</retornoOperacao>', txtRetorno, true));
					 				retorno = eval( retorno );
					 				if ( retorno ){										
						 				var html = pegaRetornoAjax('<iniciolista>', '</fimlista>', txtRetorno);
										jQuery('#listaCombustivel').html( html );
										selectEstuf = (pegaRetornoAjax('<selectEstuf>', '</selectEstuf>', txtRetorno, true));
				 						jQuery('#comboEstuf').html( selectEstuf );	
//										// Retira o estado que est� sendo salvo.
//										jQuery('[name=estuf] option:selected').remove();
										// Limpa Campos para nova Inser��o
										jQuery('[name=cbtid]').val("");
						 				jQuery('[name=cbtvalor]').val("");
						 				jQuery('[name=estuf]').val("");
						 				
										alert('Opera��o Realizada com Sucesso!');
					 				}else{
										alert('Falha, opera��o n�o realizada!');
					 				}
					 				
					 				
					 				
					}});
		divCarregado();			 			
	}
}

function excluirCombustivel(cbtid, estuf){
	if ( confirm('Deseja apagar o pre�o de combust�vel do "' + estuf +'".') ){
		divCarregando();
		var param = {"tabelas"  : "precocombustivel",
					 "evento"   : "excluir",
					 "cbtid"    : cbtid,
					 "estuf"    : estuf};
					 
		jQuery.ajax({url   	 : "?modulo=sistema/geral/tabelas_de_apoio&acao=A", 
					 type	 : "POST",
					 async 	 : false, 
					 data  	 : param,
					 success : function (txtRetorno){
					 				var retorno;
					 				retorno = (pegaRetornoAjax('<retornoOperacao>', '</retornoOperacao>', txtRetorno, true));
					 				retorno = eval( retorno );
					 				if ( retorno ){										
						 				var html = pegaRetornoAjax('<iniciolista>', '</fimlista>', txtRetorno);
										jQuery('#listaCombustivel').html( html );						 		
										selectEstuf = (pegaRetornoAjax('<selectEstuf>', '</selectEstuf>', txtRetorno, true));
				 						jQuery('#comboEstuf').html( selectEstuf );	
												
										alert('Opera��o Realizada com Sucesso!');
					 				}else{
										alert('Falha, opera��o n�o realizada!');
					 				}
					 				
					 				
					 				
					}});
		divCarregado();			 			
	}
}

function editarCombustivel(cbtid, estuf){
	divCarregando();
	var param = {"tabelas"  : "precocombustivel",
				 "evento"   : "editar",
				 "estuf"    : estuf,
				 "cbtid"    : cbtid};
				 
	jQuery.ajax({url   	 : "?modulo=sistema/geral/tabelas_de_apoio&acao=A", 
				 type	 : "POST",
				 async 	 : false, 
				 data  	 : param,
				 success : function (txtRetorno){
				 				var retorno;
				 				retorno     = (pegaRetornoAjax('<retornoOperacao>', '</retornoOperacao>', txtRetorno, true));
				 				eval( "var dado = " + retorno );
				 				selectEstuf = (pegaRetornoAjax('<selectEstuf>', '</selectEstuf>', txtRetorno, true));
				 				jQuery('#comboEstuf').html( selectEstuf );	
				 				
		 						jQuery('[name=cbtid]').val( dado.cbtid );
				 				jQuery('[name=cbtvalor]').val( mascaraglobal('[###.]###,##', dado.cbtvalor) );
//				 				jQuery('[name=estuf]').val( dado.estuf );				 				
				}});
	divCarregado();			 			
}

function submeteTabelasApoio() {
	var select = document.getElementById('tabelas');
			
	if(select.value != "") {
		document.getElementById('descricao_tabela').value = select.options[select.selectedIndex].innerHTML;
		document.getElementById('formTabelasApoio').submit();
	}
}


function redireciona(url){
	location.href = url;
}

function confirmExcluir(url, msg) {
	if(confirm(msg)) {
		window.location = url;
	}
} 

</script>

<form method="post" name="formTabelasApoio" id="formTabelasApoio" action="?modulo=sistema/geral/tabelas_de_apoio&acao=A">
<input type="hidden" id="enviado" name="enviado" value="0">
	<table class="tabela" bgcolor="#f5f5f5" cellSpacing="1" cellPadding="3"	align="center">
		<tr>
			<td class="SubTituloDireita">Selecione a tabela:</td>
			<td>
				<select class="CampoEstilo" id="tabelas" name="tabelas" onChange="submeteTabelasApoio();">
					<option value="">-- Selecione uma Tabela --</option>
					<option value="itenscomposicao">Etapas da Obra</option>
					<option value="tiporestricao">Tipo de Restri��o</option>
					<option value="tiporespcontato">Tipo de Respons�vel</option>
					<option value="tipoobra">Tipo de Obra</option>
					<option value="unidademedida">Unidade de Medida</option>
					<option value="tipoarquivo">Tipo de Arquivo</option>
					<option value="itensdetalhamento">M�dulo de Amplia��o</option>
					<option value="situacaoobra">Situa��o da Obra</option>
					<option value="desempenhoconstrutora">Desempenho da Construtora</option>
					<option value="qualidadeobra">Qualidade da Obra</option>
					<option value="programafonte">Programa Fonte</option>
					<option value="tipologiaobra">Tipologia da Obra</option>
					<option value="programatipologia">Programa / Tipologia</option>
					<option value="precocombustivel" selected="selected">Tabela de Pre�o do Combust�vel</option>
				</select>
				<input type="hidden" id="descricao_tabela" name="descricao_tabela">
			</td>
		</tr>
	</table>
</form>

<form method="POST"  name="telaFormulario"> 
<input type="hidden" name="evento" value="salvar">
<input type="hidden" name="cbtid" value="<?=$cbtid ?>">
<input type="hidden" name="tabelas" value="<?=$_REQUEST['tabelas'] ?>">

<table  class="tabela" bgcolor="#f5f5f5" cellSpacing="1" cellPadding="3" align="center">
	<tr>
		<td colspan="2">Tabela de Pre�o de Combust�vel</td>
	</tr>
	<tr bgcolor="#CCCCCC" align="center">
		<td style="padding-left:30px;">
		<b>UF:<b>
		<span id="comboEstuf">
		<? 	
		$db->monta_combo( 'estuf', $obComb->buscaDadosUF(), 'S', 'Selecione...', '', '', '', 100, '', 'estuf' ); 
		?>
		</span>
		<b style="padding-left: 20px;">Pre�o:</b>
		<?= campo_texto( 'cbtvalor', 'S', 'S', '', 10, 11, '[###.]###,##', '', 'left', '', 0, '', '' ); ?>		
		<input type="button" name="btalterar" value="Salvar" class="botao" style="margin-left: 30px;" onclick="salvar();">
		</td>
	</tr>
</table>
</form>
<table class="tabela" bgcolor="#f5f5f5" cellSpacing="1" cellPadding="3"	align="center">
	<tr>
		<td>
			<fieldset style="background: #ffffff;">
				<legend>Lista de Pre�o de Combust�vel</legend>
				<div id="listaCombustivel" style="height: 340px; overflow: auto;">
					<? $obComb->listaCombustivel(); ?>
				</div>
			</fieldset>
		</td>
	</tr>
</table>