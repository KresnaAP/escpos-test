<?php
require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

try {
    // Membuat koneksi ke printer menggunakan CUPS
    $connector = new CupsPrintConnector("EPSON_TM_T82_S_C");

    // Membuat objek printer
    $printer = new Printer($connector);
    $printer->initialize();
    // Membuat objek gambar dari file di direktori public
    // $img = EscposImage::load('logo.png', false);

    // Mengirim data gambar ke printer
    // $printer->bitImage($img);

    // Teks 1 - rata kanan
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text("Jl.Cendana 7A HP.085332203122\n");

    // Teks 2 - border dan rata tengah
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
