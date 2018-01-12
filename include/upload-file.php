<?php
define("IMG_DIR","../thumbnails/");

    // vide le dossier image avant tout
    $repertoire = opendir(IMG_DIR);
    while (($fichier = readdir($repertoire) !== false)) {
        if ($fichier != ".." AND $fichier != "." AND !is_dir($fichier))
        {
            unlink(IMG_DIR."".$fichier);
        }
    }
    closedir($repertoire);

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
                $aFileImages[] = $fileinfo["basename"];
                $aZipFileName[] = $filename;
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
    $repertoire = opendir(IMG_DIR);
    while (($fichier = readdir($repertoire) !== false)) {
        if ($fichier != ".." AND $fichier != "." AND !is_dir($fichier))
        {
            echo " \n file in dir :: ".$fichier;
            /*
             * #TODO :
             * retrieve origin image width & height
             *
             * origin ratio => 1200 / 900 = 1.33
             *
             * both new width & height must be defined before!
             *
             *  1.33 > 1
             *
             *  150 / 1.33 = 112.5
             *
             * imagecreatetruecolor()
             * imagecreatefromXXX()
             * imagecopyresampled()
             * imageXXX()
            */


                //$size = getimagesize($filename); // chaine avec height & width

                // 17) for imagecreatefromXXX we can use eval(); with function that expect string as parameters

                // 18) we cannot use eval with function imageXXX since first param is a resource

                // 19) finally push all imagenames in array of images
        }
    }
    closedir($repertoire);

        // need to force apache header content type to json if the server always return "text/html" content type
        header('Content-Type: application/json');

        // 23) finally return error message or the array of images
       // echo json_encode($aFileImages);
