<?php
require_once('tcpdf/tcpdf.php');

// Retrieve image data from the database
$images = [
    ['id' => 1, 'filename' => 'image1.jpg'],
    ['id' => 2, 'filename' => 'image2.jpg'],
    ['id' => 3, 'filename' => 'image3.jpg'],
    // additional image data
];

// Display the images with buttons
foreach ($images as $image) {
    echo '<img src="' . $image['filename'] . '" alt="' . $image['filename'] . '">';
    
    // Button to print the individual image
    echo '<button onclick="printImage(' . $image['id'] . ')">Print Image</button><br>';
}

// Button to print all images
echo '<button onclick="printAllImages()">Print All Images</button>';

// JavaScript code
?>
<script>
    // Function to print individual image as a separate PDF page
    function printImage(imageId) {
        // TODO: Implement the logic to generate a separate PDF with the imageId
        
        // Example using TCPDF library
        var pdf = new jsPDF();
        var imageData = getImageDataById(imageId);
        
        // Add image to the PDF document
        pdf.addImage(imageData, 'JPEG', 10, 10, 100, 100);
        
        // Save and open the PDF
        pdf.save('image_' + imageId + '.pdf');
    }
    
    // Function to print all images in a single PDF document
    function printAllImages() {
        // TODO: Implement the logic to generate a PDF with all images
        
        // Example using TCPDF library
        var pdf = new jsPDF();
        var xOffset = 10;
        var yOffset = 10;
        
        // Iterate through each image
        <?php foreach ($images as $image) { ?>
        var imageId = <?php echo $image['id']; ?>;
        var imageData = getImageDataById(imageId);
        
        // Add image to the PDF document
        pdf.addImage(imageData, 'JPEG', xOffset, yOffset, 100, 100);
        
        // Add a new page for the next image
        pdf.addPage();
        
        // Update the x and y offsets for the next image
        xOffset += 10;
        yOffset += 10;
        <?php } ?>
        
        // Save and open the PDF
        pdf.save('all_images.pdf');
    }
    
    function getImageDataById(imageId) {
        // TODO: Implement the logic to retrieve the image data by imageId
        
        // Example: Assuming the image data is stored in a JavaScript object
        var imageDatabase = {
            1: 'base64-encoded-image1-data',
            2: 'base64-encoded-image2-data',
            3: 'base64-encoded-image3-data',
            // additional image data
        };
        
        return imageDatabase[imageId];
    }
</script>