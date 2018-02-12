<?php

//echo "Proses Cleaning Fin Misc Account sedang berlangsung. Harap tunggu beberapa saat.\n Jangan CLOSE Tab ini!!";
$ind = 0;

//JL 2017-04-05 Rekap Saldo Awal Otomatis permintaan Mami Luci
date_default_timezone_set("Asia/Jakarta");
$date = date("Y-m-d H:i:s");
$sql_rsa = "SELECT SUM(debet), SUM(credit), fin_account_id FROM fin_account_misc WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate') AND ((cancel = 'N' AND approved = 'Y') OR (cancel = 'Y' AND approved = 'N')) "
        . "GROUP BY fin_account_id";
$hasil_rsa = pg_query($sql_rsa) or die("Query failed: " . pg_last_error());
while ($baris_rsa = pg_fetch_row($hasil_rsa)) {
    if ($baris_rsa[0] > 0 || $baris_rsa[1] > 0) {
        $debet = $baris_rsa[0] - $baris_rsa[1];
        $sql_fami = "SELECT MAX(fin_account_misc_id) FROM fin_account_misc";
        $hasil_fami = pg_query($sql_fami) or die("Query failed: " . pg_last_error());
        while ($baris_fami = pg_fetch_row($hasil_fami)) {
            $newfami = $baris_fami[0] + 1;
        }
        $sql_irsa = "INSERT INTO fin_account_misc (fin_account_id, fin_account_misc_id, fin_account_misc_type_id, debet, credit, cancel, generated, last_updated_stamp, last_updated_tx_stamp, "
                . "created_stamp, created_tx_stamp, effective_date, adjusted_date, approved) VALUES ('" . $baris_rsa[2] . "', '" . $newfami . "', '10000', '" . $debet . "', '0', 'N', 'N', "
                . "'" . $date . "', '" . $date . "', '" . $date . "', '" . $date . "', '" . $thrudate . "', '" . $thrudate . "', 'Y')";
        $hasil_irsa = pg_query($sql_irsa) or die("Query failed: " . pg_last_error());
    }
}
//JL 2017-04-05

$sql_fam = "SELECT * FROM fin_account_misc WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
$hasil_fam = pg_query($sql_fam) or die("Query failed: " . pg_last_error());
while ($baris_fam = pg_fetch_array($hasil_fam)) {
    $sqldel_famea = "DELETE FROM fin_account_misc_edit_approval WHERE new_fin_account_misc_id = '" . $baris_fam['fin_account_misc_id'] . "'";
    $hasildel_famea = pg_query($sqldel_famea) or die("Query failed: " . pg_last_error());

    $sqldel_fameao = "DELETE FROM fin_account_misc_edit_approval WHERE old_fin_account_misc_id = '" . $baris_fam['fin_account_misc_id'] . "'";
    $hasildel_fameao = pg_query($sqldel_fameao) or die("Query failed: " . pg_last_error());

    $sqldel_famda = "DELETE FROM fin_account_misc_delete_approval WHERE fin_account_misc_id = '" . $baris_fam['fin_account_misc_id'] . "'";
    $hasildel_famda = pg_query($sqldel_famda) or die("Query failed: " . pg_last_error());

    $sqldel_faml = "DELETE FROM fin_account_misc_log WHERE fin_account_misc_id = '" . $baris_fam['fin_account_misc_id'] . "'";
    $hasildel_faml = pg_query($sqldel_faml) or die("Query failed: " . pg_last_error());

    $sqldel_fam = "DELETE FROM fin_account_misc WHERE fin_account_misc_id = '" . $baris_fam['fin_account_misc_id'] . "'";
    $hasildel_fam = pg_query($sqldel_fam) or die("Query failed: " . pg_last_error());
    if ($hasildel_fam) {
        $ind++;
    }
}
if ($ind > 0) {
    echo "Fin Misc Account Lama Telah Berhasil Dihapus";
} else {
    echo "Tidak ada data yang dihapus";
}
pg_close($conn);
