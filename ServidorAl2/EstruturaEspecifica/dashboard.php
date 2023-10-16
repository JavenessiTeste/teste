<?php
require('../lib/base.php');
require('../private/autentica.php');


if($dadosInput['tipo'] =='dados'){
	$resultado['GRAFICOS'] =array();
	
	$corPadrao = array();
	$itemCor['backgroundColor'] ='rgba(59, 85, 230, 1)';
	$itemCor['borderColor'] ='rgba(59, 85, 230, 1)';
	$itemCor['pointBackgroundColor'] ='rgba(59, 85, 230, 1)';
	$itemCor['pointBorderColor'] ='#fff';
	$itemCor['pointHoverBackgroundColor'] ='#fff';
	$itemCor['pointHoverBorderColor'] ='rgba(235, 78, 54, 1)';
	$corPadrao[]=$itemCor;
	$itemCor['backgroundColor'] ='rgba(235, 78, 54, 1)';
	$itemCor['borderColor'] ='rgba(235, 78, 54, 1)';
	$itemCor['pointBackgroundColor'] ='rgba(235, 78, 54, 1)';
	$itemCor['pointBorderColor'] ='#fff';
	$itemCor['pointHoverBackgroundColor'] ='#fff';
	$itemCor['pointHoverBorderColor'] ='rgba(59, 85, 230, 1)';
	$corPadrao[]=$itemCor;
	$itemCor['backgroundColor'] ='rgba(67, 210, 158, 0.2)';
	$itemCor['borderColor'] ='rgba(67, 210, 158, 1)';
	$itemCor['pointBackgroundColor'] ='rgba(67, 210, 158, 1)';
	$itemCor['pointBorderColor'] ='#fff';
	$itemCor['pointHoverBackgroundColor'] ='#fff';
	$itemCor['pointHoverBorderColor'] ='rgba(67, 210, 158, 0.8)';
	$corPadrao[]=$itemCor;
	
	
	$grafico['TITULO'] = 'Evolução de inclusão/exclusão de beneficiários';
	$grafico['CHAVE']  = '1';
	$grafico['LABEL']  = array();
	$grafico['DATA']   = array();
	$grafico['CORES']  = $corPadrao;
	$grafico['TAMANHO']  = '100';
		
	$resultado['GRAFICOS'][] = $grafico;
	
	$grafico['TITULO'] = 'Teste';
	$grafico['CHAVE']  = '1';
	$grafico['LABEL']  = array();
	$grafico['DATA']   = array();
	$grafico['CORES']  = $corPadrao;
	$grafico['TAMANHO']  = '100';
	
	$resultado['GRAFICOS'][] = $grafico;

	
	
	echo json_encode($resultado);
}
if($dadosInput['tipo'] =='carrega'){
	
	$resultado['LABEL'] = array();
	$resultado['DATA']  = array();
	
	//pr($dadosInput['filtros']['dados']['CODIGO_EMPRESA']);
	//pr($dadosInput['filtros']['dados']['EMPRESAS']);
	
	$dadosInput['filtros']['dados']['EMPRESAS'] = str_replace("\\'", "'",$dadosInput['filtros']['dados']['EMPRESAS']);
	
	
	if($dadosInput['chave']==1){
		
		$itemInclusao = array();
		$itemInclusao['label'] = 'Inclusão';
		$itemInclusao['type'] = 'bar';//line bar
		$itemInclusao['fill'] = false;
		
		$itemExclusao = array();
		$itemExclusao['label'] = 'Exclusão';
		$itemExclusao['type'] = 'bar';//line bar
		$itemExclusao['fill'] = false;
		
		$filtro = '1=1 ';
		
		if($dadosInput['filtros']['dados']['CODIGO_EMPRESA']!=''){
			$filtro .= ' and codigo_empresa ='.aspas($dadosInput['filtros']['dados']['CODIGO_EMPRESA']).' ';
		}
		if($dadosInput['filtros']['dados']['EMPRESAS']!=''){
			$filtro .= ' and codigo_empresa in('.($dadosInput['filtros']['dados']['EMPRESAS']).') ';
		}
		
		if($dadosInput['filtros']['dados']['DATA_INICIAL']!=''){
			$filtroInc .= ' and data_admissao >='.aspas($dadosInput['filtros']['dados']['DATA_INICIAL']).' ';
			$filtroExc .= ' and data_exclusao >='.aspas($dadosInput['filtros']['dados']['DATA_INICIAL']).' ';
		}
		if($dadosInput['filtros']['dados']['DATA_FINAL']!=''){
			$filtroInc .= ' and data_admissao <='.aspas($dadosInput['filtros']['dados']['DATA_FINAL']).' ';
			$filtroExc .= ' and data_exclusao <='.aspas($dadosInput['filtros']['dados']['DATA_FINAL']).' ';
		}
		
		if (($dadosInput['filtros']['dados']['DATA_INICIAL']!='')and(substr($dadosInput['filtros']['dados']['DATA_INICIAL'], 0, 4)==substr($dadosInput['filtros']['dados']['DATA_FINAL'], 0, 4))){
			$sql = "select extract(MONTH FROM Z.DATA) MES,extract(YEAR FROM Z.DATA) ANO, sum(Z.INC)INC, sum(Z.EXC)EXC  from (
						select  ps1000.data_admissao DATA, count(*) INC,0 EXC from ps1000
						where $filtro $filtroInc
						group by  ps1000.data_admissao
						union all
						select  ps1000.data_exclusao DATA, 0 INC,count(*) EXC from ps1000
						where $filtro $filtroExc and ps1000.data_exclusao is not null
						group by   ps1000.data_exclusao

						)Z
						group by   extract(MONTH FROM Z.DATA),extract(YEAR FROM Z.DATA)
						order by extract(YEAR FROM Z.DATA),extract(MONTH FROM Z.DATA)
			";
		}else{
			$sql = "select extract(YEAR FROM Z.DATA) ANO, sum(Z.INC)INC, sum(Z.EXC)EXC  from (
						select  ps1000.data_admissao DATA, count(*) INC,0 EXC from ps1000
						where $filtro $filtroInc
						group by   ps1000.data_admissao
						union all
						select  ps1000.data_exclusao DATA, 0 INC,count(*) EXC from ps1000
						where $filtro $filtroExc and ps1000.data_exclusao is not null
						group by   ps1000.data_exclusao

						)Z
						group by   extract(YEAR FROM Z.DATA)
						order by extract(YEAR FROM Z.DATA)
			";
			
		}
		
		$result = jn_query($sql);
		while($row = jn_fetch_object($result)){
			if (($dadosInput['filtros']['dados']['DATA_INICIAL']!='')and(substr($dadosInput['filtros']['dados']['DATA_INICIAL'], 0, 4)==substr($dadosInput['filtros']['dados']['DATA_FINAL'], 0, 4))){
				$resultado['LABEL'][] = str_pad($row->MES,2,'0',STR_PAD_LEFT).'/'.$row->ANO;
			}else{
				$resultado['LABEL'][] = $row->ANO;
			}
			$itemInclusao['data'][] = $row->INC;
			$itemExclusao['data'][] = $row->EXC;
		}
		
		$resultado['DATA'][] = $itemInclusao;
		$resultado['DATA'][] = $itemExclusao;
		
		
   }if($dadosInput['chave']==2){
	  
   }
	
	echo json_encode($resultado);

}





?>