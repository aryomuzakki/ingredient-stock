<?php include 'components/login_check.php'; ?>
<?php
  // Code ni para sa pag Change Password
  if(isset($_POST['btnchangepassword'])){

    $old_password = md5($_POST['old_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if(DB::query('SELECT Password FROM account_registration WHERE Fullname=:Fullname AND Password=:Password', array(':Fullname'=>$getfullname, ':Password'=>$old_password))){

      if($new_password == $confirm_password){

        $new_password = md5($new_password);

        DB::query('UPDATE account_registration SET Password=:Password WHERE Fullname=:Fullname', array(':Fullname'=>$getfullname,':Password'=>$new_password));

        $_SESSION["success_msg"] = "Your Password is Updated Successully!";
        header("Location: add_stock.php");
        die("password updated");
      }
      else{
        $_SESSION["warning_msg"] = "Your New Password did not match! Try again...";
        header("Location: add_stock.php");
        die("new password not match");
      }
    }
    else{
      
    }
  }

  // Code ni para sa pag Add ug Ingredients Stock
  if(isset($_POST['add_item'])){

    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $date  = $_POST['date'];

    if(!DB::query('SELECT ItemName FROM stock_ingredients WHERE ItemName=:ItemName', array(':ItemName'=>$item_name))){

      DB::query('INSERT INTO stock_ingredients (ItemName, Category, Quantity, Unit, Date)
                          VALUES(:ItemName, :Category, :Quantity, :Unit, :Date)',
                          array(':ItemName'=>$item_name, ':Category'=>$category, ':Quantity'=>$quantity, ':Unit'=>$unit, ':Date'=>$date));

      $_SESSION["success_msg"] = "Item Added Successfully!";
      header("Location: add_stock.php");
      die("added");
    }
    else{
      $_SESSION["warning_msg"] = "Duplicate Entry of Item! Try again...";
      header("Location: add_stock.php");
      die("duplicated name");
    }
  }

  // Code ni para sa pag Delete ug  Items
  if(isset($_POST['delete'])){
    $display_id = $_POST['display_id'];

    DB::query('DELETE FROM `stock_ingredients` WHERE Id=:Id', array(':Id'=>$display_id));
    $_SESSION["success_msg"] = "Item Deleted Successfully!";
    header("Location: add_stock.php");
    die("deleted");
  }

  // <!-- Code ni para sa pag Update ug  Items -->
  if(isset($_POST['update'])){
    $display_id = $_POST['display_id'];
    $display_item_name = $_POST['display_item_name'];
    $display_item_category = $_POST['display_item_category'] ?? null;
    $display_item_quantity = $_POST['display_item_quantity'] ?? null;
    $display_item_unit = $_POST['display_item_unit'] ?? null;

    DB::query('UPDATE stock_ingredients SET ItemName=:ItemName, Category=:Category, Quantity=:Quantity, Unit=:Unit WHERE Id=:Id', array(':ItemName'=>$display_item_name, ':Category'=>$display_item_category, ':Quantity'=>$display_item_quantity, ':Unit'=>$display_item_unit, ':Id'=>$display_id));
    $_SESSION["success_msg"] = "Item Updated Successfully!";
    header("Location: add_stock.php");
    die("updated");
  }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
  <title> Ingredients Stock Management System | Add / Manage Stock</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.bootstrap3.min.css" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css"/>
    
  </head>
  <body>
    <?php include_once 'header.php'; ?>

    <div class="container">
      <div class="container-fluid">
        <h3>Manage Ingredients </h3>
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
              <div class="panel-heading"><span class="fa fa-cart-plus"></span>&nbsp;&nbsp;Add New Ingredients</div>
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
                <form class="form" action="add_stock.php" method="post">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label for="item_name">Item Name</label>
                      <input class="form-control" type="text" name="item_name" id="item_name" required>
                    </div>
                    <div class="form-group">
                      <label for="category">Category</label>
                      <select class="form-control" id="category" name="category" required>
                        <option value="">--Please Select--</option>
                        <option value="Bread">Bread</option>
                        <option value="Meat">Meat</option>
                        <option value="Sea Food">Sea Food</option>
                        <option value="Patty">Patty</option>
                        <option value="Fruits">Fruits</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="DairyProducts">Dairy Products</option>
                        <option value="Lainnya">Lainnya</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="quantity">Quantity</label>
                      <input class="form-control" type="number" min="1" name="quantity" id="quantity" required>
                    </div>
                    <div class="form-group">
                      <label for="unit">Satuan</label>
                      <select class="form-control" id="unit" name="unit" required>
                        <option value="">--Please Select--</option>
                        <option value="Kilogram">Kilogram</option>
                        <option value="Gram">Gram</option>
                        <option value="Miligram">Miligram</option>
                        <option value="Ons">Ons</option>
                        <option value="Liter">Liter</option>
                        <option value="Mililiter">Mililiter</option>
                        <option value="Pieces">Pieces</option>
                        <option value="Butir">Butir</option>
                        <option value="Papan">Papan</option>
                        <option value="Lusin">Lusin</option>
                        <option value="Rol">Rol</option>
                        <option value="Blok">Blok</option>
                        <option value="Bal">Bal</option>
                        <option value="Batang">Batang</option>
                        <option value="Buah">Buah</option>
                        <option value="Bungkus">Bungkus</option>
                        <option value="Pack">Pack</option>
                        <option value="Kotak">Kotak</option>
                        <option value="Kaleng">Kaleng</option>
                        <option value="Botol">Botol</option>
                        <option value="Tabung">Tabung</option>
                        <option value="Galon">Galon</option>
                        <option value="Lainnya">Lainnya</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="date">Date</label>
                      <input class="form-control" type="date" min="1" name="date" id="date" required>
                    </div>
                    <div class="form-group">
                      <input class="form-control btn btn-success" style="border-radius:0%;" type="submit" name="add_item" id="add_item" value="Add Item">
                    </div>
                    </div>
                </form>
              </div>
          </div>
          </div>
          <div class="col-sm-8">
            <div class="panel panel-danger" id="panel_table">
              <div class="panel-heading">Ingredients Stock List</div>
              <div class="panel-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-bordered">
                    <thead>
                      <tr>
                        <th width="10%" class="sticky-col">Nama Barang</th>
                        <th width="10%">Kategori</th>
                        <th width="10%">Tanggal</th>
                        <th width="12%">Kuantitas</th>
                        <th width="12%">Satuan</th>
                        <th >Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $posts_display = $mysqli->query("SELECT * FROM stock_ingredients ORDER BY Id DESC");

                        while($posting = $posts_display->fetch_assoc()){
                          echo '
                            <form class="form" action="add_stock.php" method="post">
                              <tr>
                                <td class="sticky-col">
                                  <input type="text" value='. $posting['Id'] .' name="display_id" hidden>
                                  <input type="text" value='. $posting['ItemName'] .' id="readonly" name="display_item_name">
                                </td>
                                <td>
                                  <select class="select-in-table" name="display_item_category">
                                    <option>'. $posting['Category'] .'</option>
                                    <option value="Bread">Bread</option>
                                    <option value="Meat">Meat</option>
                                    <option value="Sea Food">Sea Food</option>
                                    <option value="Patty">Patty</option>
                                    <option value="Fruits">Fruits</option>
                                    <option value="Vegetables">Vegetables</option>
                                    <option value="DairyProducts">Dairy Products</option>
                                    <option value="Others">Others</option>
                                  </select>
                                </td>
                                <td>'. $posting['Date'] .'</td>
                                <td><input type="number" class="quantity form-control" min="0" value='. $posting['Quantity'] .' name="display_item_quantity"></td>
                                <td>
                                  <select class="unit" name="display_item_unit">
                                    <option>'. $posting['Unit'] .'</option>
                                    <option value="Kilogram">Kilogram</option>
                                    <option value="Gram">Gram</option>
                                    <option value="Miligram">Miligram</option>
                                    <option value="Ons">Ons</option>
                                    <option value="Liter">Liter</option>
                                    <option value="Mililiter">Mililiter</option>
                                    <option value="Pieces">Pieces</option>
                                    <option value="Butir">Butir</option>
                                    <option value="Papan">Papan</option>
                                    <option value="Lusin">Lusin</option>
                                    <option value="Rol">Rol</option>
                                    <option value="Blok">Blok</option>
                                    <option value="Bal">Bal</option>
                                    <option value="Batang">Batang</option>
                                    <option value="Buah">Buah</option>
                                    <option value="Bungkus">Bungkus</option>
                                    <option value="Pack">Pack</option>
                                    <option value="Kotak">Kotak</option>
                                    <option value="Kaleng">Kaleng</option>
                                    <option value="Botol">Botol</option>
                                    <option value="Tabung">Tabung</option>
                                    <option value="Galon">Galon</option>
                                    <option value="Lainnya">Lainnya</option>
                                  </select>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" style="border-radius:0%;" type="submit" name="update">Update</button>
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
            <form class="form" action="add_stock.php" method="POST">
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
            <form method="POST" action="add_stock.php" style="display: inline-block;">
              <input type="text" name="display_id" hidden>
              <button class="btn btn-danger" style="border-radius:0%;" type="submit" name="delete">Delete</button>
            </form>
            <button type="button" class="btn btn-default" style="border-radius:0%;" data-dismiss="modal">Cancel</button>
          </div>
        </div>

      </div>
    </div>
    
    <script src="vendor/jquery-3.2.1.min.js" charset="utf-8"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js" integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
      $(document).ready(function() {
        // init selectize
        $("select").selectize({
          plugins: ["auto_position"],
          score: function (search) {
            const score = this.getScoreFunction(search);
            return (item) => {
                return score(item) ? 1 : 0;
            };
          },
        });

        // simulate form submit
        const submitForm = (url, data) => {
          const formEl = document.createElement("form");
          formEl.style.visibility = "hidden";
          formEl.method = "POST";
          formEl.action = url;
          for (const [name, value] of data) {
            console.log(name+ ', ' + value); 
            const inputEl = document.createElement("input");
            inputEl.name = name;
            inputEl.value = value;
            formEl.appendChild(inputEl);
          }
          document.body.appendChild(formEl);
          formEl.submit();
        }
        
        // fix selectize in table
        $("table form").each((i,el) => {
          el.addEventListener("submit", (ev) => {
            ev.preventDefault();
            
            const tableFormData = new FormData(ev.target);
            
            tableFormData.append("display_item_category", ev.target.nextElementSibling.querySelector(`select[name="display_item_category"`).value);
            tableFormData.append("display_item_unit", ev.target.nextElementSibling.querySelector(`select[name="display_item_unit"`).value);
            tableFormData.append("update", "");

            submitForm("add_stock.php", [...tableFormData.entries()]);
          });
        });
        
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
