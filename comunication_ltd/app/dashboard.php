<?php

include('../include/functions.php');
include('../include/sessions.php');

$_SESSION['TrackingURL'] = $_SERVER['PHP_SELF'];
is_authenticated();

if(isset($_GET["id"])){
  global $db;
  $id = $_GET["id"];
  $sql = "DELETE FROM clients WHERE id='$id'";
  $success=$db->query($sql);
  if ($success) {
    $_SESSION["SuccessMessage"]="Client Deleted Successfully ! ";
    redirect("dashboard.php");
  }else {
    $_SESSION["ErrorMessage"]="Something Went Wrong. Try Again !";
    redirect("dashboard.php");
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
  <title>Dashboard</title>
</head>
<body>
<?php include("navbar.php");?>
     <!-- Main Area -->
<section class="container py-2 mb-4">
  <div class="row">
    <div class=" col-lg-7" style="min-height:400px;">
    <?php
    echo ErrorMessage();
    echo SuccessMessage();
    ?>
    <h2>Existing Clients</h2>
      <table class="table table-striped table-hover">
        <thead class="thead-dark">
          <tr>
            <th>No. </th>
            <th>First Name</th>
            <th>Second Name</th>
            <th></th>
          </tr>
        </thead>
        <?php
      global $db;
      $sql = "SELECT * FROM clients ORDER BY id";
      $Execute =$db->query($sql);
      while ($DataRows=$Execute->fetch()) {
        $clientId = $DataRows["id"];
        $firstName = $DataRows["firstName"];
        $lastName= $DataRows["lastName"];
      ?>
      <tbody>
        <tr>
          <td><?php echo htmlentities($clientId); ?></td>
          <td><?php echo htmlentities($firstName); ?></td>
          <!-- XXS Vunerable parameter lastName -->
          <!-- HOW TO THE VULN : echo htmlentities($lastName) -->
          <!-- Payload <script>alert(1)</script> -->

          <td><?php echo $lastName;?></td>
          <td> <a href="dashboard.php?id=<?php echo $clientId;?>" class="btn btn-danger">Delete</a>  </td>

      </tbody>
      <?php }?>
    </div>
  </div>
    <!-- End Main Area -->

</section>
<?php include("footer.php");?>
</body>
</html>
