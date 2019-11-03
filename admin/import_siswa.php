<?php
require "../config/config.default.php";
require "../vendor/autoload.php";
$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {

    $arr_file = explode('.', $_FILES['file']['name']);
    $extension = end($arr_file);

    if ('csv' == $extension) {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
    } else {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    }
    if ($extension <> 'xlsx') {
        echo "Harap Pilih file bertipe Xlsx";
    } else {
        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);

        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        $sukses = $gagal = 0;
        $exec = mysqli_query($koneksi, "TRUNCATE siswa");
        for ($i = 1; $i < count($sheetData); $i++) {
            $id_siswa = $sheetData[$i]['0'];
            $nis = $sheetData[$i]['1'];
            $no_peserta = $sheetData[$i]['2'];
            $nama = $sheetData[$i]['3'];
            $level = $sheetData[$i]['4'];
            $kelas = $sheetData[$i]['5'];
            $pk = $sheetData[$i]['6'];
            if ($pk == "") {
                $pk = "semua";
            }
            $sesi = $sheetData[$i]['7'];
            $ruang = $sheetData[$i]['8'];
            $username = $sheetData[$i]['9'];
            $password = $sheetData[$i]['10'];
            $foto = $sheetData[$i]['11'];
            $qkelas = mysqli_query($koneksi, "SELECT id_kelas FROM kelas WHERE id_kelas='$kelas'");
            $cekkelas = mysqli_num_rows($qkelas);
            if (!$cekkelas <> 0) {
                $exec = mysqli_query($koneksi, "INSERT INTO kelas (id_kelas,level,nama)VALUES('$kelas','$level','$kelas')");
            }
            if ($setting['jenjang'] == 'SMK') {
                $qpk = mysqli_query($koneksi, "SELECT id_pk FROM pk WHERE id_pk='$pk'");
                $cekpk = mysqli_num_rows($qpk);
                if (!$cekpk <> 0) {
                    $exec = mysqli_query($koneksi, "INSERT INTO pk (id_pk,program_keahlian)VALUES('$pk','$pk')");
                }
            }
            $qlevel = mysqli_query($koneksi, "SELECT kode_level FROM level WHERE kode_level='$level'");
            $ceklevel = mysqli_num_rows($qlevel);
            if (!$ceklevel <> 0) {
                $exec = mysqli_query($koneksi, "INSERT INTO level (kode_level,keterangan)VALUES('$level','$level')");
            }
            $qruang = mysqli_query($koneksi, "SELECT kode_ruang FROM ruang WHERE kode_ruang='$ruang'");
            $cekruang = mysqli_num_rows($qruang);
            if (!$cekruang <> 0) {
                $exec = mysqli_query($koneksi, "INSERT INTO ruang (kode_ruang,keterangan)VALUES('$ruang','$ruang')");
            }
            $qsesi = mysqli_query($koneksi, "SELECT kode_sesi FROM sesi WHERE kode_sesi='$sesi'");
            $ceksesi = mysqli_num_rows($qsesi);
            if (!$ceksesi <> 0) {
                $exec = mysqli_query($koneksi, "INSERT INTO sesi (kode_sesi,nama_sesi)VALUES('$sesi','$sesi')");
            }
            if ($nama <> '') {
                $exec = mysqli_query($koneksi, "INSERT INTO siswa (id_siswa,id_kelas,idpk,nis,no_peserta,nama,level,sesi,ruang,username,password,foto) VALUES ('$id_siswa','$kelas','$pk','$nis','$no_peserta','$nama','$level','$sesi','$ruang','$username','$password','$foto')");
                ($exec) ? $sukses++ : $gagal++;
            }
        }
        echo "Berhasil: $sukses | Gagal: $gagal ";
    }
}
