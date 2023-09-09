<?php
 $connection = mysqli_connect("localhost","root","");
 $db = mysqli_select_db($connection,"miniproject");
 session_start();


$user_id = $_SESSION['user_id'];
if(!isset($user_id))
        header('location:welcome.php');     
       
  








?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="./assets/css/adminstyle.css" />
    
    <title>sharewithcode</title>
   
</head>

<body>
     
    <div class="d-flex" id="wrapper">
        
        <div class="maha11" id="sidebar-wrapper">
        <nav class="main-nav" id="projectname">
          <a href="welcome.php" class="logo">
            <h4 style="text-transform: none;">Share<span>With</span><img src="assets/images/download.png" alt="">code</h4>
          </a>
        
        </nav>
            <div class="list-group list-group-flush my-3">
                <a href="welcome.php" class="list-group-item list-group-item-action bg-transparent second-text active"><img src="assets/images/dashboard.png" alt="">
                      <p>Dashboard</p></a>
                <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                <img src="assets/images/user.png" alt=""><p>profile</p></a>
                <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                            <img src="assets/images/locked.png" alt=""><p>change password</p></a>
                <a href="logout.php" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold">
                           <img src="assets/images/qrview.png" alt=""><p>logout</p></a>
                           
                

                
                
                
 
            </div>
            <br>
            <br>

            <div class="container1">
             <a href="#" onclick="generateQR()" class="list-group-item list-group-item-action bg-transparent second-text fw-bold ">
    
                <div id="imgb">
                    <img src="assets/images/qrview1.gif"  id="qrImage">
                </div>
            </div>
            </a> 
        </div>
        
        <div class="header">
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-1 px-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-5 me-5" id="menu-toggle"></i>
                    
                   <?php
                        $con = mysqli_connect("localhost","root","");
                        $db = mysqli_select_db($con,"miniproject");
                        
                        $select = mysqli_query($con,"SELECT * From sharewithcode WHERE id = $user_id");
                        if(mysqli_num_rows($select) > 0){
                            $fetch = mysqli_fetch_assoc($select);
                        }
                    ?>



                    

                    <h2 class=" m-0" id="das11">Dashboard</h2>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-4 mb-lg-2">
                        <li class="nav-item dropdown" id="dropd">
                            <a class="nav-link dropdown-toggle second-text fw-bold" href="#" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><a class="dropdown-item" href="#">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            </div>
            
            <div class="container-fluid px-2"  >
                <div class="row g-2 my-3">
                <div id="namehello">
                        <p><span>Hello <?php echo $fetch['name']; ?>., <br> welcome to Share with code</span></p> 

                        </div>
                    <div class="col-md-10">
                        

                        <div class="p-1 bg-white shadow-sm d-flex justify-content-around align-items-center" id="dada">

                            <div id="head111">
                               <table class="table table-striped"  cellspacing="20px" id="tab111" >
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col"><b>number</b></th>
                                        <th scope="col"><b>NAME</b></th>
                                        <th scope="col"><b>FILE</b></th>
                                        
                                    </tr>
                                    
                                </thead>
                                <tbody>
                                
                                <?php
                                $sql="select * from data1";
                                $result=mysqli_query($con,$sql);
                                while($row=mysqli_fetch_assoc($result)){
                                    $custname=$row['custname'];
                                    $image=$row['image'];
                                    echo '<tr>
                                    <td id="custn">'.$custname.'</td>
                                    <td><img src='.$image.' width="50px";height="50px"/></td>

                                    </tr>';
                                }

                                ?>
                                
                                </tbody>
                               </table>
                            </div>
                        </div>
                        	

	
                    </div>

                  

            </div>
        </div>
    </div>
    
  
    
    <script>
        let imgb = document.getElementById("imgb");
        let qrImage = document.getElementById("qrImage");
        let imgtext = document.getElementById("imgtext");
        function generateQR(){
            qrImage.src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://sharewithcode.epizy.com/scan.php";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>


</body>

</html>