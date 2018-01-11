<?php
define("IMG_DIR","../thumbnails");

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

	if ($res === true) {
	    var_dump($zip);
            // here you can run a custom function for the particular extracted file
            // 6) loop on each entries of the zip
            for($i = 0; $i < $zip->numFiles; $i++) {
                echo " <br/> ".$zip->numFiles;
                // 7) retrieve local entrie filenames
                // 8) retrieve file "basename" (filenames without directory)
                $filename = $zip->getNameIndex($i);
                echo " <br/> ".$filename;
                //$zip->extractTo(IMG_DIR."/", array($zip->getNameIndex($i)));
                $zip->extractTo(IMG_DIR."/" . $filename, array('*.jpg','*.jpeg','*.png','*.gif') );

                // 09) we skip directories

                // 10) retrieve absolute zip entries filenames $fileZipLocation = "zip://".$tempFileName."#".$filename;

                // 11) retrieve file content-type

                // 12) we restrict loop to images only

                // 13) save the image type that will be used lately in eval();

                // 14) create a relative path destination variable

                // 15) copy file from zip to thumbnails relative dir

                // 16) Resize part
                $aFileImages[] = $filename;
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
                $size = getimagesize($filename); // chaine avec height & width

                    // 17) for imagecreatefromXXX we can use eval(); with function that expect string as parameters

                    // 18) we cannot use eval with function imageXXX since first param is a resource

                    // 19) finally push all imagenames in array of images

                    // 20) close the zip

                    // 21) delete the zip

            }

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