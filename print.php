<?php
require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

try {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $connector = new WindowsPrintConnector("Brother_QL_800");
    } else {
        $connector = new CupsPrintConnector("Brother_QL_800");
    }

    $connector = new CupsPrintConnector("EPSON_TM_T82_S_C");

    $printer = new Printer($connector);
    $printer->initialize();

    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text("Jl.Cendana 7A HP.085332203122\n");

    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("Menyediakan Kebutuhan Rumah Tangga, Alat\nBangunan, Power Tool, Alat Listrik, Sanitary\n");

    $printer->feed();

    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Nomor Struk: ");
    $printer->text("10");
    $printer->text("         " . date('d') . "/" . date('m') . "/" . date('Y') . "\n");
    $printer->text(str_repeat("_", 48));

    $printer->feed();

    $printer->text(str_repeat("_", 48));

    $printer->feed();

    $data = array(
        array("TANDA TERIMA", 'Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada kesepakatan sebelumnya', "Total : Bayar : Sisa : ", "Rp.4.000 Rp.4.000 Rp.4.000"),
    );

    $printer->feed();

    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("Terima kasih telah memilih BMS Smart sebagai\n");
    $printer->text("Solusi Rumah dan Pekerjaan Anda\n");

    $printer->feed();

    // Memberikan perintah untuk memotong kertas
    $printer->cut();

    // Membuka koneksi ke printer
    $printer->close();

    return "Cetak berhasil";
} catch (Exception $e) {
    echo "Failed to print: " . $e->getMessage() . "\n";
}
