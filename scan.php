<?php
  $con=mysqli_connect('localhost','root','','miniproject');
  
  if(isset($_POST['upload']))
  {
    $custname =$_POST['custname'];
    $image=$_FILES['file111'];
    $imagefilename = $image['name'];
    $imagefileerror = $image['error'];
    $imagefiletmp = $image['tmp_name'];
    $filename_separate=explode('.',$imagefilename);
    $file_extension=strtolower(end($filename_separate));
    $extension=array('jpeg','jpg','png');
     if(in_array($file_extension,$extension)){
      $upload_image='images1/'.$imagefilename;
      move_uploaded_file($imagefiletmp,$upload_image);
      $sql="insert into data1(custname,image) values ('$custname','$upload_image')";
      $result=mysqli_query($con,$sql);
      if($result){
      }
     }




   
    
    
    

  }
   
  require_once('./operation.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWC_scan</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="./assets/css/scanstyle.css">
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.min.js" integrity="sha384-heAjqF+bCxXpCWLa6Zhcp4fu20XoNIA98ecBC1YkdXhszjoejr5y9Q77hIrv8R9i" crossorigin="anonymous"></script>

</head>
<body>


<nav class="main-nav1">
   
   <a href="scan.php" class="logo">
     <h4 style="text-transform: none;">Share<span>With</span><img src="assets/images/download.png" alt="">code<span></span></h4>
   </a>
</nav>

    <h1 class="text-center my-3">upload file</h1>
    <div class="container d-flex justify-content-center">
    <form action="scan.php" method="POST" class="w-50" enctype="multipart/form-data" >
      <?php inputFields("custname","custname","","text") ?>
      <?php inputFields("choose your file","file111","","file")?>
      <div class="field btn">
                <div class="btn-layer"></div>
                <input type="submit" name="upload" value="upload" id="liveAlertBtn">
      </div>
    </form>
    </div>
     
<script>
  
const alertPlaceholder = document.getElementById('liveAlert')
const alert = (message, type) => {
  const wrapper = document.createElement('div')
  wrapper.innerHTML = [
    `<div class="alert alert-${type} alert-dismissible" role="alert">`,
    `   <div>${message}</div>`,
    '</div>'
  ].join('')

  alertPlaceholder.append(wrapper)
}

const alertTrigger = document.getElementById('liveAlertBtn')
if (alertTrigger) {
  alertTrigger.addEventListener('click', () => {
    alert('your file submited successfully','success')
  })
}
function disable(x){
  x.disabled = true;
}
</script>


   
  
</body>
</html>