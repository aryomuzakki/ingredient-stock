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
        header("Location: ingredient.php");
        die("password updated");
      }
      else{
        $_SESSION["warning_msg"] = "Your New Password did not match! Try again...";
        header("Location: ingredient.php");
        die("new password not match");
      }
    }
    else{
      $_SESSION["warning_msg"] = "Your Old Password did not match! Try again...";
      header("Location: ingredient.php");
      die("old password not match");
    }
  }

  // Code ni para sa pag Add ug Ingredients Stock
  if(isset($_POST['add_item'])){

    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $updated_at  = $_POST['updated_at'];

    if(!DB::query('SELECT ItemName FROM stock_ingredients WHERE ItemName=:ItemName', array(':ItemName'=>$item_name))){

      DB::query('INSERT INTO stock_ingredients (ItemName, Category, Quantity, Unit, UpdatedAt, UserId)
                          VALUES(:ItemName, :Category, :Quantity, :Unit, :UpdatedAt, :UserId)',
                          array(':ItemName'=>$item_name, ':Category'=>$category, ':Quantity'=>$quantity, ':Unit'=>$unit, ':UpdatedAt'=>$updated_at, ":UserId"=>$logged_account["user_id"]));

      $_SESSION["success_msg"] = "Item Added Successfully!";
      header("Location: ingredient.php");
      die("added");
    }
    else{
      $_SESSION["warning_msg"] = "Duplicate Entry of Item! Try again...";
      header("Location: ingredient.php");
      die("duplicated name");
    }
  }

  // Code ni para sa pag Delete ug  Items
  if(isset($_POST['delete'])){
    $display_id = $_POST['display_id'];

    DB::query('DELETE FROM `stock_ingredients` WHERE Id=:Id', array(':Id'=>$display_id));
    $_SESSION["success_msg"] = "Item Deleted Successfully!";
    header("Location: ingredient.php");
    die("deleted");
  }

  // <!-- Code ni para sa pag Update ug  Items -->
  if(isset($_POST['update'])){
    $display_id = $_POST['display_id'];
    $display_item_name = $_POST['display_item_name'];
    $display_item_category = $_POST['display_item_category'] ?? null;
    $display_item_quantity = $_POST['display_item_quantity'] ?? null;
    $display_item_unit = $_POST['display_item_unit'] ?? null;
    $updated_at = (new DateTime("now"))->format('Y-m-d H:i:sP');

    DB::query('UPDATE stock_ingredients SET ItemName=:ItemName, Category=:Category, UserId=:UserId, UpdatedAt=:UpdatedAt, Quantity=:Quantity, Unit=:Unit WHERE Id=:Id', array(':ItemName'=>$display_item_name, ':Category'=>$display_item_category, ':UserId'=>$logged_account['user_id'], ':UpdatedAt'=>$updated_at, ':Quantity'=>$display_item_quantity, ':Unit'=>$display_item_unit, ':Id'=>$display_id));
    $_SESSION["success_msg"] = "Item Updated Successfully!";
    header("Location: ingredient.php");
    die("updated");
  }

  // data fetching
  $sortBy = $_GET["sb"] ?? "Id";
  $sortDir = array("1"=>"ASC", "2"=>"DESC")[$_GET["sd"] ?? "2"] ?? "DESC";

  $limit = intval($_GET["l"] ?? 25);
  $page = intval($_GET["p"] ?? 1);
  $offset = $limit * ($page - 1);

  $whereString = "";
  if (isset($_GET["query"])) {
    $query = $_GET["query"];
    $whereString = " WHERE ItemName LIKE '%".$query."%'
      OR Category LIKE '%".$query."%'
      OR UpdateAt LIKE '%".$query."%'
      OR Quantity LIKE '%".$query."%'
      OR Unit LIKE '%".$query."%'
    ";
  }

  $totalRowCount = $mysqli->query("SELECT COUNT(1) FROM stock_ingredients ")->fetch_assoc()["COUNT(1)"];
  $totalFilteredRow = $mysqli->query("SELECT COUNT(1) FROM stock_ingredients ".$whereString)->fetch_assoc()["COUNT(1)"];
  $totalPage = intval(ceil($totalFilteredRow / $limit));
  if ($totalPage > 0 && $page > $totalPage) {
    header("Location: ingredient.php");
    die("refresh");
  }
  $posts_display = $mysqli->query("SELECT i.Id, i.ItemName, tc.CategoryName, i.UpdatedAt, a.Fullname, i.Quantity, tu.UnitName
  FROM stock_ingredients as i LEFT JOIN account_registration as a ON i.UserId = a.Id LEFT JOIN tb_unit as tu ON i.UnitId = tu.Id LEFT JOIN tb_category as tc ON i.CategoryId = tc.Id ".$whereString." ORDER BY ".$sortBy." ".$sortDir." LIMIT ".$limit." OFFSET ".$offset);
  $fetchedRowCount = $posts_display->num_rows;
  // var_dump($totalPage);
  $firstRowNum = $totalPage > 0 ? (1 + $offset) : 0;

  // list category
  $category_list = $mysqli->query("SELECT * FROM tb_category ORDER BY Id ASC");
  $category_opts = "";
  while($category = $category_list->fetch_assoc()){                                     
    $category_opts .= '<option value="'.$category['Id'].'">'.$category['CategoryName'].'</option>';
  }

  // list unit
  $unit_list = $mysqli->query("SELECT * FROM tb_unit ORDER BY Id ASC");
  $unit_opts = "";
  while($unit = $unit_list->fetch_assoc()){                                     
    $unit_opts .= '<option value="'.$unit['Id'].'">'.$unit['UnitName'].'</option>';
  }
  
  // // list user
  // $user_list = $mysqli->query("SELECT * FROM account_registration ORDER BY Id ASC");
  // $user_arary = array();
  // while($user = $user_list->fetch_assoc()){                                     
  //   $user_array;
  // }

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
  <title> Ingredient Stock Management System | Add / Manage Stock</title>
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
        <h3>Manage Ingredient </h3>
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
        <div class="main-content-wrapper">
          <div class="panel panel-default">
            <div class="panel-heading flex-middle-between">
              <div>
                <span class="fa fa-cart-plus"></span>&nbsp;&nbsp;Add New Ingredient
              </div>
              <button class="btn btn-default" id="add-data-toggle">
                <span class="fa fa-caret-down"></span>
              </button>
            </div>
            <div class="panel-body d-none">
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
              <form class="form" action="ingredient.php" method="post">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="item_name">Item Name</label>
                    <input class="form-control" type="text" name="item_name" id="item_name" required>
                  </div>
                  <div class="form-group">
                    <label for="category">Category</label>
                    <select class="with-selectize form-control" id="category" name="category" required>
                      <?php echo $category_opts ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input class="form-control" type="number" min="1" name="quantity" id="quantity" required>
                  </div>
                  <div class="form-group">
                    <label for="unit">Satuan</label>
                    <select class="with-selectize form-control" id="unit" name="unit" required>
                      <?php echo $unit_opts ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="updated_at">Tanggal</label>
                    <input class="form-control" type="date" min="1" name="updated_at" id="updated_at" required>
                  </div>
                  <div class="form-group">
                    <input class="form-control btn btn-success" style="border-radius:0%;" type="submit" name="add_item" id="add_item" value="Add Item">
                  </div>
                  </div>
              </form>
            </div>
          </div>
          <div class="">
            <div class="filter-wrapper">
              <form action="" class="query-form">
                <label for="query" class="control-label">Cari</label>
                <input type="text" class="form-control" name="query" id="query" value="<?php echo $_GET["query"] ?? "" ?>" required>
                <button type="submit" class="btn btn-info btn-sm">
                  Cari
                </button>
              </form>
              <?php 
                if (isset($_GET["query"])) {
                  echo '
                  <form action="" class="reset-query-form">
                    <button type="submit" class="btn btn-danger btn-sm">
                      Hapus Pencarian
                    </button>
                  </form>';
                }
              ?>
              <div class="max-row-wrapper">
                <label for="max_row" class="control-label">Baris</label>
                <div style="max-width: max-content;">
                  <select name="max_row" id="max_row">
                    <?php echo '<option selected>'.($_GET["l"] ?? 25).'</option>'; ?>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="page-info">
              <?php
                echo "Halaman ".$page." dari ".$totalPage.". Menampilkan baris ".$firstRowNum."-".($offset + $fetchedRowCount).($whereString === "" ? " dari total ".$totalRowCount." baris." : " dari ".$totalFilteredRow." baris yang ditemukan. Total data : ".$totalRowCount." baris.");
              ?>
            </div>
            <div class="panel panel-danger" id="panel_table">
              <div class="panel-heading">Ingredient Stock List</div>
              <div class="panel-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-bordered">
                    <thead>
                      <tr>
                        <th width="10%" class="sortable-column sticky-col-1">
                          <button type="button" class="flex-middle-between" data-sort-dir="0" data-sort-by="Id">
                            No
                            <div>
                              <span class="fa fa-caret-up"></span>
                              <span class="fa fa-caret-down"></span>
                            </div>
                          </button>
                        </th>
                        <th width="10%" class="sortable-column sticky-col-2">
                          <button type="button" class="flex-middle-between" data-sort-dir="0" data-sort-by="ItemName">
                            Nama Barang
                            <div>
                              <span class="fa fa-caret-up"></span>
                              <span class="fa fa-caret-down"></span>
                            </div>
                          </button>
                        </th>
                        <th width="10%" class="sortable-column">
                          <button type="button" class="flex-middle-between" data-sort-dir="0" data-sort-by="Category">
                            Kategori
                            <div>
                              <span class="fa fa-caret-up"></span>
                              <span class="fa fa-caret-down"></span>
                            </div>
                          </button>
                        </th>
                        <th width="10%" class="sortable-column">
                          <button type="button" class="flex-middle-between" data-sort-dir="0" data-sort-by="UpdateAt">
                            Tanggal
                            <div>
                              <span class="fa fa-caret-up"></span>
                              <span class="fa fa-caret-down"></span>
                            </div>
                          </button>
                        </th>
                        <th width="10%" class="sortable-column">
                          <button type="button" class="flex-middle-between" data-sort-dir="0" data-sort-by="UserId">
                            User
                            <div>
                              <span class="fa fa-caret-up"></span>
                              <span class="fa fa-caret-down"></span>
                            </div>
                          </button>
                        </th>
                        <th width="12%" class="sortable-column">
                          <button type="button" class="flex-middle-between" data-sort-dir="0" data-sort-by="Quantity">
                            Kuantitas
                            <div>
                              <span class="fa fa-caret-up"></span>
                              <span class="fa fa-caret-down"></span>
                            </div>
                          </button>
                        </th>
                        <th width="12%" class="sortable-column">
                          <button type="button" class="flex-middle-between" data-sort-dir="0" data-sort-by="Unit">
                            Satuan
                            <div>
                              <span class="fa fa-caret-up"></span>
                              <span class="fa fa-caret-down"></span>
                            </div>
                          </button>
                        </th>
                        <th >Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $no = $firstRowNum;
                        if ($fetchedRowCount < 1) {
                          echo '
                            <tr><td colspan="6" style="text-align: left; font-style: italic; padding: 2rem;">Kosong.</td></tr>
                          ';
                        }
                        while($posting = $posts_display->fetch_assoc()){
                          echo '
                            <form class="form" action="ingredient.php" method="post">
                              <tr>
                                <td class="sticky-col-1">
                                  '.$no++.'
                                </td>
                                <td class="sticky-col-2">
                                  <input type="text" value="'. $posting['Id'] .'" name="display_id" hidden>
                                  <div class="scrollable-wrapper">
                                    <input type="text" value="'. $posting['ItemName'] .'" class="editable" name="display_item_name">
                                  </div>
                                </td>
                                <td>
                                  <select class="with-selectize select-in-table" name="display_item_category">
                                    <option>'. $posting['CategoryName'] .'</option>
                                    '.$category_opts.'
                                  </select>
                                </td>
                                <td>'. $posting['UpdatedAt'] .'</td>
                                <td>'. $posting['Fullname'] .'</td>
                                <td><input type="number" class="quantity form-control" min="0" value="'. $posting['Quantity'] .'" name="display_item_quantity"></td>
                                <td>
                                  <select class="with-selectize unit" name="display_item_unit">
                                    <option>'. $posting['UnitName'] .'</option>
                                    '.$unit_opts.'
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
                <nav aria-label="Page navigation" class="pagination-wrapper">
                  <ul class="pagination">
                    <?php if ($page === 1) {
                      echo '
                        <li class="disabled">
                          <span aria-label="Previous">
                            <span aria-hidden="true">&lt;</span>
                          </span>
                        </li>
                      ';
                    } else {
                        echo '
                          <li>
                            <a href="?p='.($page - 1).'" data-p="'.($page - 1).'" aria-label="Previous">
                              <span aria-hidden="true">&lt;</span>
                            </a>
                          </li>
                        ';
                      }
                    ?>
                    <?php
                    if ($totalPage>1) {
                      for ($i = 1; $i <= $totalPage; $i++) {
                        if ($page === $i) {
                          echo '<li class="active"><span>'.$i.'</span></li>';
                        } else {
                          echo '<li><a href="?p='.$i.'" data-p="'.$i.'">'.$i.'</a></li>';
                        }
                      }
                    } else {
                      echo '<li class="active"><span>1</span></li>';
                    }
                    ?>
                    <?php 
                      if ($page === $totalPage) {
                        echo '
                          <li class="disabled">
                            <span aria-label="Next">
                              <span aria-hidden="true">&gt;</span>
                            </span>
                          </li>
                        ';
                      } else {
                        echo '
                          <li>
                            <a href="?p='.($page + 1).'" data-p="'.($page + 1).'" aria-label="Next">
                              <span aria-hidden="true">&gt;</span>
                            </a>
                          </li>
                        ';
                      }
                    ?>
                  </ul>
                </nav>
              </div>
            </div>
            <div class="page-info" style="margin-bottom: 20px;">
              <?php
                echo "Halaman ".$page." dari ".$totalPage.". Menampilkan baris ".$firstRowNum."-".($offset + $fetchedRowCount).($whereString === "" ? " dari total ".$totalRowCount." baris." : " dari ".$totalFilteredRow." baris yang ditemukan. Total data : ".$totalRowCount." baris.");
              ?>
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
            <form class="form" action="ingredient.php" method="POST">
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
            <form method="POST" action="ingredient.php" style="display: inline-block;">
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
        // add data toggle
        const addDataToggle = document.getElementById("add-data-toggle");
        addDataToggle.addEventListener("click", (ev) => {
          ev.preventDefault();
          const addDataPanelBody = addDataToggle.parentElement.nextElementSibling;
          const isOpen = !addDataPanelBody.classList.contains("d-none");
          if (isOpen) {
            addDataPanelBody.classList.add("d-none");
            addDataPanelBody.classList.remove("d-block");
            addDataToggle.querySelector("span.fa").classList.add("fa-caret-down");
            addDataToggle.querySelector("span.fa").classList.remove("fa-caret-up");
          } else {
            addDataPanelBody.classList.remove("d-none");
            addDataPanelBody.classList.add("d-block");
            addDataToggle.querySelector("span.fa").classList.remove("fa-caret-down");
            addDataToggle.querySelector("span.fa").classList.add("fa-caret-up");
          }
        })

        // filtering
        // query
        // limit
        const setRowLimit = (value) => {
          const urlWithLimit = new URL(window.location.href);
          urlWithLimit.searchParams.set("l", value);
          window.location = urlWithLimit;
        };

        // sort
        const sortableBtns = document.querySelectorAll(".sortable-column>button");
        sortableBtns.forEach((sortableBtn, idx) => {
          sortableBtn.addEventListener("click", (ev) => {
            ev.preventDefault();
            console.log("idx: ", idx);
            console.log(ev.target.dataset.sortBy);
            console.log(ev.target.dataset.sortDir);
            
            let sortDir = parseInt(ev.target.dataset.sortDir);
            const newSortDir = (sortDir < 2 ? ++sortDir : 0).toString();
            // ev.target.dataset.sortDir = newSortDir;
            // set on page load instead
            
            const urlWithSort = new URL(window.location.href);
            if (newSortDir === "0") {
              urlWithSort.searchParams.delete("sb");
              urlWithSort.searchParams.delete("sd");
            } else {
              urlWithSort.searchParams.set("sb", ev.target.dataset.sortBy);
              urlWithSort.searchParams.set("sd", newSortDir);
            }
            console.log(urlWithSort.href);

            // // reset all other sort dir
            // sortableBtns.forEach((sBtn, id) => {
            //   if (id === idx) return;
            //   sBtn.dataset.sortDir = "0";
            // });
            // set when page load instead

            window.location = urlWithSort;
          });
        });
        sortableBtns.forEach((sortableBtn, idx) => {
          const currentURL = new URL(window.location.href);
          const sb = currentURL.searchParams.get("sb");
          const sd = currentURL.searchParams.get("sd");
          if (sortableBtn.dataset.sortBy === sb) {
            sortableBtn.dataset.sortDir = sd || "1";
          } else {
            sortableBtn.dataset.sortDir = "0";
          }
        });

        // pagination
        const pageBtns = document.querySelectorAll(".pagination li>a");
        pageBtns.forEach((pageBtn) => {
          pageBtn.addEventListener("click", (ev)=>{
            ev.preventDefault();
            const urlWithPagination = new URL(window.location);
            urlWithPagination.searchParams.set("p", pageBtn.dataset.p);
            window.location = urlWithPagination;
          });
        });
       
        // init selectize
        $("select.with-selectize").selectize({
          plugins: ["auto_position"],
          score: function (search) {
            const score = this.getScoreFunction(search);
            return (item) => {
                return score(item) ? 1 : 0;
            };
          },
        });

        // selectize for max row limit
        $("select#max_row").selectize({
          plugins: ["auto_position"],
          onChange: setRowLimit,
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

            submitForm("ingredient.php", [...tableFormData.entries()]);
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
