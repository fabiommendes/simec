<?php
class CombustivelController extends Controller{	

	function buscaDadosUF(Array $param = null){
		// COLUNAs a serem retornadas da query				    
		$arCol = array(
						"e.estuf AS codigo",
						"e.estuf AS descricao"
					  );
		
		if ($param['estuf']){
			$whereComp = " OR estuf = '{$param['estuf']}'";
		}
		// WHERE - filtros necess�rios			  
		$arWhere = array("e.estuf NOT IN (SELECT estuf FROM obras.combustivel) {$whereComp}");
		
		// Instancia do MODEL que ser� utilizada
		$obModel = new Estado();
		$arDados = $obModel->lista($arCol, $arWhere, $join, array('alias' => 'e') );			 
		
		return ($arDados ? $arDados : array());
	}

	function listaCombustivel(){
		/*
		 * IN�CIO - MODEL
		 */		
		$obModel = new Combustivel();
		$arDados = $obModel->listaCombustivel(); 
		/*
		 * FIM - MODEL
		 */				    
		/*
		 * IN�CIO - VIEW
		 */
		// CABE�ALHO da lista
		$arCabecalho = array(
								"A��o",
								"Estado",
								"Pre�o (R$)"
						    );
						    
		// A��O que ser� posta na primeira coluna de todas as linhas
		$acao = "<center>
				   <img src='../imagens/alterar.gif' title='Editar Pre�o Combust�vel' style='cursor:pointer' onclick='editarCombustivel({cbtid}, \"{estuf}\");'>
				   <img src='../imagens/excluir.gif' title='Excluir Pre�o Combust�vel' style='cursor:pointer; margin-left:3px' onclick='excluirCombustivel({cbtid}, \"{estuf}\");'>
				 <center>";
		
		// parametros que cofiguram as colunas da lista, a ordem do array equivale a ordem do cabe�alho 
		$arParamCol[0] = array("align" => "center");
		
		// ARRAY de parametros de configura��o da tabela
		$arConfig = array("style" => "width:90%;");
		
		$a = new Lista($arConfig);
		$a->setCabecalho( $arCabecalho );
		$a->setCorpo( $arDados, $arParamCol );
		$a->setAcao( $acao );
		$a->show();			
		/*
		 * FIM - VIEW
		 */		
	}
	
	function salvar($cbtid = null){
		$obModel = new Combustivel( $cbtid );
		$arCampos = array("estuf", "cbtvalor");
		$obModel->popularObjeto( $arCampos );
		$retorno = $obModel->salvar();
		$obModel->commit();
		
		if ( gettype( $retorno ) == "resource" ){
			$retorno = true;
		}
		
		return $retorno;
	}
	
	function excluir($cbtid){
		$obModel = new Combustivel();
		$retorno = $obModel->excluir( $cbtid, "cbtid" );
		$obModel->commit();
		
		return $retorno;
	}

	function editar( $cbtid ){
		$obModel = new Combustivel( $cbtid );
		$retorno = $obModel->getDados();
		
		return $retorno;
	}
}
?>