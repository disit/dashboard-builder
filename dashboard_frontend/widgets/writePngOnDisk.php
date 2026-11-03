<?php

//$data = 'data:image/png;base64,AAAFBfj42Pj4';

function writePngIconFile($path, $data) {
    $file = @fopen($path, 'wb');
    if ($file === false) return false;
    $written = @fwrite($file, $data);
    $closed = fclose($file);
    if ($written === false || $written !== strlen($data) || !$closed) {
        @unlink($path);
        return false;
    }
    return true;
}

function pngIconWriteFailure() {
    http_response_code(500);
    return ['result' => 'KO', 'detail' => 'File could not be written.'];
}

$response = [];

if (isset($_REQUEST['imgBase64Data'])) {

    $data = $_REQUEST['imgBase64Data'];

    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

   /* if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
        $data = substr($data, strpos($data, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            throw new \Exception('invalid image type');
        }

        $data = base64_decode($data);

        if ($data === false) {
            throw new \Exception('base64_decode failed');
        }
    } else {
        throw new \Exception('did not match data URI with image data');
    }*/

  //  file_put_contents("/output_png/img.{$type}", $data);
    if (isset($_REQUEST['nameFile'])) {
        if (isset($_REQUEST['nameFolder'])) {
         //   $serviceType = explode("_rgb", $_REQUEST['nameFile'])[0];
            if (strpos($_REQUEST['nameFile'], '-over') !== false) {
                $serviceType = explode('-over', $_REQUEST['nameFile'])[0];
            } else {
                $serviceType = explode('_rgb', $_REQUEST['nameFile'])[0];
                if (strpos($serviceType, '#') !== false) {
                    $serviceType = explode('_#', $serviceType)[0];
                }
                if (strpos($serviceType, 'undefined') !== false) {
                    $serviceType = explode('_#', $serviceType)[0];
                }
                if (strpos($serviceType, '_default') !== false) {
                    $serviceType = explode('_default', $serviceType)[0];
                }
            }

            $uploadFolder = $_REQUEST['nameFolder'] . $serviceType . "/";

            if(!file_exists($_REQUEST['nameFolder']))
            {
                $oldMask = umask(0);
                $created = @mkdir($_REQUEST['nameFolder'], 0777);
                umask($oldMask);
                if (!$created && !is_dir($_REQUEST['nameFolder'])) {
                    echo json_encode(pngIconWriteFailure());
                    return;
                }
            }

            if(!file_exists($uploadFolder))
            {
                $oldMask = umask(0);
                $created = @mkdir($uploadFolder, 0777);
                umask($oldMask);
            }

            if (strpos($_REQUEST['nameFile'], "#") !== false) {
                $fileName = explode("#", $_REQUEST['nameFile'])[0] . explode("#", $_REQUEST['nameFile'])[1];
            } else {
                $fileName = $_REQUEST['nameFile'];
            }

            if (writePngIconFile($_REQUEST['nameFolder'] . "/" . $serviceType . "/" .$fileName . ".png", $data)) {
                $response['detail'] = 'File Written.';
            } else {
                $response = pngIconWriteFailure();
            }

        /*    $files = glob($uploadFolder.'*');
            foreach($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }*/

        } else {
            $serviceType = explode("_rgb", $_REQUEST['nameFile'])[0];
            if (strpos($serviceType, '#') !== false) {
                $serviceType = explode('_#', $serviceType)[0];
            }
            if (strpos($serviceType, 'undefined') !== false) {
                $serviceType = explode('_#', $serviceType)[0];
            }
            if (strpos($_REQUEST['nameFile'], "#") !== false) {
                $fileName = explode("#", $_REQUEST['nameFile'])[0] . explode("#", $_REQUEST['nameFile'])[1];
            } else {
                $fileName = $_REQUEST['nameFile'];
            }
            if (writePngIconFile("../img/outputPngIcons/" . $fileName . ".png", $data)) {
                $response['detail'] = 'File Written.';
            } else {
                $response = pngIconWriteFailure();
            }
        }
    } else {
        $response['detailFile'] = 'No File Name Passed';
    }

} else {

    $response['detail'] = 'No imgBase64Data';

}

echo json_encode($response);