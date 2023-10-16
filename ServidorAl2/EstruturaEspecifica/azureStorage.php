<?php
require_once('../lib/base.php');

global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient;


$ACCOUNT_NAME  =  retornaValorConfiguracao('STORAGE_ACCOUNT');
$ACCOUNT_KEY   =  retornaValorConfiguracao('STORAGE_ACCOUNT_KEY');
$containerName =  retornaValorConfiguracao('STORAGE_NAME'); 


if($ACCOUNT_NAME != ''){
	require_once('../lib/azureStorage/autoload.php');
}

	use MicrosoftAzure\Storage\Blob\BlobRestProxy;
	use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
	use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
	use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
	use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
	use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
	use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;
	use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
	use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;
	use MicrosoftAzure\Storage\Blob\Models\ContainerACL;
	use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
	use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions;
	use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
	use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
	use MicrosoftAzure\Storage\Common\Internal\Resources;
	use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
	use MicrosoftAzure\Storage\Common\Models\Range;
	use MicrosoftAzure\Storage\Common\Models\Logging;
	use MicrosoftAzure\Storage\Common\Models\Metrics;
	use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
	use MicrosoftAzure\Storage\Common\Models\ServiceProperties;




if($ACCOUNT_NAME != ''){

$CONNECTION_STRING = 'DefaultEndpointsProtocol=https;AccountName='.$ACCOUNT_NAME.';AccountKey='.$ACCOUNT_KEY.';EndpointSuffix=core.windows.net'; 

$blobClient = BlobRestProxy::createBlobService($CONNECTION_STRING);

try {
	$blobClient->createContainer($containerName);
} catch (ServiceException $e) {
	//echo $e;
}	

}

function utilizaBlobStorage(){
	global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient  ;
	if($ACCOUNT_NAME != ''){
		return true;
	}else{
		return false;
	}
	
}	
	
//$caminhoarquivo = 'NovaPasta10/teste';
//$nomeArquivo    = 'HelloWorld.txt';
//$arquivo = fopen($nomeArquivo , "r");

//uploadFileBlogStorage($caminhoarquivo,$nomeArquivo,$arquivo);
//echo listaFileBlogStorage($caminhoarquivo);
//baixaFileBlogStorage($caminhoarquivo,$nomeArquivo);
//detelaFileBlogStorage($caminhoarquivo,$nomeArquivo);

function existeFileBlogStorage($caminhoArquivo){
	global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient  ;
	try {
		// Get blob.
		$blob = $blobClient->getBlob($containerName,$caminhoArquivo);
		
		if($blob != ''){
			return true;
		}else{
			return false;
		}

	} catch(ServiceException $e) {

		return false;
	}	
}




function uploadFileBlogStorage($caminho,$nomeArquivo,$arquivo,$mime){
	global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient  ;
	try {
		//subir arquivo
		$fileToUpload = $nomeArquivo;
		$content = $arquivo;//fopen($fileToUpload, "r");
		//$mime = mime_content_type($fileToUpload);
		$options = new CreateBlockBlobOptions();
		$options->setContentType($mime);
		$blobClient->createBlockBlob($containerName, $caminho.'/'.$fileToUpload, $content,$options);
	} catch (ServiceException $e) {
		echo $e;
	}	
}

function detelaFileBlogStorage($caminhoArquivo){
	global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient  ;
	try {
		$blobClient->deleteBlob($containerName, $caminhoArquivo);
	} catch (ServiceException $e) {
		echo $e;
	}		
}

function listaFileBlogStorage($caminho){
	global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient  ;
	$retorno = '';
	
	try {

		$blobListOptions = new ListBlobsOptions();
		$blobListOptions->setPrefix($caminho.'/');
		
		$getBlobResult = $blobClient->listBlobs($containerName,$blobListOptions);
		//var_dump($getBlobResult);
		$blobs = $getBlobResult->getBlobs();
            foreach ($blobs as $blob) {
                $retorno .= $retorno.$blob->getName().'|';
				//echo $blob->getName().": ".$blob->getUrl().PHP_EOL;
				//echo '<br>';
            }
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
	return $retorno;
}

function baixaFileBlogStorage($caminhoArquivo,$nomeOriginal){
	global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient  ;
	try {
		// Get blob.
		$blob = $blobClient->getBlob($containerName,$caminhoArquivo);
		$properties = $blobClient->getBlobProperties($containerName, $caminhoArquivo);
		$size = $properties->getProperties()->getContentLength();

		//$content = stream_get_contents($blob->getContentStream());  
		//$finfo = new finfo(FILEINFO_MIME);
		//$mime = $finfo->buffer($content);

		//header("Content-type: $mime");
		header('Content-Type: application/octet-stream');
		header("Content-length: $size");
		//header ("Content-Disposition: inline; filename=HelloWorld.txt");
		header('Content-Disposition: attachment; filename="' . $nomeOriginal. '"');

		print_r($content);

	} catch(ServiceException $e) {

		$code = $e->getCode();
		$error_message = $e->getMessage();
		echo $code.": ".$error_message."<br />";
	}	
}
function abreFileBlogStorage($caminhoArquivo,$nomeOriginal){
	global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient  ;
	try {
		// Get blob.
		$blob = $blobClient->getBlob($containerName,$caminhoArquivo);
		$properties = $blobClient->getBlobProperties($containerName, $caminhoArquivo);
		$size = $properties->getProperties()->getContentLength();

		$content = stream_get_contents($blob->getContentStream());  
		//$finfo = new finfo(FILEINFO_MIME);
		$mime = $properties->getProperties()->getContentType();

		header("Content-type: $mime");
		//header('Content-Type: application/octet-stream');
		//header("Content-length: $size");
		header ('Content-Disposition: inline; filename="' . $nomeOriginal. '"');
		//header('Content-Disposition: attachment; filename="' . $nomeOriginal. '"');

		print_r($content);

	} catch(ServiceException $e) {

		$code = $e->getCode();
		$error_message = $e->getMessage();
		echo $code.": ".$error_message."<br />";
	}	
}

function tipoMineBlogStorage($caminhoArquivo){
	global $ACCOUNT_NAME,$ACCOUNT_KEY,$CONNECTION_STRING,$containerName,$blobClient  ;
	try {
		// Get blob.
		$properties = $blobClient->getBlobProperties($containerName, $caminhoArquivo);
		
		return $properties->getProperties()->getContentType();
		
	} catch(ServiceException $e) {

		$code = $e->getCode();
		$error_message = $e->getMessage();
		echo $code.": ".$error_message."<br />";
	}	
}

/*

	try {
		//Pegar um arquivo
        //$getBlobResult = $blobClient->getBlob($containerName, "NovaPasta2/HelloWorld.txt");
		
		//pegar todos arquivos de um diretorio
		$blobListOptions = new ListBlobsOptions();
		$blobListOptions->setPrefix('NovaPasta2/');
		
		$getBlobResult = $blobClient->listBlobs($containerName,$blobListOptions);
		//var_dump($getBlobResult);
		$blobs = $getBlobResult->getBlobs();
            foreach ($blobs as $blob) {
                echo $blob->getName().": ".$blob->getUrl().PHP_EOL;
				echo '<br>';
            }
    } catch (ServiceException $e) {
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message.PHP_EOL;
    }
*/
/*$fileToUpload ='HelloWorld.txt';
 $blob = $blobClient->getBlob($containerName,"NovaPasta5/HelloWorld.txt");
 fpassthru($blob->getContentStream());	


try {
    // Get blob.
    $blob = $blobClient->getBlob($containerName,"NovaPasta5/HelloWorld.txt");
    $properties = $blobClient->getBlobProperties($containerName, "NovaPasta5/HelloWorld.txt");
    $size = $properties->getProperties()->getContentLength();

    //$content = stream_get_contents($blob->getContentStream());  
    //$finfo = new finfo(FILEINFO_MIME);
    //$mime = $finfo->buffer($content);

    //header("Content-type: $mime");
	header('Content-Type: application/octet-stream');
    header("Content-length: $size");
    //header ("Content-Disposition: inline; filename=HelloWorld.txt");
	header('Content-Disposition: attachment; filename="' . 'HelloWorld.txt' . '"');

    print_r($content);

} catch(ServiceException $e) {

    $code = $e->getCode();
    $error_message = $e->getMessage();
    echo $code.": ".$error_message."<br />";
}
*/
//$blobClient->deleteBlob($containerName, $blob);
//DefaultEndpointsProtocol=https;AccountName=aliancaweb;AccountKey=b+4yCvwcGYJFGRg4FphGtszDkf4jKxJ9OuAyLesHgInNPCl+R/m9hC9mhSMI2b+hfflMy9VsIatfz15vGOdjVg==;EndpointSuffix=core.windows.net
//b+4yCvwcGYJFGRg4FphGtszDkf4jKxJ9OuAyLesHgInNPCl+R/m9hC9mhSMI2b+hfflMy9VsIatfz15vGOdjVg==



?>