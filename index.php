<?php
$conn = mysqli_connect("localhost","root","","csv");
if(isset($_POST['import'])){
    $csvMimes = array('text/x-comma-separated-values','text/comma-separated-values','application/octet-stream','text/csv','application/excel','application/vnd.msexcel','text/plain');
    $file__name = $_FILES["file"]["name"];
    $file__tmp__name = $_FILES["file"]["tmp_name"];
    $file__type = $_FILES["file"]["type"];
    if(!empty($file__name)&& in_array($file__type, $csvMimes)){
        if(is_uploaded_file($file__tmp__name)){
            $csvFile = fopen($file__tmp__name, 'r');
            fgetcsv($csvFile);
            while(($line = fgetcsv($csvFile)) !== FALSE){
                $name = $line[0];
                $email = $line[1];
                $phone = $line[2];
                $age = $line[3];
                $prevQuery = "INSERT INTO people (name,email,phone,age) VALUES ('$name','$email','$phone','$age')";
                $upload_csv = mysqli_query($conn,$prevQuery);
            }
            if($upload_csv){
                $message = "CSV Data Imported Successfully";
            }else{
                
                $message = "Problem In Importing CSV";
            }
        }
    }
}
if(isset($_GET['delete'])){
    $delete__id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM people WHERE id = '$delete__id'") or die("Delete Query Failed");
    header('location:index.php');
}
if(isset($_GET['deleteAll'])){
    mysqli_query($conn,"DELETE FROM people") or die("Delete Query Failed");
    header('location:index.php');
}
if(isset($_GET['update'])){
  $updatad_id = $_GET['update'];
  $original_data = "SELECT * FROM people WHERE id ='$updatad_id'";
  $original_result = mysqli_query($conn,$original_data);
  if(mysqli_num_rows($original_result)>0){
    while($fetch_result = mysqli_fetch_assoc($original_result)){
      $original_id = $fetch_result['id'];
      $original_name = $fetch_result['name'];
      $original_email = $fetch_result['email'];
      $original_phone = $fetch_result['phone'];
      $original_age = $fetch_result['age'];
    }
  }
}
if(isset($_POST['inserted_btn'])){
    $inserted_id = $_POST['inserted_id'];
    $inserted_name = $_POST['inserted_name'];
    $inserted_email = $_POST['inserted_email'];
    $inserted_phone = $_POST['inserted_phone'];
    $inserted_age = $_POST['inserted_age'];
    if(isset($_GET['update'])){
      mysqli_query($conn , "UPDATE people SET name = '$inserted_name',email = '$inserted_email',phone = '$inserted_phone',age = '$inserted_age' WHERE id = '$inserted_id'") or die("Query Update Failed");
      $message="Person has been updated successfully!";
    }else{
      mysqli_query($conn , "INSERT INTO people (name,email,phone,age) VALUES ('$inserted_name' , '$inserted_email' , '$inserted_phone' , '$inserted_age')") or die("Query Insert Failed");
      $message="Person has been Inserted successfully!";
    }
    header('location:index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="libs/css/bootstrap.css" />
  <link rel="stylesheet" href="libs/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php if(!empty($message)){?><h2 class="message-box" onclick="this.remove()"><?php echo $message?></h2>
  <?php }?>
  <section class=" my-5">
    <div class="container ">
      <h1 class="section__header container-fluid">PEOPLE DATA </h1>
      <div class="result">
        <form class="update_container" method="POST">
          <input type="hidden" name="inserted_id" value="<?php if(isset($_GET['update'])) echo $original_id?>">
          <div class="update_box">
            <label for="inserted_name">Name</label>
            <input type="text" name="inserted_name" id="inserted_name"
              value="<?php if(isset($_GET['update'])) echo $original_name?>"
              <?php if(!isset($_GET['update'])) echo 'required'; ?>>
          </div>
          <div class="update_box">
            <label for="inserted_email">Email</label>
            <input type="email" name="inserted_email" id="inserted_email"
              value="<?php if(isset($_GET['update'])) echo $original_email?>"
              <?php if(!isset($_GET['update'])) echo 'required';?>>
          </div>
          <div class="update_box">
            <label for="inserted_phone">Phone</label>
            <input type="tel" name="inserted_phone" id="inserted_phone"
              value="<?php if(isset($_GET['update'])) echo $original_phone?>"
              <?php if(!isset($_GET['update'])) echo 'required';?>>
          </div>
          <div class="update_box">
            <label for="inserted_age">Age</label>
            <input type="number" name="inserted_age" id="inserted_age"
              value="<?php if(isset($_GET['update'])) echo $original_age?>"
              <?php if(!isset($_GET['update'])) echo 'required';?>>
          </div>
          <div class="update_buttons">
            <button type="submit" name="inserted_btn"
              class="btn btn-success m-1"><?php if(isset($_GET['update'])){ echo 'Update'; }else{ echo 'Insert';}?></button>
            <a href="index.php" class="btn btn-info m-1">Cancle</a>
          </div>
        </form>

        <div class="actions__container">
          <form action="" method="post" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data"
            class="importForm">
            <div>
              <input type="file" name="file" id="file" accept=".csv">
              <button type="submit" id="import" name="import" class="btn btn-success">Import CSV</button>
            </div>
            <a href="index.php?deleteAll" class="btn btn-danger m-1"
              onclick="return confirm('Are You Sure You Want To Delete Them All?')">Delete All</a>
          </form>
        </div>

        <table id="table_data" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Age</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $sqlselect = "SELECT * FROM people ";
            $result = mysqli_query($conn,$sqlselect);
            if(mysqli_num_rows($result)>0){
                while($row = mysqli_fetch_array($result)){
                    ?>
            <tr>
              <td><?php echo $row['id']?></td>
              <td><?php echo $row['name']?></td>
              <td><?php echo $row['email']?></td>
              <td><?php echo $row['phone']?></td>
              <td><?php echo $row['age']?></td>
              <td><a href="index.php?update=<?php echo $row['id']?>" class="btn btn-success m-1">Update</a><a
                  href="index.php?delete=<?php echo $row['id']?>" class="btn btn-danger m-1"
                  onclick="return confirm('Are You Sure You Want To Update User:  <?php echo $row['id']?>?')">Delete</a>

              </td>
            </tr>
            <?php }
                  }
                 ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
  <script src="libs/js/jquery-3.5.1.js"></script>
  <script src="libs/js/jquery.dataTables.min.js"></script>
  <script src="libs/js/dataTables.bootstrap4.min.js"></script>
  <script>
  $(document).ready(function() {
    $("#table_data").DataTable();
  });
  </script>
</body>

</html>