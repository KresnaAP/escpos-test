<?php
require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

try {
    $nomor_struk = $nomor_struk == null ? $this->nomor_struk() : $nomor_struk;

    // Membuat koneksi ke printer menggunakan CUPS
    $connector = new CupsPrintConnector("EPSON_TM_T82_S_C");

    // Membuat objek printer
    $printer = new Printer($connector);

    // Membuat objek gambar dari file di direktori public
    $img = EscposImage::load('logo.png', false);

    // Mengirim data gambar ke printer
    $printer->bitImage($img);

    // Teks 1 - rata kanan
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text("Jl.Cendana 7A HP.085332203122\n");

    // Teks 2 - border dan rata tengah
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text("Menyediakan Kebutuhan Rumah Tangga, Alat\nBangunan, Power Tool, Alat Listrik, Sanitary\n");

    $printer->feed();

    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Nomor Struk: ");
    $printer->text($nomor_struk);
    $printer->text("         " . date('d') . "/" . date('m') . "/" . date('Y') . "\n");
    $printer->text(str_repeat("_", 48));

    // Menentukan data pada tabel
    for ($i = 0; $i < count($request->barang_id); $i++) {
        $barang = $this->barangService->getAllBarangByKodeBarang($request->barang_id[$i]);

        $data[$i] = [$barang->nama, $request->jumlah[$i], "Rp." . $request->harga[$i], $request->diskon[$i] . "%", "Rp." . $request->total_harga[$i]];
    }

    // Menentukan lebar kolom dan jumlah kolom pada tabel
    $col1_width = 14;
    $col2_width = 5;
    $col3_width = 12;
    $col4_width = 5;
    $col5_width = 12;

    // Print header row
    $header_text = str_pad('NAMA BARANG', $col1_width, ' ', STR_PAD_RIGHT);
    $header_text .= str_pad('JML', $col2_width, ' ', STR_PAD_RIGHT);
    $header_text .= str_pad('HARGA', $col3_width, ' ', STR_PAD_RIGHT);
    $header_text .= str_pad('DIS', $col4_width, ' ', STR_PAD_RIGHT);
    $header_text .= str_pad('TOTAL', $col5_width, ' ', STR_PAD_RIGHT);
    $printer->text($header_text . "\n");
    $printer->text(str_repeat("_", 48));

    // Mengirim data pada tabel ke printer
    foreach ($data as $row) {
        $lines1 = str_split($row[0], $col1_width);
        $lines2 = str_split($row[1], $col2_width);
        $lines3 = str_split($row[2], $col3_width);
        $lines4 = str_split($row[3], $col4_width);
        $lines5 = str_split($row[4], $col5_width);
        $key1 = 0;
        foreach ($lines1 as $key => $line) {
            $col1_text[$key] = str_pad($line, $col1_width, ' ', STR_PAD_RIGHT);
            $key1 += 1;
        }
        foreach ($lines2 as $key => $line) {
            $col2_text[$key] = str_pad($line, $col2_width, ' ', STR_PAD_RIGHT);
        }
        for ($i = $key + 1; $i < $key1; $i++) {
            $col2_text[$i] = str_pad(' ', $col2_width, ' ', STR_PAD_RIGHT);
        }
        foreach ($lines3 as $key => $line) {
            $col3_text[$key] = str_pad($line, $col3_width, ' ', STR_PAD_RIGHT);
        }
        for ($i = $key + 1; $i < $key1; $i++) {
            $col3_text[$i] = str_pad(' ', $col3_width, ' ', STR_PAD_RIGHT);
        }
        foreach ($lines4 as $key => $line) {
            $col4_text[$key] = str_pad($line, $col4_width, ' ', STR_PAD_RIGHT);
        }
        for ($i = $key + 1; $i < $key1; $i++) {
            $col4_text[$i] = str_pad(' ', $col4_width, ' ', STR_PAD_RIGHT);
        }
        foreach ($lines5 as $key => $line) {
            $col5_text[$key] = str_pad($line, $col5_width, ' ', STR_PAD_RIGHT);
        }
        for ($i = $key + 1; $i < $key1; $i++) {
            $col5_text[$i] = str_pad(' ', $col5_width, ' ', STR_PAD_RIGHT);
        }
        for ($i = 0; $i < $key1; $i++) {
            $printer->text($col1_text[$i] . $col2_text[$i] . $col3_text[$i] . $col4_text[$i] . $col5_text[$i] . "\n");
        }
    }

    $printer->feed();

    $printer->text(str_repeat("_", 48));

    $printer->feed();

    $data = array(
        array("TANDA TERIMA", 'Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada kesepakatan sebelumnya', "Total : Bayar : Sisa : ", "Rp.4.000 Rp.4.000 Rp.4.000"),
    );

    $printer->text("TANDA TERIMA                Total : Rp." . $request->grand_total . "\n");
    $printer->text("___________________________ Bayar : Rp." . $request->total_bayar . "\n");
    $printer->text("|Barang yang sudah dibeli  |Sisa  : Rp." . number_format($request->kembalian, 2, '.', '') . "\n");
    $printer->text("|dapat dikembalikan kecuali|\n");
    $printer->text("|ada kesepakatan sebelumnya|\n");
    $printer->text("----------------------------\n");


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
