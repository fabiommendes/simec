<?php
function geraTextoClicavel( $strCampo , $strMask, $strTexto , $intMaxLength = 200)
{
	$boolReduzida = false;
	$strOriginal = $strTexto;
	$strTexto = geraTextoLimitado( $strTexto, $intMaxLength , $boolReduzida  );
	if( true )
	{
		?>
			<span
			class="activeFrozenFieldDisable"
			title="<?= $strOriginal ?>"
			attribute="<?= $strCampo ?>"  
			mask="<?= $strMask ?>" 
			><?= $strTexto ?></span>
		<?php
	}
	else
	{
		?>
			<span
			><?= $strTexto ?></span>
		<?php
	}
}

function geraTextoLimitado( $strTexto = "" , $intMaxLength = 200 , &$boolReduzida )
{
	$strTexto = html_entity_decode ( $strTexto );
	if( $strTexto == "" )
	{
		$strTexto = "-";
	}
	if( strlen( $strTexto ) > $intMaxLength )
	{
		$strTexto = substr( $strTexto , 0 , $intMaxLength ) . "...";
		$boolReduzida = true;
	}
	$strTexto = xmlentities( trim( $strTexto ) );
	return $strTexto;
}

function geraTabelaTarefas( $arrTarefasQueContenho , $boolAjaxMode = false )
{
	$arrTarefasQueContenho = orderArrayOfObjectsByMethod( $arrTarefasQueContenho , 'getCodigoUnico' );
	?>
		<script>
			window.serverSideClassName = "<?= get_class( @$arrTarefasQueContenho[0] ) ?>";
		</script>	
			<table border="0" class="tabelaTarefas" id="tabelaTarefas">
				<tbody>
					<?php foreach ( $arrTarefasQueContenho as $intPosicao => $objTarefa ) 
					{
					?>
						<tr class="tarefasFilhasAberto <?= 
								( ($intPosicao % 2 ) ? 'Par' : 'Impar' ) 
								?> <?= 
								( ($objTarefa->getQuantidadeDeTarefasFilhas() > 0 ) ? 'MacroEtapa' : 'Etapa' ) 
								?> <?=
								( ($objTarefa->getSubAcao() ) ? ' subAcao ' : '' ) 
								?> <?=
								( ($objTarefa->getOrigemEspecial() ) ? 'OrigemEspecial' : 'OrigemComum' )
								?>" id="tr<?= $objTarefa->getId() ?>" parent="<?= $objTarefa->getContainerId()?>" >
							<td class="tarefaMais">
								<!-- Codigo -->
								<element
								class="<?= get_class( $objTarefa )  ?>" 
								identifier="<?= $objTarefa->getId() ?>">
									<?= geraTextoClicavel( "intCodigoUnico" , "readonly" , $objTarefa->getCodigoUnico() , 5  ) ?>
								</element>
								</td>
							</td>
							<td class="tarefaOrdem">
								<span style="color:blue"	title="Incluir Tarefa Filha">
									<?php if ( ! $objTarefa->getSomenteLeitura() ): ?>
										<img border="0" src="../imagens/gif_inclui.gif" title="Incluir Tarefa Filha." onclick="incluirTarefaFilha(<?= $objTarefa->getId() ?> , <?= $objTarefa->getAcaoId() ?> , this )"/>
									<?php else: ?>
										<img border="0" src="../imagens/gif_inclui.gif" title="" style="visibility:hidden"/>
									<?php endif ?>
								</span>
								<span style="color:green"	title="Editar a Tarefa" >
								 	<img border="0" src="../imagens/alterar.gif" title="Editar a Tarefa." onclick="editarTarefa(<?= $objTarefa->getId() ?>)" />
								</span>
									<span style="color:red"		title="Excluir a Tarefa" >
										<?php if ( ! $objTarefa->getSomenteLeitura() ): ?>
											<img border="0" src="../imagens/excluir.gif" title="Excluir a Tarefa." onclick="removeElement( this , <?= $objTarefa->getId() ?>, <?= $objTarefa->getAcaoId() ?>, '<?= get_class( $objTarefa )  ?>' )"/>
										<?php else: ?>					
											<img border="0" src="../imagens/excluir.gif" title="" style="visibility:hidden"/>
										<?php endif ?>								
									</span>
								<span class="gantt" >
									<img border="0"  src="../imagens/lupa_grafico.gif" onclick="exibe_grafico( 'Acao' , <?= $objTarefa->getAcao()->getId() ?> , <?= $objTarefa->getId() ?> , 1000 )" />
								</span>
								<span class="imagemPPA" title="Origem Especial PPA">
									<img border="0"  src="../imagens/ppa.gif"  />
								</span>
							</td>
							<td class="tarefaNome">
								<span class="tarefaMais" name="255,255,255">
									<!-- Mais -->
									<img src="../../includes/JsLibrary/img/more.gif" id="imgTarefa<?= $objTarefa->getId() ?>" onclick="carregaTarefasFilhas( <?= $objTarefa->getId() ?> , this )" />
								</span>
								<element
								class="<?= get_class( $objTarefa ) ?>" 
								identifier="<?= $objTarefa->getId() ?>">
									<!-- Codigo Estruturado -->
									<?= geraTextoClicavel( "strCodigoEstruturado" , "readonly" , $objTarefa->getCodigoEstruturado() , 80 ) ?>
								</element>
								<element
								class="<?= get_class( $objTarefa )  ?>" 
								identifier="<?= $objTarefa->getId() ?>">
									<!-- Nome -->
									<?= geraTextoClicavel( "strNome" , $objTarefa->getSomenteLeitura() ? "readonly" : "string" , $objTarefa->getNome() , 80 ) ?>
								</element>
							</td>
							<td class="tarefaDescricao">
								<!-- Descricao -->
								<element
								class="<?= get_class( $objTarefa )  ?>" 
								identifier="<?= $objTarefa->getId() ?>">
									<?= geraTextoClicavel( "strDescricao" , $objTarefa->getSomenteLeitura() ? "readonly" : "string" , $objTarefa->getDescricao() , 80 ) ?>
								</element>
							</td>
							<td class="tarefaInicio">
								<!-- Inicio -->
								<element
								class="<?= get_class( $objTarefa )  ?>" 
								identifier="<?= $objTarefa->getId() ?>">
									<?= geraTextoClicavel( "datInicio" , $objTarefa->getSomenteLeitura() ? "readonly" : "date" , $objTarefa->getDataInicio() ) ?>
								</element>
							</td>
							<td class="tarefaTermino">
								<!-- Termino -->
								<element
								class="<?= get_class( $objTarefa )  ?>" 
								identifier="<?= $objTarefa->getId() ?>">
									<?= geraTextoClicavel( "datFim" , $objTarefa->getSomenteLeitura() ? "readonly" : "date" , $objTarefa->getDataFim() ) ?>
								</element>
							</td>
							<td class="tarefaFechada">
								<!--  Fechada -->	
								<element
								method="object"
								class="<?= get_class( $objTarefa )  ?>" 
								identifier="<?= $objTarefa->getId() ?>">
									<input
									type="checkbox"
									disabled="disabled" 
									attribute="boolDataFechada"  
									mask="<?= $objTarefa->getSomenteLeitura() ? "readonly" :  "checkbox" ?>"
									<? if ( $objTarefa->getDataFechada() ) : ?>
										checked="checked" 
									<? endif ?>
									/>
								
								</element>
							</td>
							<td class="tarefaTipo">
								<!-- Predecessora -->
								<element
								method="object"
								class="<?= get_class( $objTarefa )  ?>" 
								identifier="<?= $objTarefa->getId() ?>">
									<?= geraTextoClicavel( "intPredecessoraCodigoUnico" , $objTarefa->getSomenteLeitura() ? "readonly" : "integer" , $objTarefa->getPredecessora() ? $objTarefa->getPredecessora()->getCodigoUnico() : '' ) ?>
								</element>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	<?
}
?>