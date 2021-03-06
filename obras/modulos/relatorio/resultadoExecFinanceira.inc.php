<?php

ini_set("memory_limit", "1024M");

// Inclui componente de relat�rios
include APPRAIZ. 'includes/classes/relatorio.class.inc';

// instancia a classe de relat�rio
$rel = new montaRelatorio();

// monta o sql, agrupador e coluna do relat�rio
$sql       = obrasSqlExecFinanceira();
$agrupador = obrasAgpExecFinanceira();
$coluna    = obrasColunaExecFinanceira();
$dados 	   = $db->carregar( $sql );

$rel->setAgrupador($agrupador, $dados); 
$rel->setColuna($coluna);
$rel->setTotNivel(true);

// Gera o XLS do relat�rio
if ( $_REQUEST['tipoRelatorio'] == 'xls' ){
	ob_clean();
    $nomeDoArquivoXls = 'relatorio';
    echo $rel->getRelatorioXls();
    die;
}

?>

<html>
	<head>
		<title><?php echo $GLOBALS['parametros_sistema_tela']['nome_e_orgao']; ?></title>
		<link rel="stylesheet" type="text/css" href="../includes/Estilo.css">
		<link rel="stylesheet" type="text/css" href="../includes/listagem.css">
	</head>
	<body>
		<center>
			<!--  Cabe�alho Bras�o -->
			<?php echo monta_cabecalho_relatorio( '95' ); ?>
		</center>
		
		<!--  Monta o Relat�rio -->
		<? echo $rel->getRelatorio(); ?>
		
	</body>
</html>
