<!-- <?php
        $str = "Apples and bananas.";
        $pattern = "/ba(na){2}/i";
        echo preg_match($pattern, $str); // Outputs 1
        ?> -->

<?php
// function familyname ($fname, $year) {
//     echo "family $fname was born in $year";
// }
// familyname("versiferdi","1900");

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml('hello world');

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();
?>

