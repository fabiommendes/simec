<?php

include_once '_funcoes.inc';

$exibe_coluna_ins = true;
$exibe_coluna_met = true;
$exibe_coluna_int = true;
$exibe_coluna_orc = true;
$exibe_coluna_con = true;
$exibe_nivel = 2;
$orc_agrupar = true;
$orc_inicio = 2007;
$orc_fim = 2011;
$dados = array();
if ( isset( $_SESSION['atividades_pde'] ) )
{
	$dados = $_SESSION['atividades_pde'];
}
if ( isset( $_REQUEST['filtro'] )  )
{
	$dados = $_REQUEST;
}
if ( count( $dados ) )
{
	$dados['coluna_visivel'] = (array) $dados['coluna_visivel'];
	$exibe_coluna_ins = in_array( 'ins', $dados['coluna_visivel'] );
	$exibe_coluna_met = in_array( 'met', $dados['coluna_visivel'] );
	$exibe_coluna_int = in_array( 'int', $dados['coluna_visivel'] );
	$exibe_coluna_orc = in_array( 'orc', $dados['coluna_visivel'] );
	$exibe_coluna_con = in_array( 'con', $dados['coluna_visivel'] );
	$exibe_nivel = (integer) $dados['nivel_visivel'];
	$orc_agrupar = (boolean) $dados['orc_agrupar'];
	$orc_inicio = (integer) $dados['orc_inicio'];
	$orc_fim = (integer) $dados['orc_fim'];
	if ( $orc_inicio > $orc_fim )
	{
		$orc_inicio = $orc_fim;
	}
	$_SESSION['atividades_pde'] = $dados;
}

?>
	<?php require_once APPRAIZ . "includes/cabecalho.inc"; ?>
	<style>
		.coluna_medida { color:#333333; width:40%; cursor: poiter; }
		.coluna_data { color:#4488CC; }
		.coluna_meta { color:#008000; text-align: center; }
		.coluna_instrumento { color: #2277bb; text-align: left; font-size: 10px; }
		.coluna_orcamento { color:#3366CC; text-align: right; }
		.coluna_controle { text-align: center; color:#CC9933; }	
	</style>
	<script type="text/javascript">
		
		function armazenaNoCookieVisibilidadeAtividade( strIdAtividade , boolStatus )
		{
//			if( !window.finished )
			{
				return;
			}
			
//			alert( 'strIdAtividade: ' + strIdAtividade );
//			alert( 'boolStatus: ' + boolStatus );
			 
			// 1. Carregando o Array do Cookie , se existir //

			var objAtividadesAbertasCookie = new cookieElement( "atividadesAbertas" );
			var objAtividadesFechadasCookie = new cookieElement( "atividadesFechadas" );
			
			if( objAtividadesAbertasCookie.getValue() == undefined )
			{
				var arrAtividades = Array();
			}
			else
			{
				var arrAtividades = explode( ',' , objAtividadesAbertasCookie.getValue() );
			}
			
			if( objAtividadesFechadasCookie.getValue() == undefined )
			{
				var arrAtividadesFechadas = Array();
			}
			else
			{
				var arrAtividadesFechadas = explode( ',' , objAtividadesFechadasCookie.getValue() );
			}
			
			// 2. Alterando o Array em memoria a partir da mudanca da atividade //
		
			// 2.1 Atividades Abertas //
			
			if( array_search( strIdAtividade , arrAtividades ) == -1 )
			{
				if( boolStatus )
				{
					arrAtividades.push( strIdAtividade );
				}
			}
			else
			{
				if( !boolStatus )
				{
					arrAtividades.splice(  arrAtividades.indexOf( strIdAtividade )  , 1 );
				}
			}
			
			// 2.2 Atividades Fechadas //
			
			if( array_search( strIdAtividade , arrAtividadesFechadas ) == -1 )
			{
				if( !boolStatus )
				{
					arrAtividadesFechadas.push( strIdAtividade );
				}
			}
			else
			{
				if( boolStatus )
				{
					arrAtividadesFechadas.splice(  arrAtividadesFechadas.indexOf( strIdAtividade )  , 1 );
				}
			}  	
			  	
			// 3. Salvando o Array alterado no cookie //
			var strAtividadesAbertas	= implode( ',' , arrAtividades );
			var strAtividadesFechadas	= implode( ',' , arrAtividadesFechadas );
//			document.title = strAtividadesAbertas;
			objAtividadesAbertasCookie.setValue( strAtividadesAbertas );
//			objAtividadesFechadasCookie.setValue( strAtividadesFechadas );
		}
			
		/**
		 * Controla a visibilidade dos itens da �rvore.
		 */
//		var IE = document.all ? true : false;
		
		function atualizaAtividadesPeloCookie()
		{
			var objAtividadesAbertasCookie	= new cookieElement( "atividadesAbertas" );
			var objAtividadesFechadasCookie	= new cookieElement( "atividadesFechadas" );
//			document.title = objAtividadesAbertasCookie.getValue();
			return;
			if( objAtividadesAbertasCookie.getValue() != null )
			{ 
				var arrAtividadesAbertas	= explode( ',' , trim( objAtividadesAbertasCookie.getValue() ) );
			}
			else
			{
				var arrAtividadesAbertas	= Array();
			}
			
			if( objAtividadesFechadasCookie.getValue() != null )
			{
				var arrAtividadesFechadas	= explode( ',' , trim( objAtividadesFechadasCookie.getValue() ) );
			}
			else
			{
				var arrAtividadesFechadas	= Array();
			}
			
			for( var i = 0 ; i < arrAtividadesAbertas.length ; i++ )
			{
				if ( arrAtividadesAbertas[ i ] != '' )
				{
//					alert( "$" + arrAtividadesAbertas[ i ] + "$" );
					var objAtividade = document.getElementById( 'tr' + arrAtividadesAbertas[ i ] );
					if( objAtividade )
					{
						if( !IE )
						{
							objAtividade.style.display = "table-row";
						} 
						else
						{
							objAtividade.style.display = "block";
						}
					}
				}
			}
			for( var i = 0 ; i < arrAtividadesFechadas.length ; i++ )
			{
				if ( arrAtividadesFechadas[ i ] != '' )
				{
//					alert( "@" + arrAtividadesFechadas[ i ] + "@" );
					var objAtividade = document.getElementById( 'tr' + arrAtividadesFechadas[ i ] );
					if( objAtividade )
					{
						objAtividade.style.display = "none";
					}
				}
			}
		}
		
		function exibirOcultarAtividadesFilhas( atividade, imagem, origem ){
			var atividades = document.getElementById( 'atividades' ).getElementsByTagName( 'tr' );
			for( var i = 0; i < atividades.length ; ++i ) {
				if( atividades[i].getAttribute( 'parent' ) == atividade ) {
					if ( atividades[i].style.display == "none" ) {
						armazenaNoCookieVisibilidadeAtividade( atividades[i].id , true );
						if( !IE ) {
							atividades[i].style.display = "table-row";
						} else {
							atividades[i].style.display = "block";
						}
						if ( origem == true ) {
							imagem.src = imagem.src.replace( 'mais' , 'menos' );
						}
					} else {
						armazenaNoCookieVisibilidadeAtividade( atividades[i].id , false );
						atividades[i].style.display = "none";
						if ( origem == true ) {
							imagem.src = imagem.src.replace( 'menos' , 'mais' );
						}
					}
					var imagens = atividades[i].getElementsByTagName( 'img' );
					for( var j = 0; j < imagens.length ; ++j ) {
						if( imagens[j].getAttribute( 'atividade' ) != null && imagens[j].src.indexOf( 'menos' ) > 0 ) {
							exibirOcultarAtividadesFilhas( imagens[j].getAttribute( 'atividade' ), imagens[j], false );
						}
					}
				}
			}
		}
		
		function filtrarListagem() {
			selectAllOptions( document.getElementById( 'coluna_visivel' ) );
			document.filtro.submit();
		}
		
		function reiniciarFormulario(){
			document.filtro.reset();
		}
		
	</script>
	<br/>
	<?php if ($_REQUEST['idnivel']) 
					{$idnivel=$_REQUEST['idnivel'];} 
						else 
					{$idnivel=3;} ?>
	<?php $sql="select atidescricao from projetos.atividade where atiid = " . $idnivel; 
	      $titulo = $db->pegaum("select atidescricao from projetos.atividade where atiid=" . $idnivel);?>
	<?php monta_titulo( $titulo, '' ); ?>
	
	<script type="text/javascript" src="../../includes/JsLibrary/_start.js"></script>
	<script type="text/javascript" src="../../includes/JsLibrary/cookies/cookies.js"></script>
	
	<form name="filtro" action="" method="post">
		<input type="hidden" name="filtro" value="1"/>
		<table width="95%" cellspacing="0" cellpadding="0" border="0" align="center" class="tabela" style="color: rgb(51, 51, 51); border-botom:none;">
			<tr>
				<td width="1">
					<table cellspacing="0" cellpadding="0" border="0" align="center" style="color: rgb(51, 51, 51);">
						<tr bgcolor="#f0f0f0">
							<td align="center" bgcolor="#dcdcdc" align="center" width="50">
								Mostrar
							</td>
							<td width="10">&nbsp;</td>
							<td align="center" bgcolor="#dcdcdc" width="50">
								Ocultar
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<?php
								
									include APPRAIZ . 'includes/Agrupador.php';
									$invisivel = array();
									$visivel = array();
									
									$coluna_ins = array(
										'codigo'    => 'ins',
										'descricao' => 'Instrumento'
									);
									if ( !$exibe_coluna_ins ) {
										array_push( $invisivel, $coluna_ins );
									} else {
										array_push( $visivel, $coluna_ins );
									}
									
									$coluna_met = array(
										'codigo'    => 'met',
										'descricao' => 'Meta'
									);
									if ( !$exibe_coluna_met ) {
										array_push( $invisivel, $coluna_met );
									} else {
										array_push( $visivel, $coluna_met );
									}
				
									$coluna_int = array(
										'codigo'    => 'int',
										'descricao' => '�rg�os Participantes'
									);
									if ( !$exibe_coluna_int ) {
										array_push( $invisivel, $coluna_int );
									} else {
										array_push( $visivel, $coluna_int );
									}
				
									$coluna_orc = array(
										'codigo'    => 'orc',
										'descricao' => 'Or�amento'
									);
									if ( !$exibe_coluna_orc ) {
										array_push( $invisivel, $coluna_orc );
									} else {
										array_push( $visivel, $coluna_orc );
									}
				
									$coluna_con = array(
										'codigo'    => 'con',
										'descricao' => 'Controle'
									);
									if ( !$exibe_coluna_con ) {
										array_push( $invisivel, $coluna_con );
									} else {
										array_push( $visivel, $coluna_con );
									}
									
									$agrupador = new Agrupador( 'filtro' );
									$agrupador->setDestino( 'coluna_invisivel', null, $invisivel );
									$agrupador->setOrigem( 'coluna_visivel', null, $visivel );
									$agrupador->exibir();
								?>
							</td>
						</tr>
					</table>
				</td>
				<td style="padding: 0 0 0 30px;" bgcolor="#f0f0f0">
					<table align="left" border="0" cellpadding="3" cellspacing="1" height="100%" width="100%">
						<tr>
						<?php $sql = "select atiid as codigo, substr(numero ||' - '||atidescricao, 0, 40)||case when char_length(numero ||' - '||atidescricao)>39 then '...' else '' end as descricao  from(
									select trim(to_char(a1.atiordem,'99')) as numero, a1.atiid, a1.atidescricao, a1.atiid as atiidpai, a1.atiordem from projetos.atividade a1 where a1.atiidpai=3 and a1.atistatus='A'
									
									union all
									
									select '&nbsp;&nbsp;&nbsp;'||trim(to_char(a1.atiordem,'99'))||'.'||trim(to_char(a2.atiordem,'99')) as numero, a2.atiid, a2.atidescricao, a2.atiidpai, a2.atiordem from projetos.atividade a1 
									inner join projetos.atividade a2 on a2.atiidpai=a1.atiid
									where a1.atiidpai=3 and a1.atistatus='A' and a2.atistatus='A') as foo order by atiidpai, atiid, atiordem";?>
							<td bgcolor="#dcdcdc">
								<label for="orc_agrupar">N�vel Inicial</label>
							</td>
							<td style="padding: 0 20px 0 10px;">
								<?php $db->monta_combo( "idnivel", $sql, 'S', '0 - Raiz PDE', '', '' );?>
							</td>
						</tr>
					    <tr>
							<td bgcolor="#dcdcdc" width="150">N�veis Vis�veis</td>
							<td style="padding: 0 20px 0 10px;">
								<select name="nivel_visivel">
									<option value="0" <?php echo $exibe_nivel == 0 ? 'selected="selected"' : ''; ?>>
										&nbsp;&nbsp;0&nbsp;&nbsp;
									</option>
									<option value="1" <?php echo $exibe_nivel == 1 ? 'selected="selected"' : ''; ?>>
										&nbsp;&nbsp;1&nbsp;&nbsp;
									</option>
									<option value="2" <?php echo $exibe_nivel == 2 ? 'selected="selected"' : ''; ?>>
										&nbsp;&nbsp;2&nbsp;&nbsp;
									</option>
									<option value="3" <?php echo $exibe_nivel == 3 ? 'selected="selected"' : ''; ?>>
										&nbsp;&nbsp;3&nbsp;&nbsp;
									</option>
								</select>
							</td>
						</tr>
						<tr>
							<td bgcolor="#dcdcdc">Per�odo do Or�amento</td>
							<td style="padding: 0 20px 0 10px;">
								<select name="orc_inicio">
									<?php foreach ( range( 2007, 2011 ) as $ano ) : ?>
										<option value="<?php echo $ano; ?>"
										<?php echo $ano == $orc_inicio ? 'selected="selected"' : '';?>
										>
											<?php echo $ano; ?>
										</option>
									<?php endforeach; ?>
								</select>
								<select name="orc_fim">
									<?php foreach ( range( 2007, 2011 ) as $ano ) : ?>
										<option value="<?php echo $ano; ?>"
										<?php echo $ano == $orc_fim ? 'selected="selected"' : '';?>
										>
											<?php echo $ano; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td bgcolor="#dcdcdc">
								<label for="orc_agrupar">Agrupar Or�amento</label>
							</td>
							<td style="padding: 0 20px 0 10px;">
								<input type="checkbox" name="orc_agrupar" value="1" id="orc_agrupar"
								<?php echo $orc_agrupar ? 'checked="checked"' : '';?>
								/>
							</td>
						</tr>
					</table>
				</td>
				<td bgcolor="#dcdcdc" align="center" width="100">
					<input type="button" name="filtrar" value="Filtrar" onclick="filtrarListagem();"/>
				</td>
			</tr>
		</table>
	</form>
	<table id="atividades" width="95%" cellspacing="0" cellpadding="2" border="0" align="center" class="listagem" style="color: rgb(51, 51, 51);">
		<thead>
			<tr style="text-align: center">
				<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">n�</td>
				<!--
				<td valign="top" class="title notprint" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">A��es</td>
				<td valign="top" class="title notprint" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">Recuo</td>
				-->
				<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">Medida</td>
				<?php if ( $exibe_coluna_ins ) : ?>
					<td align="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">Instrumento</td>
				<?php endif; ?>
				<?php if ( $exibe_coluna_met ) : ?>
					<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">Meta</td>
				<?php endif; ?>
				<?php if ( $exibe_coluna_int ) : ?>
					<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">�rg�os participantes</td>
				<?php endif; ?>
				<?php if ( $exibe_coluna_orc ) : ?>
					<?php if ( !$orc_agrupar ) : ?>
						<?php foreach ( range( $orc_inicio, $orc_fim ) as $ano ) : ?>
							<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">
								<?php echo $ano; ?>
							</td>
						<?php endforeach ; ?>
					<?php else : ?>
						<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">
							<?php echo $orc_inicio; ?>-<?php echo $orc_fim; ?>
						</td>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( $exibe_coluna_con ) : ?>
					<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">Controle</td>
				<?php endif; ?>
				<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">In�cio</td>
				<td valign="top" class="title" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">Fim</td>
				<!--
				<td valign="top" class="title notprint" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(192, 192, 192); border-bottom: 1px solid rgb(192, 192, 192);">Ordem</td>
				-->
			</tr>
		</thead>
		<tbody>
			<?php
				$raiz = array(
					'atiid' => $idnivel
				);
				$sql = sprintf( "select * from projetos.atividade where atiid=%d", $idnivel );
				$raiz = $db->carregar( $sql );
				if( $raiz[0]['atiidpai'] ){
					foreach ( $raiz as $registro ) {
						exibir_registro_consulta(
							$registro,
							$orc_agrupar,
							$orc_inicio,
							$orc_fim,
							0,
							'',
							count( $filhos ),
							$exibe_nivel,
							$exibe_coluna_ins,
							$exibe_coluna_met,
							$exibe_coluna_int,
							$exibe_coluna_orc,
							$exibe_coluna_con,
							false
						);
					}
				} else {
					$filhos = pegar_filhos( $raiz[0] );
					foreach ( $filhos as $registro ) {
						exibir_registro_consulta(
							$registro,
							$orc_agrupar,
							$orc_inicio,
							$orc_fim,
							0,
							'',
							count( $filhos ),
							$exibe_nivel,
							$exibe_coluna_ins,
							$exibe_coluna_met,
							$exibe_coluna_int,
							$exibe_coluna_orc,
							$exibe_coluna_con
						);
					}
				}
			?>
		</tbody>
		<!--
		<tfoot>
			<tr>
				<td colspan="13" style="background-color:#ffffff; text-align:center; padding:15px">
					<div onclick="popup_cadastrar_atividade( <?= $raiz['atiid'] ?> )" title="Cadastrar Atividade" onmouseover="this.style.cursor='pointer'">
						<img border="0" src="../imagens/gif_inclui.gif" style="vertical-align:middle;"/>
						Cadastrar Atividade
					</div>
				</td>
			</tr>
		</tfoot>
		-->
	</table>
	<script>
		window.finished = true;
		atualizaAtividadesPeloCookie();
	</script>
