<?php 
include('include/functions.php');
if(isset($_SESSION['userId'])){
    redirect('app/dashboard.php');
}else{
    redirect('login.php');
}
?>