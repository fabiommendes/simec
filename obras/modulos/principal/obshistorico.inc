<?php

if ( !empty($_REQUEST["hprid"]) ){
	
	$sql = "";
	$sql = "SELECT hprobs FROM obras.historicoparalisacao WHERE hprid = {$_REQUEST["hprid"]}";
	$hprobs = $db->pegaUm( $sql );
}

?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../includes/Estilo.css"/>
		<link rel='stylesheet' type='text/css' href='../includes/listagem.css'/>
	</head>
	<body>
		<table class="tabela" bgcolor="#f5f5f5" cellSpacing="1" cellPadding="3" align="center">
			<tr>
				<td colspan="2"><center><b>Hist�rico de Paralisa��es</b></center></td>
			</tr>
			<tr>
				<td class="subtitulodireita">Observa��o</td>
				<td>
					<?php echo campo_textarea( 'hprobs', 'N', 'N', '', '64', '13', '1000'); ?>
				</td>
			</tr>
			<tr bgcolor="#C0C0C0">
				<td></td>
				<td>
					<input type="button" value="Fechar" onclick="closeMessage();" style="cursor:pointer;"/>
				</td>
			</tr>
		</table>
	</body>
</html>