<?php
//
include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;
session_start();
if (isset($_SESSION['loggedUsername'])) {
        if (isset($_SESSION['loggedRole'])) {
        $role_session_active = $_SESSION['loggedRole'];
        if (($role_session_active == "RootAdmin") || ($role_session_active == "ToolAdmin")) {
                    $currentDir = getcwd();
                    $fileExtensions = ['jpeg','jpg','png','gif','GIF','JPEG','JPG','PNG'];
                    $uploadDirectory = "img/sensorImages/";
                    if(isset($_POST['id_row'])){
                            $id_row = $_POST['id_row'];
                    }else{
                            $id_row = "";   
                    }
                    if ((is_numeric($id_row)!=="")&&(is_numeric($id_row)!==null)){
                    // 
                            $fileTmpName  = $_FILES['uploadField']['tmp_name'];
                            $fileName  = $_FILES['uploadField']['name'];
                            $fileType = $_FILES['uploadField']['type'];

                            $newDir = explode('management',$currentDir );
                            $currentDir2 = $newDir[0];

                             $fileExtension = explode('.',$fileName);

                             //
                             if (! in_array($fileExtension[1],$fileExtensions)) {
                                                echo('Error Upload');
                                    }else{
                                          if(file_exists($currentDir2 . $uploadDirectory .  $id_row)){
                                                    $dir = $currentDir2 . $uploadDirectory .  $id_row;
                                                    $dirHandle = opendir($dir);
                                                        $i = 0;
                                                           while ($file = readdir($dirHandle)) {
                                                                   if($i >1){
                                                                           echo($file);
                                                                           unlink($dir.'/'.$file);
                                                                    }
                                                               $i++;
                                                            }
                                                       $uploadPath = $currentDir2 . $uploadDirectory .  $id_row . '/'  . basename($fileName); 
                                                        echo($uploadPath);
                                                        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
                                       }else{
                                                    mkdir($currentDir2 . $uploadDirectory .  $id_row, 0777);
                                                    $uploadPath = $currentDir2 . $uploadDirectory .  $id_row . '/'  . basename($fileName);
                                                    $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
                                        }
                                        
                                                                                            //****///
                                                    $link = mysqli_connect($host_processes, $username_processes, $password_processes) or die("failed to connect to server Processes !!");
                                                     mysqli_set_charset($link, 'utf8');
                                                        mysqli_select_db($link, $dbname_processes);
                                                        //$query = "INSERT INTO devices (image) VALUES ('".basename($fileName)."') WHERE device_name='".$id_row."'";
                                                        //Se esiste fare un update e se non esiste fare insert.
                                                        $query_count = "SELECT count(*) AS count FROM devices WHERE device_name='".$id_row."'";
                                                        $result_count= mysqli_query($link, $query_count) or die(mysqli_error($link));
                                                        //$num_rows = mysql_num_rows($result_count);
                                                         while ($row_own = mysqli_fetch_assoc($result_count)) {
                                                        if ($row_own['count'] == 0){
                                                            $query = "INSERT INTO devices (device_name,process,HealthinessCriteria,Period,image) VALUES ('".$id_row."','','','','".basename($fileName)."')";
                                                         $result = mysqli_query($link, $query) or die(mysqli_error($link));
                                                        }else{
                                                           
                                                         //
                                                         $query = "UPDATE devices SET image = '".basename($fileName)."' WHERE device_name='".$id_row."'";
                                                         $result = mysqli_query($link, $query) or die(mysqli_error($link));
                                                         //
                                                        }
                                    }
                              }

                    }else{
                        //error
                    } 
        }else{
            exit();
        }
        }else{
            exit();
        }
   }else{
     exit();
}
//
?>