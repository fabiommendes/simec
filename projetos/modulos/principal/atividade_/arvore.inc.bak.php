<?php

echo 'Entrou';
die();

// VERIFICA SE PROJETO EST� SELECIONADO
projeto_verifica_selecionado( $_REQUEST["atiidraiz"] );

// VERIFICA DE USU�RIO POSSUI PERFIL PARA ALOCA��O DE DALAS
$usuario_alocacao_salas = arrayPerfil();

// CABE�ALHO
include APPRAIZ . 'includes/cabecalho.inc';
print '<br/>';

if($_SESSION['projeto'] == '55168' && (in_array(PERFIL_ADMINISTRADOR,$usuario_alocacao_salas) || in_array(PERFIL_GESTOR,$usuario_alocacao_salas) || in_array(PERFIL_EQUIPE_APOIO_GESTOR,$usuario_alocacao_salas) || $db->testa_superuser() ) ) {
	
	$menu = array(0 => array("id" => 1, "descricao" => "Todas Atividades",   "link" => "/pde/projetos.php?modulo=principal/atividade_/arvore&acao=A"),
				  1 => array("id" => 2, "descricao" => "Minhas Atividades",	 "link" => "/pde/projetos.php?modulo=principal/atividade_/arvore&acao=R"),
				  2 => array("id" => 2, "descricao" => "Aloca��o de Salas",	 "link" => "/pde/projetos.php?modulo=principal/atividade_/listaEnem&acao=A"));
	
	echo montarAbasArray($menu, $_SERVER['REQUEST_URI']);
} else {
	$db->cria_aba( $abacod_tela, $url, '' );
}
montar_titulo_projeto();

if ( $_REQUEST["formulario_filtro_arvore"] ) {
	
}

?>
<table class="tabela" bgcolor="#f5f5f5" cellspacing="0" cellpadding="10" align="center">
	<tr>
		<td>
			<table border="0" cellpading="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
						<?php if ( $_REQUEST['acao'] != 'R' ) : ?>
							<script language="javascript" type="text/javascript">
								
								function formulario_filtro_arvore_submeter()
								{
									document.formulario_filtro_arvore.submit();
								}
								
							</script>
							<form name="formulario_filtro_arvore" action="" method="post">
								<input type="hidden" name="formulario_filtro_arvore" value="1"/>
								<table border="0" cellpadding="5" cellspacing="0" width="100%">
									<tr>
										<td align="right" width="150">
											Atividade
										</td>
										<td>
											<select id="atiidraiz" name="atiidraiz" class="CampoEstilo" style="width: 250px;">
												<option value="">
													<?php
													$sql = "select atidescricao from pde.atividade where atiid = " . PROJETO;
													echo $db->pegaUm( $sql );
													?>
												</option>
												<?php
												
												$sql = "
													select
														a.atiid,
														a.atidescricao,
														a._atiprofundidade as profundidade,
														a._atinumero as numero
													from pde.atividade a
													where
														a.atistatus = 'A'
														and a._atiprofundidade < 3
														and a._atiprojeto = " . PROJETO . " 
													order by
														a._atiordem
												";
												$lista = $db->carregar( $sql );
												$lista = $lista ? $lista : array();
												
												?>
												<?php foreach ( $lista as $item ) : ?>
													<option value="<?=  $item['atiid'] ?>" <?= $item['atiid'] == $_REQUEST["atiidraiz"] ? 'selected="selected"' : '' ?>>
														<?= str_repeat( '&nbsp;', $item['profundidade'] * 5 ) ?>
														<?= $item['numero'] ?>
														<?= $item['atidescricao'] ?>
													</option>
												<?php endforeach; ?>
											</select>
										</td>
									</tr>
									<tr>
										<td align="right">
											Profundidade
										</td>
										<td>
											<?php
											
											// for�a o preenchimento do formul�rio
											$_REQUEST["profundidade"] = $_REQUEST["profundidade"] ? $_REQUEST["profundidade"] : 3;
											?>
											<select name="profundidade" class="CampoEstilo">
												<option value="1" <?= $_REQUEST["profundidade"] == 1 ? 'selected="selected"' : '' ?>>1 n�vel</option>
												<option value="2" <?= $_REQUEST["profundidade"] == 2 ? 'selected="selected"' : '' ?>>2 n�veis</option>
												<option value="3" <?= $_REQUEST["profundidade"] == 3 ? 'selected="selected"' : '' ?>>3 n�veis</option>
												<option value="4" <?= $_REQUEST["profundidade"] == 4 ? 'selected="selected"' : '' ?>>4 n�veis</option>
												<option value="5" <?= $_REQUEST["profundidade"] == 5 ? 'selected="selected"' : '' ?>>5 n�veis</option>
												<option value="6" <?= $_REQUEST["profundidade"] == 6 ? 'selected="selected"' : '' ?>>6 n�veis</option>
											</select>
										</td>
									</tr>
									<script language="javascript" type="text/javascript">
										
										function SetAllCheckBoxes( FormName, FieldName, CheckValue ) {
											if(!document.forms[FormName])
												return;
											var objCheckBoxes = document.forms[FormName].elements[FieldName];
											if(!objCheckBoxes)
												return;
											var countCheckBoxes = objCheckBoxes.length;
											if(!countCheckBoxes)
												objCheckBoxes.checked = CheckValue;
											else
												for(var i = 0; i < countCheckBoxes; i++)
													objCheckBoxes[i].checked = CheckValue;
										}
										
									</script>
									<tr>
										<td align="right">
											Situa��o
											(<a href="" onclick="SetAllCheckBoxes( 'formulario_filtro_arvore', 'situacao[]', true ); return false;">todos</a>)
										</td>
										<td>
											<?php
											
											// for�a o preenchimento do formul�rio
											if ( $_REQUEST["formulario_filtro_arvore"] ) {
												$situacao = $_REQUEST["situacao"];
											} else {
												$situacao = array(
													STATUS_NAO_INICIADO,
													STATUS_EM_ANDAMENTO
												);
											}
											
											$situacao = (array) $situacao;
											
											?>
											<label style="margin: 0 10px 0 0;"><input type="checkbox" name="situacao[]" value="<?= STATUS_NAO_INICIADO ?>" <?= in_array( STATUS_NAO_INICIADO, $situacao ) ? 'checked="checked"' : '' ?>/>n�o iniciado</label>
											<label style="margin: 0 10px 0 0;"><input type="checkbox" name="situacao[]" value="<?= STATUS_EM_ANDAMENTO ?>" <?= in_array( STATUS_EM_ANDAMENTO, $situacao ) ? 'checked="checked"' : '' ?>/>em andamento</label>
											<label style="margin: 0 10px 0 0;"><input type="checkbox" name="situacao[]" value="<?= STATUS_SUSPENSO ?>" <?= in_array( STATUS_SUSPENSO, $situacao ) ? 'checked="checked"' : '' ?>/>suspenso</label>
											<label style="margin: 0 10px 0 0;"><input type="checkbox" name="situacao[]" value="<?= STATUS_CANCELADO ?>" <?= in_array( STATUS_CANCELADO, $situacao ) ? 'checked="checked"' : '' ?>/>cancelado</label>
											<label style="margin: 0 10px 0 0;"><input type="checkbox" name="situacao[]" value="<?= STATUS_CONCLUIDO ?>" <?= in_array( STATUS_CONCLUIDO, $situacao ) ? 'checked="checked"' : '' ?>/>conclu�do</label>
										</td>
									</tr>
									<?php if( atividade_verificar_responsabilidade( PROJETO, $_SESSION["usucpf"] ) ): ?>
										<tr>
											<td align="right">Sob Responsabilidade</td>
											<td>
												<?php
												
												// for�a o preenchimento do formul�rio
												$usucpf = $_REQUEST["usucpf"];
												
												$sql = "
													select
														u.usucpf as codigo,
														u.usunome as descricao
													from seguranca.usuario u
														inner join seguranca.usuario_sistema us on us.usucpf = u.usucpf
														inner join pde.usuarioresponsabilidade ur on ur.usucpf = u.usucpf
														inner join seguranca.perfil p on p.pflcod = ur.pflcod
														inner join seguranca.perfilusuario pu on pu.pflcod = p.pflcod and pu.usucpf = u.usucpf
														inner join pde.atividade a on a.atiid = ur.atiid
													where
														u.suscod = 'A'
														and us.suscod = 'A'
														and us.sisid = ". $_SESSION["sisid"] ."
														and ur.rpustatus = 'A'
														and ur.pflcod = ". PERFIL_GERENTE ."
														and a.atistatus = 'A'
														and a._atiprojeto = ". PROJETO ."
													group by u.usucpf, u.usunomeguerra, u.usunome
													order by u.usunome
												";
												$db->monta_combo(
													"usucpf",
													$sql,
													"S",
													"- selecione -",
													"", ""
												);
												
												?>
											</td>
										</tr>
									<?php endif; ?>
									<tr>
										<td align="right">&nbsp;</td>
										<td>
											<input
												type="button"
												name="alterar_arvore"
												value="Atualizar �rvore"
												onclick="formulario_filtro_arvore_submeter();"
											/>
										</td>
									</tr>
								</table>
							</form>
						<?php endif; ?>
					</td>
					<td valign="top" width="250">
						<?= montar_formulario_pesquisa(); ?>
					</td>
				</tr>
			</table>
			<hr size="1" noshade="noshade" color="#dddddd" style="margin:15px 0 15px 0;"/>
			<?php
			
			if ( $_REQUEST["acao"] == "R" ) {
				$atividade = PROJETO;
				$profundidade = null;
				$usuario = $_SESSION["usucpf"];
				$perfil = array();
				$situacao = array(
					STATUS_NAO_INICIADO,
					STATUS_EM_ANDAMENTO,
					STATUS_SUSPENSO,
					STATUS_CANCELADO,
					STATUS_CONCLUIDO
				);
			} else {
				$atividade = $_REQUEST["atiidraiz"] ? $_REQUEST["atiidraiz"] : PROJETO;
				$profundidade;
				$situacao = array();
				$profundidade = $_REQUEST["profundidade"];
				$usuario = $_REQUEST["usucpf"] ? $_REQUEST["usucpf"] : null;
				$perfil = array( PERFIL_GERENTE );
			}
			echo arvore( $atividade, $profundidade, $situacao, $usuario, null, null, $perfil );
			
			?>
		</td>
	</tr>
</table>