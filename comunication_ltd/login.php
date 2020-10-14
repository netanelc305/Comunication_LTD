<?php
include('include/sessions.php');
include('include/functions.php');
include('include/database.php');

// Check if user already logged in
if(isset($_SESSION['userId'])){
  redirect('app/dashboard.php');
}

if(isset($_POST['Submit'])){

  $loginAllow =json_decode(file_get_contents('passwords_policy.json'), true)['login_attempts'];
  $userName = $_POST['Username'];
  $password = $_POST['Password'];
  
  // Check all fileds are filled
  if(empty($userName)|| empty($password)){
    $_SESSION['ErrorMessage'] = "All fields must be filled out";
    redirect('login.php');
  }

  // Query the db for username and his login attempts, if user exists return a user object.
  $user = get_login_attempts($userName);
  if($user){
    $attempts = $user['login_attempts']+1;

    // If max attempt exceeded prevent login and redirect to rest password page.
    if($attempts>$loginAllow){
      $_SESSION["ErrorMessage"]="Your account is block please reset password"; 
      redirect("forgotPassword.php");
    }else{

      // Validate username and password from the database.
      $account = authenticate($userName,$password);
      if($account){

        // If authentication was OK set all session variables and set login attempts to 0.
        $_SESSION["userId"]=$account["id"];
        $_SESSION["userName"]=$account["username"];
        $_SESSION["email"]=$account["email"];
        $_SESSION["SuccessMessage"]= "Wellcome ".$_SESSION["userName"]."!";
        set_login_attempts($userName,0);

        // After successfull login redirect the user to previous page if set or to dashboard.
        if(isset($_SESSION['TrackingURL'])){
          redirect($_SESSION['TrackingURL']);
        }else{
          redirect('app/dashboard.php');
        }
      }else{
        // If password was wrong , increate login attempts and display message , redirect to login page.
        set_login_attempts($userName,$attempts);
        $_SESSION["ErrorMessage"]="Wrong Password you have another ".($loginAllow-$attempts)." attempts"; 
        redirect("login.php");
      }
    }
  }else{
    $_SESSION["ErrorMessage"]="User Not Exists"; 
    redirect("login.php");
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <link rel="stylesheet" href="Css/Styles.css">
  <title>Login</title>
</head>
<body>
  <!-- NAVBAR -->
  <div style="height:10px; background:#27aae1;"></div>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a href="#" class="navbar-brand">Comunication LTD</a>
      <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarcollapseCMS">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarcollapseCMS">
      </div>
    </div>
  </nav>
    <div style="height:10px; background:#27aae1;"></div>
    <!-- NAVBAR END -->
    <!-- HEADER -->
    <header class="bg-dark text-white py-3">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
          </div>
        </div>
      </div>
    </header>
    <!-- HEADER END -->
    <!-- Main Area Start -->
    <section class="container py-2 mb-4">
  <div class="row">
  <div class=" col-lg-7" style="min-height:400px;">
  <br>
  <?php
    echo ErrorMessage();
    echo SuccessMessage();
    ?>
      <form class="" action="login.php" method="post">
        <div class="card bg-secondary text-light mb-3">
          <div class="card-header">
            <h1>Login</h1>
          </div>
          <div class="card-body bg-dark">
            <div class="form-group">
              <label for="username"> <span class="FieldInfo"> Username: </span></label>
               <input class="form-control" type="text" name="Username" id="username"  value="">
            </div>
            <div class="form-group">
              <label for="Password"> <span class="FieldInfo"> Password: </span></label>
               <input class="form-control" type="password" name="Password" id="Password" value="">
            </div>
            <div class="row">
              <div class="col-lg-6 mb-2">
                <button type="submit" name="Submit" class="btn btn-success btn-block">
                  <i class="fas fa-check"></i> Login
                </button>
              </div>
              <div class="col-lg-6 mb-2">
                <a href="register.php" class="btn btn-primary btn-block">Register</a>
              </div>
              <div class="col-lg-6 mb-2">
                <a href="forgotPassword.php" class="btn btn-danger btn-block">Forgot Password</a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

</section>

    <!-- Main Area End -->

    
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

</body>
</html>
