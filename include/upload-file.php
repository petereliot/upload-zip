<?php
define("IMG_DIR","../thumbnails/");
define("NEW", 150);

    // vide le dossier image avant tout
    $handle = opendir("../thumbnails");
    var_dump($handle);

    while (false !== ($entry = readdir($handle))) {
        $chemin = "../thumbnails/".$entry; // On définit le chemin du fichier à effacer.
        if ($entry != "." && $entry != ".." AND !is_dir($entry)) {
            unlink($chemin);
        }
    }
    closedir($handle);

    // #TODO : reste à vérifier le type du document uploadé
    // #TODO : Si c'est un Zip : on gère le zip (sinon on vérifie que c'est une image et on upload direct l'image !!
    //  gere le upload du zip
    $thename = ""; // the file uploaded
    if ($_FILES["pictures"]["error"] == UPLOAD_ERR_OK) {

        $tmp_name = $_FILES["pictures"]["tmp_name"];
        $thename = basename($_FILES["pictures"]["name"]);
        if(!move_uploaded_file($tmp_name, IMG_DIR."".$thename)) {
            $error = " file cannot be moved";
        }
    }

    // dézippe le fichier : #todo : vérifier que c'est un zip !!
    $aFileImages=[];
    $aZipFileName=[];

    $zip = new ZipArchive();

    $res = $zip->open(IMG_DIR.$thename);

	if ($res === true)
	{
        for($i = 0; $i < $zip->numFiles; $i++)
        {
            $filename = $zip->getNameIndex($i);
            $fileinfo = pathinfo($zip->getNameIndex($i));
            $ext = $fileinfo['extension'];
            if((stristr($filename,"__MACOSX") === false && stristr($filename,".DS_Store") === false )
                && ($ext == "jpg" || $ext == "png" || $ext == "gif" ) && (substr( $filename, -1 ) !== '/') )
            {
                //$aFileImages[] = $fileinfo["basename"];
                //$aZipFileName[] = $filename;
                copy("zip://".IMG_DIR.$thename."#".$zip->getNameIndex($i), "../thumbnails/".$fileinfo["basename"]);

            }
        }

        $zip->close();
        unlink(IMG_DIR.$thename); // On efface le doc zip
    }
    else {
        echo " Erreur de dezippage ";
    }

    // liste des fichiers correctement uploadés pour retailler l'image
    $handle = opendir("../thumbnails");
	var_dump($handle);

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != ".." AND !is_dir($entry)) {
            $imgFile = IMG_DIR.$entry;
            $fileinfo = pathinfo($imgFile);
            $ext = $fileinfo['extension'];
            $newImgFile = IMG_DIR.$fileinfo['extension']."-b.".$ext;
            echo "<br/>".$ext;
            var_dump($fileinfo);
            // retrieve origin image width & height
            $dimensions = getimagesize($imgFile);

            // origin ratio => 1200 / 900 = 1.33
            $ratio_orig = $dimensions[0] / $dimensions[1];


            // both new width & height must be defined before!
            $width = 150;
            $height = 150;

            // ratio_oirg > 1 : $width/$height = 1
            if ($width/$height > $ratio_orig)
            {
                // 150 / 1.33 = 112.5
                $new_width = $height*$ratio_orig;
                $new_height = $height;
            }
            else
            {
                $new_width = $width;
                $new_height = $width/$ratio_orig;
            }
            // Redimensionnement
            $image_p = imagecreatetruecolor($width, $height);
            switch($ext){
                case "jpg" : {
                    $image = imagecreatefromjpeg($imgFile);
                    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height,$dimensions[0],$dimensions[1]);
                    imagejpeg($image_p, $imgFile, 100);
                }
                    break;
                case "png" : {
                    $image = imagecreatefrompng($imgFile);
                    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height,$dimensions[0],$dimensions[1]);
                    imagepng($image_p, $imgFile);
                }
                    break;
                case "gif" : {
                    $image = imagecreatefromgif($imgFile);
                    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height,$dimensions[0],$dimensions[1]);
                    imagegif($image_p, $imgFile);
                }
                    break;
                default :break;
            }

            // Libération de la mémoire
            imagedestroy($image_p);

            $aFileImages[]=$imgFile; //finally push all imagenames in array of images
        }
    }
    closedir($handle);

    // need to force apache header content type to json if the server always return "text/html" content type
    header('Content-Type: application/json');

    // 23) finally return error message or the array of images
    echo json_encode($aFileImages);
// var_dump($aFileImages);
/*
$file = '';
$codeErrorUpoad =['UPLOAD_ERR_OK', ' UPLOAD_ERR_INI_SIZE', ' UPLOAD_ERR_FORM_SIZE', 'UPLOAD_ERR_PARTIAL','UPLOAD_ERR_NO_FILE',
                   'UPLOAD_ERR_NO_TMP_DIR','UPLOAD_ERR_CANT_WRITE','UPLOAD_ERR_EXTENSION '];
if ($_FILES["the_file"]["error"] == UPLOAD_ERR_OK) {
   $time = microtime(true);
       if(move_uploaded_file($_FILES["the_file"]["tmp_name"], 'thumbnails/'.$time.'.zip')){
           //ok $file
           $file = $time.'.zip';
       }else $errorMsg = 'erreur move_uploaded_file';

}else $errorMsg = $codeErrorUpoad [$_FILES["the_file"]["error"]];
echo json_encode($errorMsg ?? $file);
 */
