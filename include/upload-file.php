<?php
define("IMG_DIR","../thumbnails");

// vide le dossier image avant tout
$dossier_traite = IMG_DIR;
$repertoire = opendir($dossier_traite); // On définit le répertoire dans lequel on souhaite travailler.
while (($fichier = readdir($repertoire) !== false)) {// On lit chaque fichier du répertoire dans la boucle.
    $chemin = $dossier_traite."/".$fichier; // On définit le chemin du fichier à effacer.
    // Si le fichier n'est pas un répertoire…
    if ($fichier != ".." AND $fichier != "." AND !is_dir($fichier))
    {
        unlink($chemin); // On efface.
    }
}
closedir($repertoire); // Ne pas oublier de fermer le dossier ***EN DEHORS de la boucle*** ! Ce qui évitera à PHP beaucoup de calculs et des problèmes liés à l'ouverture du dossier.

/*
 *
 */

var_dump($_FILES);

// if all went fine ===>
/*
UPLOAD_ERR_OK: 0
UPLOAD_ERR_INI_SIZE: 1
UPLOAD_ERR_FORM_SIZE: 2
UPLOAD_ERR_NO_TMP_DIR: 6
UPLOAD_ERR_CANT_WRITE: 7
UPLOAD_ERR_EXTENSION: 8
UPLOAD_ERR_PARTIAL: 3
 */
$thename = "";
    echo " error = ".$_FILES["pictures"]["error"];
    if ($_FILES["pictures"]["error"] == UPLOAD_ERR_OK) {
echo " upload ok ";
        // 1) create a NEW UNIQUE uploaded zip filename
        $tmp_name = $_FILES["pictures"]["tmp_name"];

        echo " tmpname ".$tmp_name;

        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
        $thename = basename($_FILES["pictures"]["name"]);
        echo " name ".$thename;
        // 2) upload the zip on the server
        $ret = move_uploaded_file($tmp_name, IMG_DIR."/".$thename);
        if($ret) echo " file moved ";
        else echo " pb not moved";
    }


    // 3) declare an empty array of resized images
    $aFileImages=[];
    /*
     *
     */
    // 4) instantiate the ZipArchive class
    $zip = new ZipArchive(); // heroku la classe n'est pas connue

    // 5) open the uploaded zip
    $res = $zip->open(IMG_DIR."/".$thename);

	if ($res === true)
	{
        for($i = 0; $i < $zip->numFiles; $i++)
        {
            /*
             *  filename = pics/alexwende_4.gif
             fileinfo = array(4) {
              ["dirname"]=>
              string(4) "pics"
              ["basename"]=>
              string(15) "alexwende_4.gif"
              ["extension"]=>
              string(3) "gif"
              ["filename"]=>
              string(11) "alexwende_4"
            }
             */
            $filename = $zip->getNameIndex($i);
            $fileinfo = pathinfo($zip->getNameIndex($i));
            $ext = $fileinfo['extension'];
            $toFileName = $fileinfo["basename"];
            echo "\n filename = ".$filename;
            echo "\n fileinfo = ";
            var_dump($fileinfo);
            //if ( substr( $entry, -1 ) == '/' ) continue; // skip directories
            if((stristr($filename,"__MACOSX") === false && stristr($filename,".DS_Store") === false )
                && ($ext == "jpg" || $ext == "png" || $ext == "gif" ) && (substr( $filename, -1 ) !== '/') )
            {
                $from = "\n zip://" . $path . "#" . $filename;
                $to = "\n".IMG_DIR."/" . $fileinfo["basename"];
                copy("zip://".$filename, IMG_DIR."/" . $toFileName);
                $aFileImages[] = $toFileName;
            }
        }

        $dossier_traite = IMG_DIR;
        $repertoire = opendir($dossier_traite); // On définit le répertoire dans lequel on souhaite travailler.
        while (($fichier = readdir($repertoire) !== false)) {// On lit chaque fichier du répertoire dans la boucle.
            $chemin = $dossier_traite."/".$fichier; // On définit le chemin du fichier à effacer.
            // Si le fichier n'est pas un répertoire…
            if ($fichier != ".." AND $fichier != "." AND !is_dir($fichier))
            {
               echo " fichier trouvé ok !! ".$fichier;
            }
        }
        closedir($repertoire); // Ne pas oublier de fermer le dossier ***EN DEHORS de la boucle*** ! Ce qui évitera à PHP beaucoup de calculs et des problèmes liés à l'ouverture du dossier.

        //$aFileImages[] = $filename;


      //  $ext = strtolower(pathinfo($zip->getNameIndex($i), PATHINFO_EXTENSION));
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

        $zip->close();
        // 22) check if there were at least 1 image in the zip

        // need to force apache header content type to json if the server always return "text/html" content type
        header('Content-Type: application/json');

        // 23) finally return error message or the array of images
        echo json_encode($aFileImages);

    }
    else {
	    echo " Erreur de dezippage ";
        //json_encode("error");
    }