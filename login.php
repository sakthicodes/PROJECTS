<?php
  $connection = mysqli_connect("localhost","root","");
  $db = mysqli_select_db($connection,"miniproject");
  session_start();

  if(isset($_POST['submit']))

  {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $sql = "insert into sharewithcode(name,username,password,confirm_password)values('$name','$username','$password','$confirm_password')";
    if(mysqli_query($connection,$sql))
    {
      echo '<script> location.replace("login.php")</script>';
    }
    else
    {
      echo "somethingeror".$connection->error;

    }    

  }
  if(isset($_POST['submit1'])){
    $username=$_POST['username'];
    $password=$_POST['password'];
    $con = mysqli_connect("localhost","root","","miniproject");
    $sql = " SELECT * from sharewithcode WHERE username='$username' AND password='$password' ";
    $result=mysqli_query($con,$sql);
    $resultcheck=mysqli_num_rows($result);
    
 

    
    if($resultcheck>0)
    {
      $select = mysqli_query($con," SELECT * from sharewithcode WHERE username='$username' AND password='$password' " );
      $row = mysqli_fetch_assoc($select);
      $_SESSION['user_id'] = $row['id'];
      header('location:welcome.php');      
    }
    else
    {
      echo '<script> location.replace("login.php")</script>';
      echo "invalid user";
    }

  }
 
?>
<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Sharewithcode</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/stylelogin.css">
   
    

</head>
<body>

    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
          <span class="dot"></span>
          <div class="dots">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
    <div class="wrapper">
        <div class="title-text">
          <div class="title login">Login</div>
          <div class="title signup">Signup</div>
        </div>
        <div class="form-container">
          <div class="slide-controls">
            <input type="radio" name="slide" id="login" checked>
            <input type="radio" name="slide" id="signup">
            <label for="login" class="slide login">Login</label>
            <label for="signup" class="slide signup">Signup</label>
            <div class="slider-tab"></div>
          </div>
          <div class="form-inner">
            <form action="login.php" class="login" method="post">
              <div class="field">
                <input type="text" name="username" placeholder="Email Address" required>
              </div>
              <div class="field">
                <input type="password" name="password" placeholder="Password" required>
              </div>
              <div class="pass-link"><a href="#">Forgot password?</a></div>
              <div class="field btn">
                <div class="btn-layer"></div>
                <input type="submit" name="submit1" value="Login">
              </div>
              <div class="signup-link">Not a member? <a href="">Signup now</a></div>
            </form>
            <form action="login.php" class="signup" method="post">
              <div class="field">
                <input type="text" name="name" placeholder="Name" required>
              </div>
              <div class="field">
                <input type="text" name="username" placeholder="Email Address" required>
              </div>
              <div class="field">
                <input type="password" name="password" placeholder="Password" required>
              </div>
              <div class="field">
                <input type="password" name="confirm_password" placeholder="Confirm password" required>
              </div>
              <div class="field btn">
                <div class="btn-layer"></div>
                <input type="submit" name="submit" value="Signup">
              </div>
            </form>
          </div>
        </div>
      </div>
<script>
     const loginText = document.querySelector(".title-text .login");
      const loginForm = document.querySelector("form.login");
      const loginBtn = document.querySelector("label.login");
      const signupBtn = document.querySelector("label.signup");
      const signupLink = document.querySelector("form .signup-link a");
      signupBtn.onclick = (()=>{
        loginForm.style.marginLeft = "-50%";
        loginText.style.marginLeft = "-50%";
      });
      loginBtn.onclick = (()=>{
        loginForm.style.marginLeft = "0%";
        loginText.style.marginLeft = "0%";
      });
      signupLink.onclick = (()=>{
        signupBtn.click();
        return false;
      });
      

</script> 
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/animation.js"></script>
<script src="assets/js/imagesloaded.js"></script>
<script src="assets/js/templatemo-custom.js"></script> 


</body>
</html>


