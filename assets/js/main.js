(function () {
  "use strict";

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll(".needs-validation");

  // Loop over them and prevent submission
  Array.from(forms).forEach((form) => {
    form.addEventListener(
      "submit",
      (event) => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }

        form.classList.add("was-validated");
      },
      false
    );
  });

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim();
    if (all) {
      return [...document.querySelectorAll(el)];
    } else {
      return document.querySelector(el);
    }
  };

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    if (all) {
      select(el, all).forEach((e) => e.addEventListener(type, listener));
    } else {
      select(el, all).addEventListener(type, listener);
    }
  };

  /**
   * Easy on scroll event listener
   */
  const onscroll = (el, listener) => {
    el.addEventListener("scroll", listener);
  };

  /**
   * Sidebar toggle
   */
  if (select(".toggle-sidebar-btn")) {
    on("click", ".toggle-sidebar-btn", function (e) {
      select("body").classList.toggle("toggle-sidebar");
    });
  }

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select("#header");
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add("header-scrolled");
      } else {
        selectHeader.classList.remove("header-scrolled");
      }
    };
    window.addEventListener("load", headerScrolled);
    onscroll(document, headerScrolled);
  }

  /**
   * Back to top button
   */
  let backtotop = select(".back-to-top");
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add("active");
      } else {
        backtotop.classList.remove("active");
      }
    };
    window.addEventListener("load", toggleBacktotop);
    onscroll(document, toggleBacktotop);
  }

  /**
   * Initiate Datatables
   */
  const datatables = select(".datatable", true);
  datatables.forEach((datatable) => {
    new simpleDatatables.DataTable(datatable);
  });

  // loading-------
  window.addEventListener("load", function () {
    // loading page
    $(window).ready(function () {
      $(".loader").fadeOut(2200, function () {
        $("body").css("overflow", "auto");
        $(".loading-spiner").fadeOut(1500);
      });
    });
  });
  // ------------ //

  // image scale--------
  const lightbox = GLightbox({
    selector: ".glightbox",
    touchNavigation: true,
    onOpen: () => {
      const newBtn = document.createElement("button");
      newBtn.id = "newBtn";
      newBtn.innerText = "New Button";
      lightbox.modal.appendChild(newBtn);
    },
  });
  // ---------- //

  // print all files
  // document.getElementById("btn1").addEventListener("click", function () {
  //   const printWindow = window.open("", "_blank");
  //   const images = document.querySelectorAll(".img img");
  //   const headerText = "صور الملفات"; // Header text for the first page

  //   let imagesLoaded = 0;

  //   const onLoadImage = function () {
  //     imagesLoaded++;

  //     if (imagesLoaded === images.length) {
  //       images.forEach(function (image, index) {
  //         const imageContainer = image.closest(".img");
  //         const imageSrc = image.getAttribute("src");
  //         const imageFileName =
  //           imageContainer.nextElementSibling.querySelector("span").textContent;

  //         const page = printWindow.document.createElement("div");
  //         page.style.border = "2px solid black";
  //         page.classList.add("page");
  //         page.style.pageBreakAfter = "always"; // Add page break after each page

  //         const imageElement = printWindow.document.createElement("img");
  //         imageElement.src = imageSrc;
  //         imageElement.style.width = "100%"; // Modify the width here
  //         imageElement.style.margin = "100px"; // Center alignment
  //         imageElement.style.padding = "70px 0";
  //         page.appendChild(imageElement);

  //         //file name-------------
  //         // const fileNameHeading = printWindow.document.createElement("h2");
  //         // fileNameHeading.textContent = imageFileName;
  //         // page.appendChild(fileNameHeading);

  //         if (index === 0) {
  //           const header = printWindow.document.createElement("h2");
  //           header.textContent = headerText;
  //           header.style.textAlign = "center"; // Center alignment for the header
  //           header.style.marginBottom = "20px"; // Add some margin at the bottom

  //           page.insertBefore(header, imageElement); // Insert the header before the image
  //         }

  //         printWindow.document.body.appendChild(page);
  //       });

  //       printWindow.document.title = "محكمة إستئناف أسيوط"; // Update document title

  //       // Remove server details from print preview
  //       const styleElement = printWindow.document.createElement("style");
  //       styleElement.textContent = `
  //           #btn1, .details, [data-reactroot], @page {
  //             display: none !important;
  //           }
  //           .footer {
  //             display: none !important;
  //           }
  //         `;
  //       printWindow.document.head.appendChild(styleElement);

  //       printWindow.document.body.style.width = "210mm"; // Set paper width to A4 size
  //       printWindow.document.body.style.margin = "0";
  //       printWindow.document.body.style.padding = "20mm"; // Add padding for better presentation

  //       printWindow.onafterprint = function () {
  //         printWindow.close(); // Close the print window after printing
  //       };

  //       printWindow.print();
  //     }
  //   };

  //   images.forEach(function (image) {
  //     if (image.complete) {
  //       onLoadImage();
  //     } else {
  //       image.addEventListener("load", onLoadImage);
  //     }
  //   });
  // });
  // ------------- //
  document.getElementById("btn1").addEventListener("click", function () {
    const printWindow = window.open("", "_blank");
    const images = document.querySelectorAll(".img img");
    const headerText = "صور الملفات"; // Header text for the first page
    const logoSrc = "assets/img/logo.jpg"; // Replace with the actual path to your logo image

    let imagesLoaded = 0;

    const onLoadImage = function () {
      imagesLoaded++;

      if (imagesLoaded === images.length) {
        const styleElement = printWindow.document.createElement("style");
        styleElement.textContent = `
            @page {
              size: A4;
              margin: 0;
            }
           
            .page {
              position: relative;
              page-break-after: always;
              border: 2px solid black;
              padding: 100px;
              margin:2px 0;
              height: 99%;
              width: 100%;
              box-sizing: border-box;
              position:relative;
            }
            img {
              display: block;
              max-width: 100%;
              margin: 30px auto;
            }
            .logo {
              position: absolute;
              top: 0px;
              left: 20px;
              width: 70px;
              z-index:2222;
            }
            .title {
              position: absolute;
              top: 20px;
              right: 20px;
            }
          `;
        printWindow.document.head.appendChild(styleElement);

        images.forEach(function (image, index) {
          const imageContainer = image.closest(".img");
          const imageSrc = image.getAttribute("src");
          const imageFileName =
            imageContainer.nextElementSibling.querySelector("span").textContent;

          const page = printWindow.document.createElement("div");
          page.classList.add("page");

          const imageElement = printWindow.document.createElement("img");
          imageElement.src = imageSrc;
          page.appendChild(imageElement);

          // Logo--------------
          const logoElement = printWindow.document.createElement("img");
          logoElement.src = logoSrc;
          logoElement.classList.add("logo");
          page.appendChild(logoElement);

          // Title--------------
          const titleElement = printWindow.document.createElement("h2");
          titleElement.textContent = "محكمة إستئناف أسيوط";
          titleElement.classList.add("title");
          page.appendChild(titleElement);

          if (index === 0) {
            const header = printWindow.document.createElement("h2");
            header.textContent = headerText;
            header.style.textAlign = "center"; // Center alignment for the header
            header.style.marginBottom = "20px"; // Add some margin at the bottom

            page.insertBefore(header, imageElement); // Insert the header before the image
          }

          printWindow.document.body.appendChild(page);
        });

        printWindow.document.title = "محكمة إستئناف أسيوط"; // Update document title

        setTimeout(function () {
          printWindow.onafterprint = function () {
            printWindow.close();
          };

          window.addEventListener("beforeunload", function () {
            printWindow.close();
          });

          printWindow.print();
        }, 100);
      }
    };

    images.forEach(function (image) {
      if (image.complete) {
        onLoadImage();
      } else {
        image.addEventListener("load", onLoadImage);
      }
    });
  });

  //  print the right side page
  function printDiv() {
    var printContents = document.getElementById("pageContainer").innerHTML;
    var originalContents = document.body.innerHTML;

    // Replace the placeholder with the URL or path of your logo image
    var logoUrl = "assets/img/logo.jpg";

    var printWindow = window.open("", "_blank");
    printWindow.document.open();
    printWindow.document.write("<html><head>");
    printWindow.document.write(
      '<title style="text-align:right;">محكمة إستئناف أسيوط </title>'
    );
    printWindow.document.write('<style type="text/css">');
    printWindow.document.write(
      "@media print { #aboutBlankContainer { display: none; } }"
    );
    printWindow.document.write(
      ".page { border: 3px solid black; padding: 40px 40px; position: relative; height:96%;direction:rtl;margin:15px; }"
    );
    printWindow.document.write(
      ".logo { position: absolute; top: 20px; left: 20px; max-width: 100%; z-index: 22 !important; }"
    );
    printWindow.document.write(
      ".container-print > div:not(.pagetitle) { text-align: right; }"
    );
    printWindow.document.write(
      "#div2 {background:white !important; box-shadow:0 0 0 white; margin-top:20px;}"
    );
    printWindow.document.write(
      ".col-md-6 { font-size:17px;  padding:10 15px;}"
    );
    printWindow.document.write("</style>");
    printWindow.document.write(
      '<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">'
    );
    printWindow.document.write(
      '<link href="assets/css/style.css" rel="stylesheet">'
    );
    printWindow.document.write(
      '</head><body style="margin: 0; text-align: center;">'
    );
    printWindow.document.write('<div class="page">');
    printWindow.document.write(
      '<img src="' + logoUrl + '" style="width:60px;" class="logo" />'
    );
    printWindow.document.write(
      '<h2 style="text-align: right; margin-top:0px;">  محكمة إستئناف أسيوط</h2>'
    );
    printWindow.document.write("<div>" + printContents + "</div>");
    printWindow.document.write("</div>");
    printWindow.document.write("</body></html>");
    printWindow.document.close();

    // Delay printing to ensure the logo is loaded and displayed
    setTimeout(function () {
      printWindow.onafterprint = function () {
        printWindow.close();
      };

      window.addEventListener("beforeunload", function () {
        printWindow.close();
      });

      printWindow.print();
    }, 100);
  }
  document.getElementById("btn2").addEventListener("click", printDiv);
  // ------------- //
})();

// image print
