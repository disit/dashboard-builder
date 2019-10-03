<?php
//
$currentDir = getcwd();
$fileExtensions = ['jpeg','jpg','png','gif','GIF','JPEG','JPG','PNG'];
$uploadDirectory = "img/sensorImages/";
if(isset($_POST['id_row'])){
        $id_row = $_POST['id_row'];
}else{
        $id_row = "";   
}
if (is_numeric($id_row)){
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
  }

}else{
    //error
} 
  
?>