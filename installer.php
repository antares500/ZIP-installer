<?php
# pack - Comprime/Descomprime e instala un codigo en archivos ZIP - 07/01/2015

if(isset($_GET['pack'])){
	$file	= './code-'.date('Ymdhis').'.zip';
	$r		= zip('./', $file);
	if($r === true)	{	echo '<h3 style="font-family:Courier New;"><a href="'.$file.'">Archivo comprimido con &eacute;xito</a></h3>';	}
	else			{	echo '<h3 style="font-family:Courier New;">Oops: algo ha ocurrido</h3>Response: ';	print_r($r);				}
} elseif(isset($_FILES) && count($_FILES)==0){ ?>
<form method="post" enctype="multipart/form-data" onsubmit="return (this.archivo.value!='')">
	<input type="file" name="code"/>
	<button type="submit">Subir a la web</button>
	<a href="?pack">Descargar c&oacute;digo</a>
</form>
<?php }
elseif( move_uploaded_file($_FILES['code']['tmp_name'],'./code.zip') ){	unzip();	}
else {	echo '<h3 style="font-family:Courier New;">Oops: algo ha ocurrido</h3>';	}


// // //
function zip($source, $destination){ //comprime
	if(!extension_loaded('zip') || !file_exists($source)){	return false;	}
	
	$zip = new ZipArchive();
	if(!$zip->open($destination, ZIPARCHIVE::CREATE)	){	return false;	}

	$source = str_replace('\\', '/', realpath($source));

	if(is_dir($source) === true){
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $file){
			$file = str_replace('\\', '/', $file);

			// Ignore "." and ".." folders
			if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )	continue;

			$file = realpath($file);
				if(is_dir( $file)===true){	$zip->addEmptyDir(	str_replace($source.'/', '', $file.'/'));	}
			elseif(is_file($file)===true){	$zip->addFromString(str_replace($source.'/', '', $file), file_get_contents($file));	}
		}
	} elseif(is_file($source) === true){	$zip->addFromString(basename($source), file_get_contents($source));	}

	return $zip->close()? true:array($source, $destination, $zip);
}
function unzip(){ //descomprime
	//descomprimir
	$z	= new ZipArchive;
	$res= $z->open('./code.zip');
	if($res === true){
		$z->extractTo('./');
		echo
			'<h3 style="font-family:Courier New;">Archivo descomprimido con &eacute;xito <em>(se redirigir&aacute; en 10s)</em></h3>'.
			'<meta http-equiv="refresh" content="10;URL=index.php">';
		$z->close();
		unlink('code.zip');	//borra el zip
		exit();
	}
	echo '<h3 style="font-family:Courier New;">Oops: algo ha ocurrido</h3>';
}
?>