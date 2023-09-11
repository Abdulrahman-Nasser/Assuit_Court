<?php
include "shared/head.php";
include "shared/header.php";
include "shared/asside.php";
include "app/config.php";
include "app/functions.php";

// empty variable to view old data


$link = $_GET['link_variable'];

$circle_no = '';
$msg = [];
$erorr = [];
$error2 = [];
$erorr3 = [];
$error4 = [];
$date_error = [];
$error5 = [];

// appeal code
if (isset($_POST['value'])) {
  $appeal_no = isset($_POST['appeal_no']) ? $_POST['appeal_no'] : '';
  $circle_no = isset($_POST['circle_no']) ? $_POST['circle_no'] : '';
  $note = isset($_POST['note']) ? $_POST['note'] : '';

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
      'url' => $link
    ];
  }
}

// appellants code
if (isset($_POST['value1'])) {
  $appeal_no = isset($_POST['appeal_no']) ? $_POST['appeal_no'] : '';
  $circle_no = isset($_POST['circle_no']) ? $_POST['circle_no'] : '';
  $note = isset($_POST['note']) ? $_POST['note'] : '';

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
      'url' => $link
    ];
  }
}

// delete session aboud && mahmoud
if (isset($_POST['delete_session_aboud'])) {
  unset($_SESSION['aboud']['nasser']);
} else if (isset($_POST['delete_session_mahmoud'])) {
  unset($_SESSION['mahmoud']['abdelrahem']);
}

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
  if (!isset($_SESSION['aboud']) && !isset($_SESSION['mahmoud'])) {
    $erorr[] = "لا يمكن ترك اسم المستئنف أو اسم السمتئنف ضده فارغين";
  }



  // validatuon check for appeal name
  if (isset($_SESSION['mahmoud']['abdelrahem'])) {
    $names2 = $_SESSION['mahmoud']['abdelrahem'];
    $appeal_name_no = count($names2);
    $namesString = mysqli_real_escape_string($conn, json_encode($names2, JSON_UNESCAPED_UNICODE));
  } else {
    $error2[] = "يجب ادخال أسم المسأنف";
  }



  // validation check for appellants name
  if (isset($_SESSION['aboud']['nasser'])) {
    $appellants1 = $_SESSION['aboud']['nasser'];
    $appellants_name_no = count($appellants1);
    $appellantsString = mysqli_real_escape_string($conn, json_encode($appellants1, JSON_UNESCAPED_UNICODE));
  } else {
    $erorr3[] = "يجب إدخال اسم المستأنف ضده";
  }

  // // validation check for appeal name number
  // if (!empty($names2)) {
  //   $appeal_name_no = count($names2);
  // } else {
  //   $appeal_name_no = null;
  // }

  // // vaildation check for appellants name number
  // if (!empty($appellants1)) {
  //   $appellants_name_no = count($appellants1);
  // } else {
  //   $appellants_name_no = null;
  // }


  // decoding the array
  // $namesString = mysqli_real_escape_string($conn, json_encode($names2, JSON_UNESCAPED_UNICODE));
  // $appellantsString = mysqli_real_escape_string($conn, json_encode($appellants1, JSON_UNESCAPED_UNICODE));


  // import files -----------------------------------------------------------------
  if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
    $target_dir = "uploads/";
    $files = $_FILES['files'];
    $fileNames = [];

    // count number of files
    for ($i = 0; $i < count($files['name']); $i++) {
      $file_name = time() . $files['name'][$i];
      $file_tmp = $files['tmp_name'][$i];
      $file_size = $files['size'][$i];
      $file_error = $files['error'][$i];

      // check for errors
      if ($file_error === 0) {
        $file_dest = $target_dir . $file_name;

        // move the uploaded file to the uploads directory
        if (move_uploaded_file($file_tmp, $file_dest)) {
          // echo "File $file_name uploaded successfully!<br>";
          $fileNames[] = $file_name;
        } else {
          echo "There was an error uploading file $file_name.<br>";
        }
      } else {
        echo "Error uploading file $file_name.<br>";
      }
    }
    $fileNamesString = mysqli_real_escape_string($conn, json_encode($fileNames, JSON_UNESCAPED_UNICODE));
    $file_numbers = count($files['name']);
  } else {
    $error4[] = "يجب إرفاق ملفات";
  }


  // if there is no errors , inserting data
  if (empty($erorr) && empty($error2) && empty($erorr3) && empty($error4) && empty($date_error)) {

    // insert data
    $insert = "INSERT INTO `$link` (`id`, `Appeal_No`, `Appeal_Date`,`appeal_num`,`appellant_num`, `appeal_name`, `appellant_name`, `circle_no`, `history_ruling`, `note`,`file`,`file_numbers`) VALUES (null,$appeal_no,'$appeal_date',$appeal_name_no,$appellants_name_no,'$namesString','$appellantsString','$circle_no','$history_ruling','$note','$fileNamesString',$file_numbers)";
    $result = mysqli_query($conn, $insert);

    // check for valid data true or not
    if ($result) {
      $msg = "تم الاضافة بنجاح";
      unset($_SESSION['mahmoud']['abdelrahem']);
      unset($_SESSION['aboud']['nasser']);
      // path('list.php');
    } else {
      $erorr[] = "خطأ في ارسال البيانات";
    }
  }
}


auth_admin(2, 3);
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

        <!-- message for succes insert -->
        <?php if (!empty($msg)) : ?>
          <div class="alret alert-success bg-success text-light text-success text-center m-3 p-3 msg">
            <?= $msg ?>
          </div>
        <?php endif; ?>

        <!-- form for insert the data -->
        <form action="" method="post" enctype="multipart/form-data" class="">
          <div class="row justify-content-center">

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

            <!-- رقم الأستئناف -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="" class="label">رقم الأستئناف</label>
                <input type="number" name="appeal_no" value="<?= $appeal_no ?>" id="validationCustom01" class="form-control  input_form">
              </div>
              <!-- message if there any errors -->
              <?php if (!empty($error5)) : ?>
                <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                  <?php foreach ($date_error as $data) : ?>
                    <strong> <?= $data ?></strong>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>

            <!-- سنة الأستئناف -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="input_form" class="label">سنة الأستئناف</label>
                <input type="date" name="appeal_date" class="form-control input_form" id="validationCustom02">
              </div>

              <!-- message if there any errors -->
              <?php if (!empty($date_error)) : ?>
                <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                  <?php foreach ($date_error as $data) : ?>
                    <strong> <?= $data ?></strong>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="invalid-feedback mt-2">
                برجاء ادخال سنة الأستئناف
              </div>
            </div>


            <!-- اسم المستئنف -->
            <div class="col-lg-6">
              <div class="row">
                <div class="col-md-10">

                  <div class="form-group">
                    <label for="" class="label"> اسم المستئنف </label>
                    <input type="text" id="" name="appeal_name" class="form-control input_form">
                  </div>

                  <!-- message if there any errors -->
                  <?php if (!empty($erorr2)) : ?>
                    <div class="alert alert-danger text-danger bg-transparent text-center ">
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

                <?php if (isset($_SESSION['mahmoud']['abdelrahem'])) : ?>
                  <div id="namesList" class="lists">
                    <?php foreach ($_SESSION['mahmoud']['abdelrahem'] as $data) : ?>
                      <span class="mt-3">
                        <?= $data ?>
                      </span>
                      <br>
                    <?php endforeach; ?>
                    <div class="col-md-12 text-center">
                      <button name="delete_session_mahmoud" class="danger_btn"><i class="bi bi-trash"></i></button>
                    </div>
                  </div>
                <?php endif; ?>
              </div>

            </div>

            <!-- اسم المسئنف ضده -->
            <div class="col-lg-6">
              <form action="" method="post">
                <div class="row">

                  <div class="col-md-10">
                    <div class="form-group">
                      <label for="" class="label"> اسم المستئنف ضده </label>
                      <input type="text" name="appeal_name1" id="appellantInput" class="form-control input_form">
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
                      <button name="value1" class="plus"><i class="bi bi-plus"></i></button>
                    </div>
                  </div>
                </div>

                <?php if (isset($_SESSION['aboud']['nasser'])) : ?>
                  <div id="namesList" class="lists">
                    <?php foreach ($_SESSION['aboud']['nasser'] as $data) : ?>
                      <span class="py-3">
                        <?= $data ?>
                      </span>
                      <br>
                    <?php endforeach; ?>
                    <div class="col-md-12 text-center">
                      <button name="delete_session_aboud" class="danger_btn"><i class="bi bi-trash"></i></button>
                    </div>
                  </div>


                <?php endif; ?>
              </form>



            </div>

            <!-- الدائرة -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="" class="label">الدائرة</label>
                <input type="text" name="circle_no" value="<?= $circle_no ?>" class="form-control input_form" id="validationCustom03" placeholder="رقم الدائرة">
              </div>
              <div class="invalid-feedback mt-2">
                يجب اختيار رقم دائرة صحيح.
              </div>
            </div>

            <!-- تاريخ الحكم -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="" class="label">تاريخ الحكم</label>
                <input type="date" name="history_ruling" id="validationCustom04" class="form-control input_form">
              </div>

              <!-- message if there any errors -->
              <?php if (!empty($date_error)) : ?>
                <div class="alert alert-danger text-danger bg-transparent text-center border-0">
                  <?php foreach ($date_error as $data) : ?>
                    <strong> <?= $data ?></strong>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="invalid-feedback mt-2">
                يجب اختيار تاريخ الحكم .
              </div>
            </div>

            <!-- منطوق الحكم -->
            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="label">منطوق الحكم</label>
                <textarea name="note" value="" class="form-control text_area p-3" id="validationCustom04" rows="3" cols="3" placeholder="أكتب شيئاً"><?php if (!empty($note)) : ?><?= $note ?><?php endif; ?></textarea>
              </div>
              <div class="invalid-feedback mt-2">
                يجب اختيار منطوق الحكم .
              </div>
            </div>

            <!-- الملف -->
            <div class="col-lg-12">
              <div class="form-group">
                <label for="" class="label">رفع الملف </label>
                <input type="file" name="files[]" multiple class="form-control input_form" id="validationCustom04">
              </div>

              <!-- message if there any errors -->
              <?php if (!empty($erorr4)) : ?>
                <div class="alert alert-danger text-danger bg-transparent text-center ">
                  <?php foreach ($erorr4 as $data) : ?>
                    <strong> <?= $data ?> </strong>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

            </div>

            <div class="col-lg-3 mt-3">
              <button type="submit" name="send" class="btn_save">حفظ</button>
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