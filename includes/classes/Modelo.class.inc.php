<?php
/*
 * Classe Modelo
 * Classe pai para as classes espelhos de banco
 * @author Orion Teles de Mesquita
 * @since 12/02/2009
 */
class Modelo extends cls_banco{

	protected $tabelaAssociativa = false;
	
	public function __construct( $id = null ){
		
		parent::__construct();
		
		if( $id ){
			$this->carregarPorId( $id );
		}
	}
		
	public function __destruct(){
		
		if( isset($_SESSION['transacao'] ) ){
			pg_query( $this->link, 'rollback; ');
			unset( $_SESSION['transacao'] );
		}
		
	}
	
	/**
	 * Fun��o __get
	 * M�todo m�gico do PHP. Recupera o valor do atributo da classe caso n�o haja m�todo get implementado.
	 * @param string $stAtributo - Nome do atributo que se deseja retornar
	 * @return string - Retorna o valor do atributo
	 * @access public
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 * @final
	 */
    final public function __get( $stAtributo ){
    	
    	if( property_exists( get_class( $this ), $stAtributo ) ) return $this->$stAtributo;
		elseif( array_key_exists( $stAtributo, $this->arAtributos ) ) return $this->arAtributos[$stAtributo];
		else trigger_error( "O atributo n�o existe!" ); 
    }

    /**
	 * Fun��o __set
	 * M�todo m�gico do PHP. Seta o valor do atributo da classe caso n�o haja m�todo set implementado.
	 * @param string $stAtributo - Nome do atributo que se deseja setar
	 * @param string $valor - Valor do atributo
	 * @access public
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 * @final
	 */
    final public function __set( $stAtributo, $valor ){
    	
    	if( array_key_exists( $stAtributo, $this->arAtributos ) ) $this->arAtributos[$stAtributo] = $valor;
		else trigger_error( "O atributo n�o existe!" ); 
    }
    
	public function antesSalvar(){ return true; }
	
	public function depoisSalvar(){ return true; }
  
    /**
	 * Fun��o salvar
	 * M�todo usado para inser��o ou altera��o de um registro do banco
	 * @return int|bool - Retorna um inteiro correspondente ao resultado ou false se hover erro 
	 * @access public
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 */
	public function salvar( $boAntesSalvar = true, $boDepoisSalvar = true, $arCamposNulo = array() ){
    	
		if( $boAntesSalvar ){
			if( !$this->antesSalvar() ){ return false; }
		}
		 
		if( count( $this->arChavePrimaria ) > 1 ) trigger_error( "Favor sobreescrever m�todo na classe filha!" );
		
		$stChavePrimaria = $this->arChavePrimaria[0];
		
		if( $this->$stChavePrimaria && !$this->tabelaAssociativa ){
			$resultado = $this->alterar($arCamposNulo);
		}
		else{
			$resultado = $this->inserir();
		}
		if( $resultado ){
			if( $boDepoisSalvar ){
				$this->depoisSalvar(); 
			}
		}
		return $resultado;
	} // Fim salvar()	

	/**
	 * Fun��o _inserir
	 * M�todo usado para inser��o de um registro do banco
	 * @return int|bool - Retorna um inteiro correspondente ao resultado ou false se hover erro 
	 * @access private
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 */
	public function inserir(){
	        
		if( count( $this->arChavePrimaria ) > 1 ) trigger_error( "Favor sobreescrever m�todo na classe filha!" );
		
		$arCampos  = array();
		$arValores = array();
		$arSimbolos = array();
		
		foreach( $this->arAtributos as $campo => $valor ){
			
			if( $campo == $this->arChavePrimaria[0] && !$this->tabelaAssociativa ) continue;
			if( $valor !== null ){
				$arCampos[]  = $campo;
				$arValores[] = trim( pg_escape_string( $valor ) );
			}
		}
		
		if( count( $arValores ) ){
			$sql = " insert into $this->stNomeTabela ( ". implode( ', ', $arCampos   ) ." ) 
											  values ( '". implode( "', '", $arValores ) ."' ) 
					 returning {$this->arChavePrimaria[0]}";
			
			$stChavePrimaria = $this->arChavePrimaria[0];
			return $this->$stChavePrimaria = $this->pegaUm( $sql );
		}
	} // Fim _inserir()	
	
	/**
	 * Fun��o _alterar
	 * M�todo usado para altera��o de um registro do banco
	 * @return int|bool - Retorna um inteiro correspondente ao resultado ou false se hover erro 
	 * @access private
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 */	
	public function alterar($arCamposNulo = array()){
		if( count( $this->arChavePrimaria ) > 1 ) trigger_error( "Favor sobreescrever m�todo na classe filha!" );
		
		$campos = "";
		foreach( $this->arAtributos as $campo => $valor ){
			if( $valor !== null ){
				if( $campo == $this->arChavePrimaria[0] ){
					$valorCampoChave = $valor;
					continue;
				} 

				$valor = pg_escape_string( $valor );
				
				$campos .= $campo." = '".$valor."', ";
			}
			else{
				if(in_array($campo, $arCamposNulo)) {
					$campos .= $campo." = null, ";
				}
			}
		}
		
		$campos = substr( $campos, 0, -2 ); 
		
		$sql = "UPDATE $this->stNomeTabela SET $campos WHERE {$this->arChavePrimaria[0]} = $valorCampoChave ";
		
		return $this->executar( $sql );
	} // Fim _alterar()	
	
	/**
	 * Fun��o antesExcluir
	 * M�todo usado para fazer alguma funcionalidade antes da exclus�o dos dados
	 * @return bool - Retorna true se n�o houve nada para impedir a exclus�o e false caso contr�rio 
	 * @access public
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 */
	public function antesExcluir( $id = null ){ return true; }
	
	/**
	 * Fun��o excluir
	 * M�todo usado para excluir registro do banco
	 * @param int $id - Identificador do registro a ser exclu�do ( se n�o for passado valor, exclui-se o registro do objeto carregado )
	 * @return int|bool - Retorna um inteiro correspondente ao resultado ou false se hover erro 
	 * @access public
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 */	
	public function excluir( $id = null, $retorno = null ){
		$complemento = ";";
		if( count( $this->arChavePrimaria ) > 1 ) trigger_error( "Favor sobreescrever m�todo na classe filha!" );
		
		if($retorno){
			$complemento = "returning $retorno;";
		}
		
		if( !$this->antesExcluir($id) ) return false;
		
		$stChavePrimaria = $this->arChavePrimaria[0];
		$id = $id ? $id : $this->$stChavePrimaria;
		
		$sql = " DELETE FROM $this->stNomeTabela WHERE $stChavePrimaria = $id $complemento";
		if($retorno){
			return $this->pegaUm( $sql );
		}else{
			return $this->executar( $sql );
		}
	}
	
	/**
	 * Fun��o carregarPorId
	 * M�todo usado para carregar um Objeto pelo ID
	 * @param int $id - Identificador do objeto a ser carregado 
	 * @access public
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 */		
	public function carregarPorId( $id ){
		
		$sql = " SELECT * FROM $this->stNomeTabela WHERE {$this->arChavePrimaria[0]} = $id; ";

		$arResultado = $this->pegaLinha( $sql );
		$this->popularObjeto( array_keys( $this->arAtributos ), $arResultado );
		
	}
	
	/**
	 * Fun��o recuperarTodos
	 * M�todo usado para recuperar todos os registros do banco, seguinto par�metros
	 * @param string $stCampos - String contendo nomes dos campos a serem carregados 
	 * @param array $arClausulas - Array contendo dados da cl�usula where 
	 * @param string $stOrdem - String contendo dados da cl�usula ordey by 
	 * @example - $obNomeObjeto->recuperarTodos( 'campo1, campo2', array( "id = 123" ), nome )
	 * @example - $obNomeObjeto->recuperarTodos() --> Se n�o passar par�metros reconhece como todos os registros do banco. 
	 * @access public
	 * @author Orion Teles de Mesquita
	 * @since 12/02/2009
	 */		
	public function recuperarTodos( $stCampos, $arClausulas = null, $stOrdem = null ){
		
		$sql = " SELECT $stCampos FROM $this->stNomeTabela ";
		$sql .= $arClausulas ? " WHERE ". implode( " AND ", $arClausulas ) : "";
		$sql .= $stOrdem ? " ORDER BY $stOrdem" : "";
		
		$resultado = $this->carregar( $sql );
		
		return $resultado ? $resultado : array();
		
	}

	public function popularObjeto( $arCampos, $arDados = null ){
		
		$arDados = $arDados ? $arDados : $_REQUEST;
		
		foreach( $arCampos as $campo ){
			
			if( key_exists( $campo, $arDados ) ){
				$this->$campo = $arDados[$campo];
			}
		}
	}
	
	public function popularDadosObjeto( $arDados = null ){
		
		$arDados = $arDados ? $arDados : $_REQUEST;
		foreach( $this->arAtributos as $campo => $valor){
			
			if( key_exists( $campo, $arDados ) ){
				$this->$campo = $arDados[$campo];
			
			}
		}
	}

	public function getDados(){
		return $this->arAtributos;
	}

	function lista($coluna = null, Array $arParamWhere = null, Array $arParamJoin = null, Array $arParam = null){
		$where 		  = "";
		$arJoin	  	  = array();		
		$coluna  	  = implode(', ', (is_null($coluna) ? array_keys($this->arAtributos) : ((array) $coluna)) );
		// Parametros auxiliares
		$alias = $arParam['alias'];
		$order = $arParam['order'] ? " ORDER BY {$arParam['order']}" : "";
		
		// Separa os JOINs (inner, left)
		$arInner = is_array($arParamJoin['inner']) ? $arParamJoin['inner'] : array();
		$arLeft  = is_array($arParamJoin['left']) ? $arParamJoin['left'] : array();

		// Monta os INNER JOINs
		foreach($arInner as $tabela => $on){
			$arJoin[] = 'JOIN ' . $tabela . ' ON (' . $on . ')';
		}
		// Monta os LEFT JOINs
		foreach($arLeft as $tabela => $on){
			$arJoin[] = 'LEFT JOIN ' . $tabela . ' ON (' . $on . ')';
		}
		
		// Monta string WHERE
		$where = (count( $arParamWhere ) > 0 ? "WHERE (" . implode(') AND (', $arParamWhere) . ")" : "");
		// Monta string JOIN
		$join  = count($arJoin) ? implode(' ', $arJoin) : '';	
			
		$sql = "SELECT 
				   {$coluna}
			  	FROM 
			  	   {$this->stNomeTabela} $alias   
			  	{$join}
			  	{$where}
			  	{$order};";
//		dbg($sql, 1);	  	
		return $this->carregar($sql);
	}	
}
