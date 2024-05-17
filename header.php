<?php
  if(isset($_POST['logout'])){
      session_start();
      $_SESSION['logged_in'] = false;
      header('location: index.php');
      die("logging out");
  }
?>
<div class="container">
  <div class="container" id="title_container">
    <div class="container-fluid">
      <h1 class="brand">Ingredient Stock Management System</h1>
    </div>
    <?php if($_SESSION['get_position'] != 'USER') {?>
    <div class="btn-group">
      <a class="btn btn-default" href="home.php"><span class="fa fa-home"></span>&nbsp;&nbsp;Home</a>
      <a class="btn btn-default" href="ingredient.php"><span class="fa fa-cart-plus"></span>&nbsp;&nbsp;Manage Ingredient</a>
      <a class="btn btn-default" href="category.php"><span class="fa fa-cart-plus"></span>&nbsp;&nbsp;Manage Category</a>
      <a class="btn btn-default" href="unit.php"><span class="fa fa-cart-plus"></span>&nbsp;&nbsp;Manage Unit</a>
      <!-- <a class="btn btn-default" href="stock_manager.php"><span class="fa fa-area-chart"></span>&nbsp;&nbsp;Ingredient Stock Manager</a> -->
      <!-- <a class="btn btn-default" href="account_registration.php"><span class="fa fa-user-secret"></span>&nbsp;&nbsp;User Management</a> -->
      <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <span class="fa fa-user-circle"></span>
        <?php echo $getfullname; ?>&nbsp;&nbsp;<img src="images/online.png" width="10">&nbsp;&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu">
          <li><a href="#" data-toggle="modal" data-target="#change">Change Password</a></li>
          <li>
            <form class="" action="header.php" method="post">
              <button class="btn btn-danger" type="submit" name="logout" style="border-radius: 0; width: 100%; padding: 3px 20px; outline: none;" >Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
    <?php } else {?>
    <!-- btn group end -->

    <div class="btn-group">
      <a class="btn btn-default" href="home.php"><span class="fa fa-home"></span>&nbsp;&nbsp;Home</a>
      <a class="btn btn-default" href="stock_manager.php"><span class="fa fa-area-chart"></span>&nbsp;&nbsp;Ingredient Stock Manager</a>
      <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <span class="fa fa-user-circle"></span>
        <?php echo $getfullname; ?>&nbsp;&nbsp;<img src="images/online.png" width="10">&nbsp;&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu">
          <li><a href="#" data-toggle="modal" data-target="#change">Change Password</a></li>
          <li>
            <form class="" action="header.php" method="post">
              <button class="btn btn-link" type="submit" name="logout">Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>

    <?php }?>
  </div>
</div>




