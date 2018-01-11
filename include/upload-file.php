<?php
define("IMG_DIR","../thumbnails/");

    // vide le dossier image avant tout
    $dossier_traite = IMG_DIR;
    $repertoire = opendir($dossier_traite); // On définit le répertoire dans lequel on souhaite travailler.
    while (($fichier = readdir($repertoire) !== false)) {// On lit chaque fichier du répertoire dans la boucle.
        $chemin = $dossier_traite."".$fichier; // On définit le chemin du fichier à effacer.
        // Si le fichier n'est pas un répertoire…
        if ($fichier != ".." AND $fichier != "." AND !is_dir($fichier))
        {
            unlink($chemin); // On efface.
        }
    }
    closedir($repertoire); // Ne pas oublier de fermer le dossier ***EN DEHORS de la boucle*** ! Ce qui évitera à PHP beaucoup de calculs et des problèmes liés à l'ouverture du dossier.

    $thename = ""; // the file uploaded
    if ($_FILES["pictures"]["error"] == UPLOAD_ERR_OK) {

        $tmp_name = $_FILES["pictures"]["tmp_name"];
        $thename = basename($_FILES["pictures"]["name"]);
        if(!move_uploaded_file($tmp_name, IMG_DIR."".$thename)) {
            $error = " file cannot be moved";
        }
    }

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
            $toFileName = $fileinfo["basename"];
            if((stristr($filename,"__MACOSX") === false && stristr($filename,".DS_Store") === false )
                && ($ext == "jpg" || $ext == "png" || $ext == "gif" ) && (substr( $filename, -1 ) !== '/') )
            {
                $aFileImages[] = $toFileName;
                $aZipFileName[] = $filename;
                copy("zip://".IMG_DIR.$thename."#".$zip->getNameIndex($i), "IMG_DIR".$fileinfo['basename']);
            }
        }

        var_dump($aFileImages);
        var_dump($aZipFileName);
       // $vazy = $zip->extractTo(IMG_DIR, $aFileImages);
        if($vazy) echo "\n extract to OK ";

        // on vérifie quoiqui n'y a dans le dossier ?
        $repertoire = opendir(IMG_DIR);
        $fichier = readdir($repertoire);
        echo " \n file == ".$fichier;
        while ($fichier) {

            echo "\n ".$fichier;
            if ($fichier != ".." AND $fichier != "." AND !is_dir($fichier))
            {
               echo "\n ".$fichier;
            }
            $fichier = readdir($repertoire);
        }
        closedir($repertoire); // Ne pas oublier de fermer le dossier ***EN DEHORS de la boucle*** ! Ce qui évitera à PHP beaucoup de calculs et des problèmes liés à l'ouverture du dossier.

        $zip->close();
    }
    else {
        echo " Erreur de dezippage ";
    }
                    /*
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

                    // 20) close the zip

                    // 21) delete the zip

        // 22) check if there were at least 1 image in the zip

        // need to force apache header content type to json if the server always return "text/html" content type
        header('Content-Type: application/json');

        // 23) finally return error message or the array of images
       // echo json_encode($aFileImages);
