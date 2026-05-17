<?php

require '../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$html = '
    <h1>Hello PDF</h1>
    <p>Dompdf is working correctly.</p>
';

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream('test.pdf');

?>