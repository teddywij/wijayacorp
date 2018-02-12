<?php

//echo "Proses Cleaning Return Lama sedang berlangsung. Harap tunggu beberapa saat.\n Jangan CLOSE Tab ini!!";
if (isset($return_id)) {
    $sql_rh = "SELECT * FROM return_header WHERE return_id = '" . $return_id . "'";
} else {
    $inre = 0;
    $sql_rh = "SELECT * FROM return_header WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
}

//$sql_rh = "SELECT * FROM return_header WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
$hasil_rh = pg_query($sql_rh) or die("Query failed: " . pg_last_error());
while ($baris_rh = pg_fetch_array($hasil_rh)) {
    $sqldel_rish = "DELETE FROM return_item_shipment WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_rish = pg_query($sqldel_rish) or die("Query failed: " . pg_last_error());

    $sqldel_rib = "DELETE FROM return_item_billing WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_rib = pg_query($sqldel_rib) or die("Query failed: " . pg_last_error());

    $sql_shr = "SELECT * FROM shipment_receipt WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasil_shr = pg_query($sql_shr) or die("Query failed: " . pg_last_error());
    while ($baris_shr = pg_fetch_array($hasil_shr)) {
        $sqlup_iid = "UPDATE inventory_item_detail SET return_id = NULL, return_item_seq_id = NULL, receipt_id = NULL "
                . "WHERE receipt_id = '" . $baris_shr['receipt_id'] . "'";
        $hasilup_iid = pg_query($sqlup_iid) or die("Query failed: " . pg_last_error());

        $sqldel_shr = "DELETE FROM shipment_receipt WHERE receipt_id = '" . $baris_shr['receipt_id'] . "'";
        $hasildel_shr = pg_query($sqldel_shr) or die("Query failed: " . pg_last_error());
    }

    $sqldel_pri = "DELETE FROM picklist_return_item WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_pri = pg_query($sqldel_pri) or die("Query failed: " . pg_last_error());

    $sqldel_risi = "DELETE FROM return_item_shipment_inventory WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_risi = pg_query($sqldel_risi) or die("Query failed: " . pg_last_error());

    $sqldel_ri = "DELETE FROM return_item WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_ri = pg_query($sqldel_ri) or die("Query failed: " . pg_last_error());

    $sqldel_rs = "DELETE FROM return_status WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_rs = pg_query($sqldel_rs) or die("Query failed: " . pg_last_error());

    $sqldel_risinv = "DELETE FROM return_item_shipment_inventory WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_risinv = pg_query($sqldel_risinv) or die("Query failed: " . pg_last_error());

    $sqldel_riwosinv = "DELETE FROM return_item_w_o_shipment_inventory WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_riwosinv = pg_query($sqldel_riwosinv) or die("Query failed: " . pg_last_error());

    $sqldel_riwoir = "DELETE FROM return_item_without_order_inv_res WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_riwoir = pg_query($sqldel_riwoir) or die("Query failed: " . pg_last_error());

    $sqldel_riwos = "DELETE FROM return_item_without_order_shipment WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_riwos = pg_query($sqldel_riwos) or die("Query failed: " . pg_last_error());

    $sqldel_riwob = "DELETE FROM return_item_without_order_billing WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_riwob = pg_query($sqldel_riwob) or die("Query failed: " . pg_last_error());

    $sqldel_riwo = "DELETE FROM return_item_without_order WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_riwo = pg_query($sqldel_riwo) or die("Query failed: " . pg_last_error());

    $sqldel_ra = "DELETE FROM return_adjustment WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_ra = pg_query($sqldel_ra) or die("Query failed: " . pg_last_error());

    $sql_sh = "SELECT * FROM shipment WHERE primary_return_id = '" . $baris_rh['return_id'] . "'";
    $hasil_sh = pg_query($sql_sh) or die("Query failed: " . pg_last_error());
    while ($baris_sh = pg_fetch_array($hasil_sh)) {
        if (!empty($baris_sh['shipment_id'])) {
            $shipment_id = $baris_sh['shipment_id'];
            include 'delete_shipment_lama.php';
        }
    }

    //JL 2017-03-27
    $sqlup_iid3 = "UPDATE inventory_item_detail SET return_id = NULL, return_item_seq_id = NULL, receipt_id = NULL, shipment_id = NULL "
            . "WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasilup_iid3 = pg_query($sqlup_iid3) or die("Query failed: " . pg_last_error());
    //JL 2017-03-27

    $sqldel_rh = "DELETE FROM return_header WHERE return_id = '" . $baris_rh['return_id'] . "'";
    $hasildel_rh = pg_query($sqldel_rh) or die("Query failed: " . pg_last_error());
    if (!isset($return_id)) {
        if ($hasildel_rh) {
            $inre++;
        }
    }
}
if (!isset($return_id)) {
    if ($inre > 0) {
        echo "Return Lama Telah Berhasil Dihapus";
    } else {
        echo "Tidak ada data yang dihapus";
    }
    pg_close($conn);
}
