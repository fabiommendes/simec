<?php

/*
	Sistema Simec
	Setor respons�vel: SPO-MEC
	Desenvolvedor: Equipe Consultores Simec
	Analista: Cristiano Cabral (cristiano.cabral@gmail.com)
	Programador: Renan de Lima Barbosa (renandelima@gmail.com)
	M�dulo: importarDadosCamara.inc
	Finalidade: Submiss�o de dados oriundos da C�mara
*/

if ( $_REQUEST['form']  )
{
	// define dados para a importa��o
	$nome = date( 'Ymd' ) . 'csv';
	$ano = isset( $_REQUEST['ano'] ) ? $_REQUEST['ano'] : date( 'Y' );
	$tipo = $_REQUEST['tipo'] == 'pl' ? 'pl' : 'ex';
	
	if ( $_FILES["arquivo_csv"]["tmp_name"] != '' )
	{
		// realiza upload de arquivo a ser importado
		$origem = $_FILES["arquivo_csv"]["tmp_name"];
		//$destino = APPRAIZ . 'www/temp/' . $nome;
		$destino = '/tmp/' . $nome;
		move_uploaded_file( $origem, $destino );
		
		// abre popup que realiza importa��o
		?>
			<script type="text/javascript">
				window.open(
					'financeiro.php' +
						'?modulo=sistema/comunica/importarDadosCamara' +
						'&acao=R' +
						'&nome=<?= urlencode( $nome ) ?>' +
						'&ano=<?= urlencode( $ano ) ?>' +
						'&tipo=<?= urlencode( $tipo ) ?>',
					'importacao',
					'width=450,height=200,status=1,toolbar=0,scrollbars=1,resizable=1'
				);
				window.location = 'financeiro.php?modulo=sistema/comunica/importarDadosCamaraInput&acao=C';
			</script>
		<?php
	}
}

include APPRAIZ . "includes/cabecalho.inc";
?>
<br/>
<? monta_titulo( 'Importar Dados C�mara', '' ); ?>
<form name="formulario" enctype="multipart/form-data" method="post">
	<input type="hidden" name="form" value="1" />
	<table class="tabela" cellSpacing="1"  cellPadding="3" bgcolor="#f5f5f5" align="center" >
		<tr>
			<!-- arquivo -->
			<td width="20%" align="right" class="SubTituloDireita" >Arquivo</td>
			<td width="80%"><input type="file" name="arquivo_csv" /></td>
		</tr>
		<tr>
			<!-- ano -->
			<td width="20%" align="right" class="SubTituloDireita" >Ano</td>
			<td width="80%">
				<select name="ano">
					<?php foreach ( range( 2005, date( 'Y' ) ) as $anoAtual ) : ?>
						<option value="<?= $anoAtual ?>" <?= $ano == $anoAtual || ( !$ano && $anoAtual == date( 'Y' ) ) ? 'selected="selected"' : '' ?>><?= $anoAtual ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<!-- tipo -->
			<td width="20%" align="right" class="SubTituloDireita" >Tipo</td>
			<td width="80%">
				<!-- projeto de lei -->
				<input type="radio" name="tipo" id="pl" value="pl" class="normal" <?= $tipo == 'pl' ? 'checked="checked"' : '' ?>/>
				<label for="pl">Projeto de Lei</label>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<!-- execucao orcamentaria -->
				<input type="radio" name="tipo" id="ex" value="ex"  class="normal" <?= $tipo == 'ex' || !$tipo ? 'checked="checked"' : '' ?>/>
				<label for="ex">Execu��o Or�ament�ria</label>
			</td>
		</tr>
		<tr>
			<td width="20%" align="right" class="SubTituloDireita">&nbsp;</td>
			<td width="80%">
				<input type="submit" name="Importar" value="Importar" />
			</td>
		</tr>
	</table>
</form>



