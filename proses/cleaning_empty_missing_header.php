<?php

echo "Proses Cleaning Empty Missing Header Lama sedang berlangsung. Harap tunggu beberapa saat.\n Jangan CLOSE Tab ini!!";
$ind = 0;

$sql_mh = "SELECT * FROM missing_header";
$hasil_mh = pg_query($sql_mh) or die("Query failed: " . pg_last_error());
while ($baris_mh = pg_fetch_array($hasil_mh)) {
    $sql_oibo = "SELECT COUNT(*) FROM order_item_back_order WHERE missing_header_id = '" . $baris_mh['missing_header_id'] . "'";
    $hasil_oibo = pg_query($sql_oibo)or die("Query failed: " . pg_last_error());

    $sql_pcbo = "SELECT COUNT(*) FROM promo_code_back_order WHERE missing_header_id = '" . $baris_mh['missing_header_id'] . "'";
    $hasil_pcbo = pg_query($sql_pcbo)or die("Query failed: " . pg_last_error());

    if (empty($hasil_oibo) && empty($hasil_pcbo)) {
        $sqldel_mh = "DELETE FROM missing_header WHERE missing_header_id = '" . $baris_mh['missing_header_id'] . "'";
        $hasildel_mh = pg_query($sqldel_mh)or die("Query failed: " . pg_last_error());
        if ($hasildel_mh) {
            $ind++;
        }
    }
}
if ($ind > 0) {
    echo "Missing Header Telah Berhasil Dihapus";
} else {
    echo "Tidak ada data yang dihapus";
}
pg_close($conn);
