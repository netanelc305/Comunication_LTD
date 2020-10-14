<?php

include('include/functions.php');
include('include/sessions.php');

if(isset($_POST["Submit"])){
  $token=$_POST["Token"];
  if(empty($token)){
    $_SESSION["ErrorMessage"]= "Please Enter The token you got on your mail";
    redirect('enterToken.php');
  }else{
    $username = check_token($token);
    if($username){
      $_SESSION["username"]=$username;
      redirect('resetPassword.php');
    }
    else {
      $_SESSION["ErrorMessage"]= "Invalid Token !";
      redirect("enterToken.php");
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
  <link rel="stylesheet" href="Css/Styles.css">
  <title>Reset Password</title>
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
  <div class=" col-lg-6" style="min-height:400px;">
  <?php
    echo ErrorMessage();
    echo SuccessMessage();
    ?>
      <form class="" action="enterToken.php" method="post">
        <div class="card bg-secondary text-light mb-3">
          <div class="card-header">
            <h1>Enter Token</h1>
          </div>
          <div class="card-body bg-dark">
            <div class="form-group">
              <label for="token"> <span class="FieldInfo"> Token: </span></label>
               <input class="form-control" type="text" name="Token" id="token"  value="">
            </div>
          <div class="row">
              <div class="col-lg-8 mb-2">
                <button type="submit" name="Submit" class="btn btn-success btn-block">
                  <i class="fas fa-check"></i> Submit
                </button>
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
