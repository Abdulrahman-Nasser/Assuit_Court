<?php
include 'shared/head.php';
include 'shared/header.php';
include 'shared/asside.php';
include 'app/config.php';
include 'app/functions.php';





$link = $_SESSION['list']['link'];
$id = $_GET['show'];

$select = "SELECT * FROM `$link` where id = $id";
$s = mysqli_query($conn, $select);
$row = mysqli_fetch_assoc($s);
$data = json_decode($row['file']);
$name = json_decode($row['appeal_name']);
$appeallant = json_decode($row['appellant_name']);





?>

<!-- Start loading page -->
<div class="loading-spiner">
  <span class="loader"></span>
</div>
<!-- End loading page -->

<div class="scroll-area left-scroll" id="scrollContainer">
  <?php foreach ($data as $item) : ?>
    <div class="col-lg-12 left ">
      <div class="img" id="div1">
        <div class="col ">
          <a href="uploads/<?= $item ?>?img=<?= $item ?>" class="glightbox">
            <img src="uploads/<?= $item ?>" alt="">
          </a>
        </div>
      </div>
      <div class="down mt-3">
        <form action="download.php" method="post">
          <input type="hidden" name="image" value="<?= $item ?>">
          <span>أسم الملف : <?= $item ?></span>
          <hr>
          <div class="row justify-content-center m-0 p-0">
            <div class="col">
              <button id="newBtn" class="" name="download"><i class="bi bi-download"></i></button>
            </div>

            <div class="col">
              <a href="uploads/<?= $item ?>?img=<?= $item ?>" id="newBtn" class="eye glightbox" name="download"><i class="bi bi-eye"></i></a>
            </div>

          </div>
        </form>
        <div class="col text-center">
          <button id="printBtn" class="print_btn" onclick="printImage('<?= $item ?>')"><i class="bi bi-printer"></i></button>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <hr>
  <div class="col-md-12 text-center">
    <button id="btn1" class="print_btn"> طباعة المستندات <i class="bi bi-printer"></i> </button>
  </div>
  <div class="col-md-12 text-center">
    <button id="downloadAllBtn" class="print_btn donwload_btn" onclick="downloadAllImages()">تحميل جميع الصور <i class="bi bi-download"></i></button>
  </div>


</div>



<main id="main" class="main ">

  <section class="section p-70">


    <div class="container col-md-8 ">
      <div class="row justify-content-center">

        <!-- Recent Sales -->
        <div id="pageContainer" class="">
          <div class="col-lg-10 bg-light container-print" id="div2">

            <div class="pagetitle text-center">
              <h1> البيانات التفصيلية</h1>
            </div><!-- End Page Title -->
            <div class="row">
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> رقم الأستئناف :</span>
                </strong>
                <span class=""><strong><?= $row['Appeal_No'] ?></strong></span>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> تاريخ الإستئناف :</span>
                </strong>
                <span class=""><strong><?= $row['Appeal_Date'] ?></strong></span>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> عدد المستأنفين :</span>
                </strong>
                <span class=""><strong><?= $row['appeal_num'] ?></strong></span>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> عدد المستأنفين ضده:</span>
                </strong>
                <span class=""><strong><?= $row['appellant_num'] ?></strong></span>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> اسماء المستأنفين :</span>
                </strong>
                <ul>

                  <?php foreach ($name as $index => $data) : ?>
                    <span class="">
                      <li style="list-style: none;"><?= $index + 1 ?> - <?= $data ?></li>
                    </span>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> اسماء المستأنفين ضده:</span>
                </strong>
                <ul>
                  <?php foreach ($appeallant as $index => $data) : ?>
                    <span class="">
                      <li style="list-style: none;"><?= $index + 1 ?> - <?= $data ?></li>
                    </span>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> الدائرة:</span>
                </strong>
                <span class=""><strong><?= $row['circle_no'] ?></strong></span>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> تاريخ الحكم :</span>
                </strong>
                <span class=""><strong><?= $row['history_ruling'] ?></strong></span>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> عدد المستندات :</span>
                </strong>
                <span class=""><strong><?= $row['file_numbers'] ?></strong></span>
              </div>
              <div class="col-md-6 col-sm-6 p-4 view">
                <strong>
                  <span> منطوق الحكم :</span>
                </strong>
                <span class=""><strong><?= $row['note'] ?></strong></span>
              </div>


            </div>
          </div><!-- End Recent Sales -->

        </div>
        <div class="col-md-8 text-center btn-parent mt-3">
          <button class="print_btn" id="btn2">طباعة <i class="bi bi-printer"></i></button>
        </div>
      </div>
    </div>

  </section>



</main><!-- End #main -->



<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<script>
 

  // download all images
  function downloadAllImages() {
    const imageLinks = Array.from(document.querySelectorAll(".img a"));

    function downloadImage(index) {
      if (index >= imageLinks.length) {
        return;
      }
      const link = imageLinks[index];
      const imageUrl = link.getAttribute("href");
      const linkElement = document.createElement("a");
      linkElement.href = imageUrl;
      linkElement.setAttribute("download", "");
      linkElement.click();

      // Trigger the next download
      setTimeout(() => {
        downloadImage(index + 1);
      }, 100);
    }

    downloadImage(0);
  }



  // print single image with border and logo 
  function printImage(imageName) {
    var img = new Image();
    img.src = "uploads/" + imageName;

    // Replace the placeholder with the URL or path of your logo image
    var logoUrl = "assets/img/logo.jpg";

    var printWindow = window.open("", "_blank");
    printWindow.document.open();
    printWindow.document.write('<html><head>');
    printWindow.document.write('<title style="text-align:right;">محكمة إستئناف أسيوط </title>');
    printWindow.document.write('<style type="text/css">');
    printWindow.document.write('@media print { #aboutBlankContainer { display: none; } }');
    printWindow.document.write('.page { border: 2px solid black; padding: 10px; position: relative; height:90%; }');
    printWindow.document.write('.logo { position: absolute; top: 10px; left: 10px; max-width: 100%; z-index: 22 !important; }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body style="margin: 0; text-align: center;">');
    printWindow.document.write('<div class="page">');
    printWindow.document.write('<img src="' + logoUrl + '" style="width:60px;" class="logo" />');
    printWindow.document.write('<h2 style="text-align: right; margin-top:0px;">  محكمة إستئناف اسيوط</h2>');
    printWindow.document.write('<img src="' + img.src + '" style="max-width: 100%; margin-top:40px;" />');
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Delay printing to ensure the logo is loaded and displayed
    setTimeout(function() {
      printWindow.onafterprint = function() {
        printWindow.close();
      };

      window.addEventListener("beforeunload", function() {
        printWindow.close();
      });

      printWindow.print();
    }, 100);
  }
</script>

<?php
include 'shared/footer.php';
include 'shared/script.php';


?>