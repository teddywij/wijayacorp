<?php

//echo "Proses Cleaning Picklist Lama sedang berlangsung. Harap tunggu beberapa saat.\n Jangan CLOSE Tab ini!!";
$ind = 0;

$sql_pl = "SELECT * FROM picklist WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
$hasil_pl = pg_query($sql_pl) or die("Query failed: " . pg_last_error());
while ($baris_pl = pg_fetch_array($hasil_pl)) {
    $sql_pb = "SELECT * FROM picklist_bin WHERE picklist_id = '" . $baris_pl['picklist_id'] . "'";
    $hasil_pb = pg_query($sql_pb) or die("Query failed: " . pg_last_error());
    while ($baris_pb = pg_fetch_array($hasil_pb)) {
        $sqldel_pi = "DELETE FROM picklist_item WHERE picklist_bin_id = '" . $baris_pb['picklist_bin_id'] . "'";
        $hasildel_pi = pg_query($sqldel_pi) or die("Query failed: " . pg_last_error());

        $sqldel_pb = "DELETE FROM picklist_bin WHERE picklist_bin_id = '" . $baris_pb['picklist_bin_id'] . "'";
        $hasildel_pb = pg_query($sqldel_pb) or die("Query failed: " . pg_last_error());
    }

    $sqldel_pr = "DELETE FROM picklist_role WHERE picklist_id = '" . $baris_pl['picklist_id'] . "'";
    $hasildel_pr = pg_query($sqldel_pr) or die("Query failed: " . pg_last_error());

    $sqldel_pl = "DELETE FROM picklist WHERE picklist_id = '" . $baris_pl['picklist_id'] . "'";
    $hasildel_pl = pg_query($sqldel_pl) or die("Query failed: " . pg_last_error());
    if ($hasildel_pl) {
        $ind++;
    }
}

$sql_plr = "SELECT * FROM picklist_return WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
$hasil_plr = pg_query($sql_plr) or die("Query failed: " . pg_last_error());
while ($baris_plr = pg_fetch_array($hasil_plr)) {
    $sql_prb = "SELECT * FROM picklist_return_bin WHERE picklist_return_id = '" . $baris_plr['picklist_return_id'] . "'";
    $hasil_prb = pg_query($sql_prb) or die("Query failed: " . pg_last_error());
    while ($baris_prb = pg_fetch_array($hasil_prb)) {
        $sqldel_pi = "DELETE FROM picklist_item WHERE picklist_bin_id = '" . $baris_prb['picklist_bin_id'] . "'";
        $hasildel_pi = pg_query($sqldel_pi) or die("Query failed: " . pg_last_error());

        $sqldel_prb = "DELETE FROM picklist_return_bin WHERE picklist_bin_id = '" . $baris_prb['picklist_bin_id'] . "'";
        $hasildel_prb = pg_query($sqldel_prb) or die("Query failed: " . pg_last_error());
    }

    $sqldel_prr = "DELETE FROM picklist_return_role WHERE picklist_return_id = '" . $baris_plr['picklist_return_id'] . "'";
    $hasildel_prr = pg_query($sqldel_prr) or die("Query failed: " . pg_last_error());

    $sqldel_plr = "DELETE FROM picklist_return WHERE picklist_return_id = '" . $baris_plr['picklist_return_id'] . "'";
    $hasildel_plr = pg_query($sqldel_plr) or die("Query failed: " . pg_last_error());
    if ($hasildel_plr) {
        $ind++;
    }
}
if ($ind > 0) {
    echo "Picklist Lama Telah Berhasil Dihapus";
} else {
    echo "Tidak ada data yang dihapus";
}
pg_close($conn);
