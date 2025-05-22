<?php
require_once 'config.php';
require_once 'functions/functions.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$documents = getDocumentsByDateRange($conn, $start_date, $end_date);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$sheet->setCellValue('A1', 'Kode Toko')
      ->setCellValue('B1', 'Nama Toko')
      ->setCellValue('C1', 'Wilayah')
      ->setCellValue('D1', 'Item Izin')
      ->setCellValue('E1', 'Pemohon')
      ->setCellValue('F1', 'Tanggal Input')
      ->setCellValue('G1', 'Status');

// Format Header
$sheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()->setRGB('007BFF');

// Data
$row = 2;
foreach ($documents as $doc) {
    $sheet->setCellValue("A$row", $doc['kode_toko'])
          ->setCellValue("B$row", $doc['nama_toko'])
          ->setCellValue("C$row", $doc['nama_wilayah'])
          ->setCellValue("D$row", $doc['nama_kategori'])
          ->setCellValue("E$row", $doc['pemohon'])
          ->setCellValue("F$row", $doc['tanggal_pengajuan'])
          ->setCellValue("G$row", $doc['status']);
    $row++;
}

// Alternating Row Colors
for ($i = 2; $i < $row; $i++) {
    $color = $i % 2 == 0 ? 'F2F2F2' : 'FFFFFF';
    $sheet->getStyle("A$i:G$i")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()->setRGB($color);
}

// Output
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Report_Dokumen.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>