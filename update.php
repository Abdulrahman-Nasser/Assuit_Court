<?php
include "shared/head.php";
include "shared/header.php";
include "shared/asside.php";
include "app/config.php";
include "app/functions.php";


// link url for listing the data
$link = $_SESSION['list']['link'];
$msg = [];
$erorr = [];
$error2 = [];
$erorr3 = [];
$date_error = [];
$erorr5 = [];
$error6 = [];


// update query
if (isset($_GET['update'])) {
  $id = $_GET['update'];

  // select for get data into inputs ----------------------------------------------------------------
  $select = "SELECT * From `$link` where id = $id ";
  $s = mysqli_query($conn, $select);
  $row = mysqli_fetch_assoc($s);

  // الملفات المسجلة قديماً--------------------------------------------------------------------------
  $old_files = json_decode($row['file']);

  // أسماء السمتأنفين حاليا-------------------------------------------------------------------------
  $current_appeal_name = json_decode($row['appeal_name']);
  $_SESSION['mahmoud'] = [
    'abdelrahem' => $current_appeal_name
  ];

  // أسماء المستأنفين ضده حاليا---------------------------------------------------------------------
  $current_appellant_name = json_decode($row['appellant_name']);
  $_SESSION['aboud'] = [
    'nasser' => $current_appellant_name
  ];

  // اسم المستأنف-------------------------------------------------------------------------------------
  if (isset($_POST['value'])) {
    $string = $_POST['appeal_name'];
    $delimiter = ',';

    $names = isset($string) && is_array($string) ? $string : [];

    if ($string) {
      $names = explode($delimiter, $string);
    }

    if (isset($_SESSION['mahmoud']) && isset($_SESSION['mahmoud']['abdelrahem'])) {
      $_SESSION['mahmoud']['abdelrahem'] = array_merge($_SESSION['mahmoud']['abdelrahem'], $names);
    } else {
      $_SESSION['mahmoud'] = [
        'abdelrahem' => $names,
      ];
    }
    $names_add = array_values($_SESSION['mahmoud']['abdelrahem']);
    $namesString1_add = mysqli_real_escape_string($conn, json_encode($names_add, JSON_UNESCAPED_UNICODE));
    $insert = "UPDATE `$link` SET `appeal_name`='$namesString1_add' WHERE id = $id";
    $result = mysqli_query($conn, $insert);
  }

  // أسم المستأنف ضده---------------------------------------------------------------------------------
  if (isset($_POST['value1'])) {
    $string1 = $_POST['appeal_name1'];
    $delimiter = ',';

    $names1 = isset($string1) && is_array($string1) ? $string1 : [];

    if ($string1) {
      $names1 = explode($delimiter, $string1);
    }

    if (isset($_SESSION['aboud']) && isset($_SESSION['aboud']['nasser'])) {
      $_SESSION['aboud']['nasser'] = array_merge($_SESSION['aboud']['nasser'], $names1);
    } else {
      $_SESSION['aboud'] = [
        'nasser' => $names1,
      ];
    }
    $appellants1_add = array_values($_SESSION['aboud']['nasser']);
    $appellantsString1_add = mysqli_real_escape_string($conn, json_encode($appellants1_add, JSON_UNESCAPED_UNICODE));
    // save the updated array to the file or database

    $insert = "UPDATE `$link` SET `appellant_name`='$appellantsString1_add' WHERE id = $id";
    $result = mysqli_query($conn, $insert);
  }



  // حذف اسماء المستأنفين-----------------------------------------------------------------------------
  if (isset($_GET['delete'])) {

    $index = $_GET['delete'];
    unset($_SESSION['mahmoud']['abdelrahem'][$index]);

    $names1 = array_values($_SESSION['mahmoud']['abdelrahem']);
    $namesString1 = mysqli_real_escape_string($conn, json_encode($names1, JSON_UNESCAPED_UNICODE));
    $insert = "UPDATE `$link` SET `appeal_name`='$namesString1' WHERE id = $id";
    $result = mysqli_query($conn, $insert);

    // save the updated array to the file or database
    path("update.php?update='$id'");
  }

  // حذف اسماء المستأنفين ضده-------------------------------------------------------------------------
  if (isset($_GET['delete1'])) {

    $index1 = $_GET['delete1'];
    unset($_SESSION['aboud']['nasser'][$index1]);
    $appellants1 = array_values($_SESSION['aboud']['nasser']);
    $appellantsString1 = mysqli_real_escape_string($conn, json_encode($appellants1, JSON_UNESCAPED_UNICODE));
    // save the updated array to the file or database

    $insert = "UPDATE `$link` SET `appellant_name`='$appellantsString1' WHERE id = $id";
    $result = mysqli_query($conn, $insert);
    path("update.php?update=$id");
  }


  // إضافة البيانات-------------------------------------------------------------------------------------
  if (isset($_POST['send'])) {

    $appeal_no = isset($_POST['appeal_no']) ? $_POST['appeal_no'] : '';
    $appeal_date = isset($_POST['appeal_date']) ? $_POST['appeal_date'] : '';
    $circle_no = isset($_POST['circle_no']) ? $_POST['circle_no'] : '';
    $history_ruling = isset($_POST['history_ruling']) ? $_POST['history_ruling'] : '';
    $note = isset($_POST['note']) ? $_POST['note'] : '';


    // start validation
    if ($appeal_no == "" || $appeal_date == '' || $circle_no == "" || $history_ruling == '' || $note == '') {
      $erorr[] = "لا يمكن ترك اي حقل فارغ ، رجائاً ملأ البيانات بشكل صحيح";
    }
    if ($appeal_date == '' || $history_ruling == '') {
      $date_error[] = "برجاء إختيار تاريخ صحيح";
    }
    if ($appeal_no == '') {
      $erorr5[] = "برجاء إدخال رقم الإستئناف";
    }
    if ($circle_no == '') {
      $erorr6[] = "برجاء إدخال رقم الدائرة";
    }


    // validatuon check for appeal name
    if (isset($_SESSION['mahmoud']['abdelrahem'])) {
      $names2 = $_SESSION['mahmoud']['abdelrahem'];
    } else {
      $error2[] = "يجب ادخال اسم المسأنف";
    }

    // validation check for appellants name
    if (isset($_SESSION['aboud']['nasser'])) {
      $appellants1 = $_SESSION['aboud']['nasser'];
    } else {
      $erorr3[] = "يجب إدخال اسم المستأنف ضده";
    }

    // validation check for appeal name number
    if (!empty($names2)) {
      $appeal_name_no = count($names2);
    } else {
      $appeal_name_no = null;
    }


    // vaildation check for appellants name number
    if (!empty($appellants1)) {
      $appellants_name_no = count($appellants1);
    } else {
      $appellants_name_no = null;
    }


    // checking for files is empty or not 
    if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {

      foreach ($old_files as $file_name) {
        unlink('uploads/' . $file_name);
      }
      $target_dir = "uploads/";
      $files = $_FILES['files'];
      $fileNames = [];
      $file_numbers = count($files['name']);

      for ($i = 0; $i < count($files['name']); $i++) {
        $file_name = time() . $files['name'][$i];
        $file_tmp = $files['tmp_name'][$i];
        $file_size = $files['size'][$i];
        $file_error = $files['error'][$i];

        // check for errors
        if ($file_error === 0) {
          $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
          $file_dest = $target_dir . $file_name;

          // move the uploaded file to the uploads directory
          if (move_uploaded_file($file_tmp, $file_dest)) {
            $fileNames[] = $file_name;
          } else {
            echo "خطأ";
          }
        }
      }
    } else {
      $file_numbers = count($old_files);
      $fileNames = $old_files;
    }

    // decoding the arraies
    $namesString = mysqli_real_escape_string($conn, json_encode($names2, JSON_UNESCAPED_UNICODE));
    $appellantsString = mysqli_real_escape_string($conn, json_encode($appellants1, JSON_UNESCAPED_UNICODE));
    $fileNamesString = mysqli_real_escape_string($conn, json_encode($fileNames, JSON_UNESCAPED_UNICODE));

    // checking if there is no error 
    if (empty($erorr) && empty($error2) && empty($erorr3) && empty($date_error)) {

      // update data into database
      $update = "UPDATE `$link` SET `Appeal_No`=$appeal_no,`Appeal_Date`='$appeal_date',`appeal_num`=$appeal_name_no,`appellant_num`=$appellants_name_no,`appeal_name`='$namesString',`appellant_name`='$appellantsString',`circle_no`=$circle_no,`history_ruling`='$history_ruling',`note`='$note',`file`='$fileNamesString',`file_numbers`=$file_numbers WHERE id = $id";
      $result = mysqli_query($conn, $update);
      path("list.php");
      if ($result) {
        $msg = "تم التعديل بنجاح";
        unset($_SESSION['mahmoud']);
        unset($_SESSION['aboud']);
      } else {
        $erorr[] = "خطأ في ارسال البيانات";
      }
    }
  }
}


auth_admin(1, 2);
?>

<!-- Start loading page -->
<div class="loading-spiner">
  <span class="loader"></span>
</div>
<!-- End loading page -->


<main id="main" class="main">



  <section class="section dashboard p-70">
    <div class="overlay"></div>


    <div class="container col-md-6 ">
      <div class="form-details p-4">

        <?php if (!empty($msg)) : ?>
          <div class="alret alert-success bg-success text-light text-success text-center m-3 p-3 msg">
            <?= $msg ?>
          </div>
        <?php endif; ?>

        <!-- message if there any errors -->
        <?php if (!empty($erorr)) : ?>
          <div class="alert alert-danger text-light bg-danger text-center p-3">
            <ul>
              <?php foreach ($erorr as $data) : ?>
                <li><?= $data ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" class="has-validation" novalidate>
          <div class="row justify-content-center">

            <!-- appeal number -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="" class="label">رقم الأستئناف</label>
                <input type="number" name="appeal_no" id="validationCustom01" value="<?= $row['Appeal_No'] ?>" class="form-control input_form" required>
              </div>

              <!-- message if there any errors -->
              <?php if (!empty($error5)) : ?>
                <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                  <?php foreach ($erorr5 as $data) : ?>
                    <strong> <?= $data ?></strong>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>


            <!-- appeal date -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="input_form" class="label">سنة الأستئناف</label>
                <input type="date" name="appeal_date" id="validationCustom02" required value="<?= $row['Appeal_Date'] ?>" class="form-control input_form">
              </div>


              <!-- message if there any errors -->
              <?php if (!empty($date_error)) : ?>
                <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                  <?php foreach ($date_error as $data) : ?>
                    <strong> <?= $data ?></strong>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>

            <!-- appeal name -->
            <div class="col-lg-6">
              <form action="" method="post">
                <div class="row">
                  <div class="col-md-10">

                    <div class="form-group">
                      <label for="" class="label"> اسم المستئنف </label>
                      <input type="text" id="" name="appeal_name" class="form-control input_form">
                    </div>


                    <!-- message if there any errors -->
                    <?php if (!empty($erorr2)) : ?>
                      <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                        <?php foreach ($erorr2 as $data) : ?>
                          <strong> <?= $data ?></strong>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>


                  </div>
                  <div class="col-md-2">

                    <div class="form-group">

                      <button id="addNameBtn" name="value" class="plus"><i class="bi bi-plus"></i></button>
                    </div>
                  </div>
                </div>

                <div id="namesList" class="lists">
                  <div class="row">
                    <?php foreach ($_SESSION['mahmoud']['abdelrahem'] as $index => $data) : ?>
                      <div class="col-md-6">
                        <span>
                          <?= $data ?>
                        </span>
                      </div>
                      <div class="col-md-6">
                        <a href="update.php?update=<?= $id ?>&delete=<?= $index ?>" class="text-danger"><i class="bi bi-trash float-start icon_delete"></i></a>
                      </div>
                      <hr>
                    <?php endforeach ?>
                  </div>
                </div>
              </form>

            </div>

            <!-- appeallant name -->
            <div class="col-lg-6">
              <form action="" method="post">
                <div class="row">
                  <div class="col-md-10">
                    <div class="form-group">
                      <label for="" class="label"> اسم المستئنف ضده </label>
                      <input type="text" name="appeal_name1" id="appellantInput" value="" class="form-control input_form">
                    </div>

                    <!-- message if there any errors -->
                    <?php if (!empty($erorr3)) : ?>
                      <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                        <?php foreach ($erorr3 as $data) : ?>
                          <strong> <?= $data ?></strong>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>

                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <button name="value1" id="addAppellantBtn" class="plus"><i class="bi bi-plus"></i></button>
                    </div>
                  </div>

                </div>
                <div id="appellantsList" class="lists">
                  <div class="row">
                    <?php foreach ($_SESSION['aboud']['nasser'] as $index1 => $data1) : ?>
                      <div class="col-md-6">
                        <span>
                          <?= $data1 ?>
                        </span>
                      </div>
                      <div class="col-md-6">
                        <a href="update.php?update=<?= $id ?>&delete1=<?= $index1 ?>" class="text-danger"><i class="bi bi-trash float-start icon_delete"></i></a>

                      </div>
                      <hr>
                    <?php endforeach ?>
                  </div>

                </div>
              </form>


            </div>


            <!-- circle number -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="" class="label">الدائرة</label>
                <input type="text" name="circle_no" id="validationCustom03" required value="<?= $row['circle_no'] ?>" class="form-control input_form" placeholder="رقم الدائرة">
              </div>

              <!-- message if there any errors -->
              <?php if (!empty($error6)) : ?>
                <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                  <?php foreach ($erorr6 as $data) : ?>
                    <strong> <?= $data ?></strong>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

            </div>


            <!-- history ruling -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="" class="label">تاريخ الحكم</label>
                <input type="date" name="history_ruling" id="validationCustom04" required value="<?= $row['history_ruling'] ?>" class="form-control input_form">
              </div>

              <!-- message if there any errors -->
              <?php if (!empty($date_error)) : ?>
                <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                  <?php foreach ($date_error as $data) : ?>
                    <strong> <?= $data ?></strong>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>


            <!-- منطوق الحكم -->
            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="label">منطوق الحكم</label>
                <textarea name="note" class="form-control text_area p-3" id="validationCustom01" required rows="3" cols="3" placeholder="أكتب شيئاً"><?= $row['note'] ?></textarea>
              </div>
            </div>

            <!-- رفع الملفات -->
            <div class="col-lg-12 mt-3">
              <div class="form-group">
                <label for="" class="label">
                  الملفات الحالية :</label>
                <?php foreach ($old_files as $data) : ?>
                  <a href="uploads/<?= $data ?>" class="glightbox">
                    <img src="uploads/<?= $data ?>" style="width:60px;" alt="">
                  </a>
                <?php endforeach; ?>
                <hr>
                <label for="" class="label">رفع ملفات جديدة</label>
                <input type="file" name="files[]" id="validationCustom05" required multiple class="form-control input_form mt-3">
              </div>
            </div>


            <div class="col-lg-3 mt-3">
              <button type="submit" name="send" class="btn_save">تعديل</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>

</main><!-- End #main -->



<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>



<?php
include "shared/footer.php";
include "shared/script.php";

?>