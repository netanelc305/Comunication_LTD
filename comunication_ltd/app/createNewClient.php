<?php

include('../include/functions.php');
include('../include/sessions.php');

$_SESSION['TrackingURL'] = $_SERVER['PHP_SELF'];
is_authenticated();

if(isset($_POST['Submit'])){
  $firstName = $_POST['firstName'];
  $lastName =  $_POST['lastName'];

  //Check for empty fileds.
  if(empty($firstName) || empty($lastName)){
    $_SESSION["ErrorMessage"]= "All fields must be filled out";
    redirect('createNewClient.php');
  }
  // Check if client exsists on the database.
  else if(get_client($firstName,$lastName)){
    $_SESSION["ErrorMessage"]= "Client already exists !";
    redirect('createNewClient.php');
  }
  else{
    // Create new client.
    $clientId = create_new_client($firstName,$lastName);
    if($clientId){
      $_SESSION["SuccessMessage"]= "New client created !";
      redirect('dashboard.php');
    }
    else {
      $_SESSION["ErrorMessage"]= "Something went wrong. Try Again !";
      redirect("createNewClient.php");
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
  <title>Create New Client</title>
</head>
<body>

<?php include("navbar.php");?>

    <!-- HEADER -->
    <header class="bg-dark text-white py-3">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
          <h1><i class="fas fa-user" style="color:#27aae1;"></i> Manage Clients</h1>
          </div>
        </div>
      </div>
    </header>
    <!-- HEADER END -->

     <!-- Main Area -->
<section class="container py-2 mb-4">
  <div class="row">
    <div class=" col-lg-7" style="min-height:400px;">
    <?php
    echo ErrorMessage();
    echo SuccessMessage();
    ?>
      <form class="" action="createNewClient.php" method="post">
        <div class="card bg-secondary text-light mb-3">
          <div class="card-header">
            <h1>Add New Client</h1>
          </div>
          <div class="card-body bg-dark">
            <div class="form-group">
              <label for="firstname"> <span class="FieldInfo"> First Name </span></label>
               <input class="form-control" type="text" name="firstName" id="firstname"  value="">
            </div>
            <div class="form-group">
              <label for="lastname"> <span class="FieldInfo"> Last Name: </span></label>
               <input class="form-control" type="text" name="lastName" id="lastname" value="">
            </div>
              <div class="offset-lg-3 col-lg-5 mb-2">
                <button type="submit" name="Submit" class="btn btn-success btn-block">Submit</button>
              </div>
            </div>
          </div>
        </div>
      </form>   
    </div>
  </div>

</section>



    <!-- End Main Area -->
    <!-- FOOTER -->
    <footer class="bg-dark text-white">
      <div class="container">
        <div class="row">
          <div class="col">
          <p class="lead text-center"></p>
           </div>
         </div>
      </div>
    </footer>
        <div style="height:50px; background:#27aae1;"></div>
    <!-- FOOTER END-->

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>
