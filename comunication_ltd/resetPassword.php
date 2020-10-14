<?php

include('include/sessions.php');
include('include/functions.php');

if(!isset($_SESSION['username'])){
  $_SESSION["ErrorMessage"]="Token was not provided please Try Again !";
  redirect('forgotPassword.php');
}
if(isset($_POST['Submit'])){

  $historyAmount =json_decode(file_get_contents('passwords_policy.json'), true)['password_history'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  set_token($_SESSION['username'],NULL);

  // Get user from database
  $user = get_user($_SESSION['username']);
  if(!$user){
    $_SESSION["ErrorMessage"]= "User Not Exsists";
    redirect('resetPassword.php');
  }
  
  // Check for empty fileds.
  if(empty($new_password) || empty($confirm_password)){
    $_SESSION["ErrorMessage"]= "All fields must be filled out";
    redirect('resetPassword.php');
  }
  // Check if new password and confirm password are match.
  else if($new_password!==$confirm_password){
    $_SESSION["ErrorMessage"]= "New Passwords not match !";
    redirect('resetPassword.php');
  }
  // Check for password policy.
  $new_password = password_policy($new_password);
  if(!$new_password){
    redirect('resetPassword.php');
  }
  // Check if new password is in the user password history.
  elseif(check_password_history($new_password,$user["id"])){
    $_SESSION["ErrorMessage"]= "You can't use your last ".$historyAmount." passwords";
    redirect('resetPassword.php');
  }
  else{
    // Update the new password on the database.
    $success = update_new_password($new_password,$user["id"]);
    if($success){
      $_SESSION["SuccessMessage"]= "Password rest successfully";
      set_login_attempts($user["username"],NULL);
      redirect('logout.php');
    }else{
      $_SESSION["ErrorMessage"]= "Something went wrong. Try Again !";
      redirect('resetPassword.php');
    }
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
  <title>Reset Password</title>
</head>
<body>
    <!-- HEADER -->
    <header class="bg-dark text-white py-3">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
          <h1><i class="fas fa-user" style="color:#27aae1;"></i> Reset Password</h1>
          </div>
        </div>
      </div>
    </header>
    <!-- HEADER END -->

     <!-- Main Area -->
<section class="container py-2 mb-4">
  <div class="row">
    <div class=" col-lg-7" style="min-height:400px;">
    <br>
    <?php
    echo ErrorMessage();
    echo SuccessMessage();
    ?>
      <form class="" action="resetPassword.php" method="post">
        <div class="card bg-secondary text-light mb-3">
          <div class="card-header">
          <h1>Enter new password</h1>
          </div>
          <div class="card-body bg-dark">
            <div class="form-group">
              <label for="Password"> <span class="FieldInfo">New Password: </span></label>
               <input class="form-control" type="password" name="new_password" id="Password" value="">
            </div>
            <div class="form-group">
              <label for="ConfirmPassword"> <span class="FieldInfo"> Confirm Password:</span></label>
               <input class="form-control" type="password" name="confirm_password" id="ConfirmPassword"  value="">
            </div>
              <div class="col-lg-6 mb-2">
              <button type="submit" name="Submit" class="btn btn-success btn-block">Submit</button>
              </div>
            </div>
          </div>
        </div>
      </form>   
    </div>
  </div>

</section>


  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>
