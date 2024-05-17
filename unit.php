<?php include 'components/login_check.php'; ?>
<?php
  // change password
  if(isset($_POST['btnchangepassword'])){

    $old_password = md5($_POST['old_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if(DB::query('SELECT Password FROM account_registration WHERE Fullname=:Fullname AND Password=:Password', array(':Fullname'=>$getfullname, ':Password'=>$old_password))){

      if($new_password == $confirm_password){

        $new_password = md5($new_password);

        DB::query('UPDATE account_registration SET Password=:Password WHERE Fullname=:Fullname', array(':Fullname'=>$getfullname,':Password'=>$new_password));

        $_SESSION["success_msg"] = "Your Password is Updated Successully!";
        header("Location: unit.php");
        die("password updated");
      }
      else{
        $_SESSION["warning_msg"] = "Your New Password did not match! Try again...";
        header("Location: unit.php");
        die("new password not match");
      }
    }
    else{
      $_SESSION["warning_msg"] = "Your Old Password did not match! Try again...";
      header("Location: unit.php");
      die("old password not match");
    }
  }

  // add data
  if(isset($_POST['add_data'])){

    $display_unit_name = $_POST['display_unit_name'];

    if(!DB::query('SELECT UnitName FROM tb_unit WHERE UnitName=:UnitName', array(':UnitName'=>$display_unit_name))){

      DB::query('INSERT INTO tb_unit (UnitName)
                          VALUES(:UnitName)',
                          array(':UnitName'=>$display_unit_name));

      $_SESSION["success_msg"] = "Data Added Successfully!";
      header("Location: unit.php");
      die("added");
    }
    else{
      $_SESSION["warning_msg"] = "Duplicate Entry of Item! Try again...";
      header("Location: unit.php");
      die("duplicated name");
    }
  }

  // delete data
  if(isset($_POST['delete_data'])){
    $display_id = $_POST['display_id'];

    DB::query('DELETE FROM `tb_unit` WHERE Id=:Id', array(':Id'=>$display_id));
    $_SESSION["success_msg"] = "Data Deleted Successfully!";
    header("Location: unit.php");
    die("deleted");
  }

  // update data
  if(isset($_POST['update_data'])){
    $display_id = $_POST['display_id'];
    $display_unit_name = $_POST['display_unit_name'];

    DB::query('UPDATE tb_unit SET UnitName=:UnitName WHERE Id=:Id', array(':UnitName'=>$display_unit_name, ':Id'=>$display_id));
    $_SESSION["success_msg"] = "Data Updated Successfully!";
    header("Location: unit.php");
    die("updated");
  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
  <title> Ingredient Stock Management System | Add / Manage Unit</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css"/>

    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css"/>
    
  </head>
  <body>
    <?php include_once 'header.php'; ?>

    <div class="container">
      <div class="container-fluid">
        <h3>Manage Unit </h3>
      </div>
      <?php
        if(isset($_SESSION["success_msg"])){
            echo '
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-success">
                            <strong>Success!</strong> &nbsp;'. $_SESSION["success_msg"] .'
                        </div>
                    </div>
                </div>
            ';
            unset($_SESSION["success_msg"]);
        }
        if(isset($_SESSION["warning_msg"])){
          echo '
              <div class="row">
                  <div class="col-sm-12">
                      <div class="alert alert-warning">
                          <strong>Warning!</strong> &nbsp;'. $_SESSION["warning_msg"] .'
                      </div>
                  </div>
              </div>
          ';
          unset($_SESSION["warning_msg"]);
        }
       ?>
      <div class="container">
        <div class="row">
          <div class="col-sm-4">
            <div class="panel panel-default">
              <div class="panel-heading"><span class="fa fa-cart-plus"></span>&nbsp;&nbsp;Add New Unit</div>
              <div class="panel-body">
                <?php
                      if(isset($alert)){
                          echo '
                              <div class="row">
                                  <div class="col-sm-12">
                                      <div class="alert alert-warning">
                                          <strong>Warning!</strong> &nbsp;'. $alert .'
                                      </div>
                                  </div>
                              </div>
                          ';
                      }

                      if(isset($success)){
                          echo '
                              <div class="row">
                                  <div class="col-sm-15">
                                      <div class="alert alert-success">
                                          <strong>Success!</strong> &nbsp;'. $success .'
                                      </div>
                                  </div>
                              </div>
                          ';
                      }
                  ?>
                <form class="form" action="unit.php" method="post">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label for="display_unit_name">Unit Name</label>
                      <input class="form-control" type="text" name="display_unit_name" id="display_unit_name" required>
                    </div>
                    <div class="form-group">
                      <input class="form-control btn btn-success" style="border-radius:0%;" type="submit" name="add_data" id="add_data" value="Add Data">
                    </div>
                    </div>
                </form>
              </div>
          </div>
          </div>
          <div class="col-sm-8">
            <div class="panel panel-danger" id="panel_table">
              <div class="panel-heading">Unit List</div>
              <div class="panel-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-bordered">
                    <thead>
                      <tr>
                        <th width="10%" class="sticky-col">Nama Kategori</th>
                        <th >Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $posts_display = $mysqli->query("SELECT * FROM tb_unit ORDER BY Id DESC");

                        while($posting = $posts_display->fetch_assoc()){
                          echo '
                            <form class="form" action="unit.php" method="post">
                              <tr>
                                <td class="sticky-col">
                                  <input type="text" value="'. $posting['Id'] .'" name="display_id" hidden>
                                  <input type="text" value="'. $posting['UnitName'] .'" class="editable" name="display_unit_name">
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" style="border-radius:0%;" type="submit" name="update_data">Update</button>
                                    <button class="btn btn-danger btn-sm delete-row" style="border-radius:0%;" type="button" data-id="'.$posting['Id'].'">Delete</button>
                                </td>
                              </tr>
                            </form>
                          ';
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

    <!-- Change Password Modal -->
    <div id="change" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><span class="fa fa-gear"></span>&nbsp;Change Password</h4>
          </div>
          <div class="modal-body">
                        <?php
                            if(isset($warning)){
                                echo '
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-warning">
                                                <strong>Warning!</strong> &nbsp;'. $warning .'
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }
                        ?>
            <form class="form" action="unit.php" method="POST">
              <div class="row">
                <div class="col-sm-12">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label for="old_password">Old Password</label>
                      <input class="form-control" type="password" name="old_password" id="old_password" required>
                    </div>
                    <div class="form-group">
                      <label for="new_password">New Password</label>
                      <input class="form-control" type="password" name="new_password" id="new_password" required>
                    </div>
                    <div class="form-group">
                      <label for="confirm_password">Confirm Password</label>
                      <input class="form-control" type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                    <div class="form-group">
                      <input class="form-control btn btn-danger" type="submit" name="btnchangepassword" value="Change Password">
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><span class="fa fa-trash-o"></span>&nbsp;Delete?</h4>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this Item?</p>
          </div>
          <div class="modal-footer">
            <form method="POST" action="unit.php" style="display: inline-block;">
              <input type="text" name="display_id" hidden>
              <button class="btn btn-danger" style="border-radius:0%;" type="submit" name="delete_data">Delete</button>
            </form>
            <button type="button" class="btn btn-default" style="border-radius:0%;" data-dismiss="modal">Cancel</button>
          </div>
        </div>

      </div>
    </div>
    
    <script src="vendor/jquery-3.2.1.min.js" charset="utf-8"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js" charset="utf-8"></script>
    
    <script>
      $(document).ready(function() {
        
        // on delete click
        const deleteBtns = document.querySelectorAll("button.delete-row");
        deleteBtns.forEach((deleteBtn) => {
          deleteBtn.addEventListener("click", (ev) => {
            ev.preventDefault();
            const deleteModalEl = document.getElementById("deleteModal");
            deleteModalEl.querySelector(`input[name="display_id"]`).value = ev.target.dataset.id;
            $('#deleteModal').modal();
          })
        });
        
      });
    </script>
  
  </body>
</html>
