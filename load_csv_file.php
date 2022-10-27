<?php
// change here (add more columns if you want)
//just remmember change csv file line numbers
$database_name = "book_store";
$table_name="books";
$table_column_one = 'name';
$table_column_two = 'image';
$table_column_three = 'price';
$table_column_four = 'category';
$table_column_five = 'description';
// 
$conn = mysqli_connect("localhost","root","",$database_name);
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
            //just remmember change csv file line numbers
              $lineZero = $line[0];
              $lineOne = $line[1];
              $lineTwo = $line[2];
              $lineThree = $line[3];
              $lineFour = $line[4];
              $prevQuery = "INSERT INTO $table_name ($table_column_one,$table_column_two,$table_column_three,$table_column_four,$table_column_five) VALUES ('$lineZero','$lineOne','$lineTwo','$lineThree','$lineFour')";
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
if(isset($_GET['deleteAll'])){
  mysqli_query($conn,"DELETE FROM $table_name") or die("Delete Query Failed");
  header('location:load_csv_file.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <center>
    <?php if(!empty($message)){?><h1 onclick="this.remove()"><?php echo $message?></h1>
    <?php }?>
  </center>
  <br>
  <center>
    <form action="" method="post" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data"
      class="importForm">
      <div>
        <input type="file" name="file" id="file" accept=".csv">
        <button type="submit" id="import" name="import" class="btn btn-success">Import CSV</button>
        <br>
        <a href="test.php?deleteAll" onclick="return confirm('Are You Sure You Want To Delete Them All?')">Delete
          All</a>
      </div>
    </form>
  </center>
</body>

</html>