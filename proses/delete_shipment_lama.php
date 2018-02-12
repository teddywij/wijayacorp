<?php

if (isset($shipment_id)) {
    $sql_ship = "SELECT * FROM shipment WHERE shipment_id = '" . $shipment_id . "'";
} else {
    $inship = 0;
    $sql_ship = "SELECT * FROM shipment WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
}
$hasil_ship = pg_query($sql_ship) or die("Query failed: " . pg_last_error());
while ($baris_ship = pg_fetch_array($hasil_ship)) {
    $delship = "true";
    $sql_iis = "SELECT * FROM item_issuance WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
    $hasil_iis = pg_query($sql_iis) or die("Query failed: " . pg_last_error());
    while ($baris_iis = pg_fetch_array($hasil_iis)) {
        $sql_oib = "SELECT * FROM order_item_billing WHERE item_issuance_id = '" . $baris_iis['item_issuance_id'] . "'";
        $hasil_oib = pg_query($sql_oib) or die("Query failed: " . pg_last_error());
        while ($baris_oib = pg_fetch_array($hasil_oib)) {
            $sql_inv = "SELECT * FROM invoice WHERE invoice_id = '" . $baris_oib['invoice_id'] . "'";
            $hasil_inv = pg_query($sql_inv) or die("Query failed: " . pg_last_error());
            while ($baris_inv = pg_fetch_array($hasil_inv)) {
                if (strtotime($thrudate) < strtotime($baris_inv['created_stamp'])) {
                    $delship = "false";
                }
                if (strtotime($fromdate) > strtotime($baris_inv['created_stamp'])) {
                    $delship = "false";
                }
            }
        }
    }

    if ($delship == "true") {
        $sql_at = "SELECT * FROM acctg_trans WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasil_at = pg_query($sql_at) or die("Query failed: " . pg_last_error());
        while ($baris_at = pg_fetch_array($hasil_at)) {
            $sql_ate = "SELECT * FROM acctg_trans_entry WHERE acctg_trans_id = '" . $baris_at['acctg_trans_id'] . "'";
            $hasil_ate = pg_query($sql_ate) or die("Query failed: " . pg_last_error());
            while ($baris_ate = pg_fetch_array($hasil_ate)) {
                $sqldel_gre = "DELETE FROM gl_reconciliation_entry WHERE acctg_trans_id = '" . $baris_ate['acctg_trans_id'] . "' "
                        . "AND acctg_trans_entry_seq_id = '" . $baris_ate['acctg_trans_entry_seq_id'] . "'";
                $hasildel_gre = pg_query($sqldel_gre) or die("Query failed: " . pg_last_error());

                $sqldel_ate = "DELETE FROM acctg_trans_entry WHERE acctg_trans_id = '" . $baris_ate['acctg_trans_id'] . "' "
                        . "AND acctg_trans_entry_seq_id = '" . $baris_ate['acctg_trans_entry_seq_id'] . "'";
                $hasildel_ate = pg_query($sqldel_ate) or die("Query failed: " . pg_last_error());
            }

            $sqldel_at = "DELETE FROM acctg_trans WHERE acctg_trans_id = '" . $baris_at['acctg_trans_id'] . "'";
            $hasildel_at = pg_query($sqldel_at) or die("Query failed: " . pg_last_error());
        }

        $sql_iis = "SELECT * FROM item_issuance WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasil_iis = pg_query($sql_iis) or die("Query failed: " . pg_last_error());
        while ($baris_iis = pg_fetch_array($hasil_iis)) {
            $sqlup_iid = "UPDATE inventory_item_detail SET order_id = NULL, order_item_seq_id = NULL, return_id = NULL, return_item_seq_id = NULL, "
                    . "item_issuance_id = NULL WHERE item_issuance_id = '" . $baris_iis['item_issuance_id'] . "'";
            $hasilup_iid = pg_query($sqlup_iid) or die("Query failed: " . pg_last_error());

            $sqldel_iir = "DELETE FROM item_issuance_role WHERE item_issuance_id = '" . $baris_iis['item_issuance_id'] . "'";
            $hasildel_iir = pg_query($sqldel_iir) or die("Query failed: " . pg_last_error());

            $sqldel_oib = "DELETE FROM order_item_billing WHERE item_issuance_id = '" . $baris_iis['item_issuance_id'] . "'";
            $hasildel_oib = pg_query($sqldel_oib) or die("Query failed: " . pg_last_error());

            $sqldel_iis = "DELETE FROM item_issuance WHERE item_issuance_id = '" . $baris_iis['item_issuance_id'] . "'";
            $hasildel_iis = pg_query($sqldel_iis) or die("Query failed: " . pg_last_error());
        }

        $sqldel_sprs = "DELETE FROM shipment_package_route_seg WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_sprs = pg_query($sqldel_sprs) or die("Query failed: " . pg_last_error());

        $sqldel_srs = "DELETE FROM shipment_route_segment WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_srs = pg_query($sqldel_srs) or die("Query failed: " . pg_last_error());

        $sqldel_sib = "DELETE FROM shipment_item_billing WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_sib = pg_query($sqldel_sib) or die("Query failed: " . pg_last_error());

        $sqldel_spc = "DELETE FROM shipment_package_content WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_spc = pg_query($sqldel_spc) or die("Query failed: " . pg_last_error());

        $sqldel_riwos = "DELETE FROM return_item_without_order_shipment WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_riwos = pg_query($sqldel_riwos) or die("Query failed: " . pg_last_error());

        $sqldel_riwosi = "DELETE FROM return_item_w_o_shipment_inventory WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_riwosi = pg_query($sqldel_riwosi) or die("Query failed: " . pg_last_error());

        $sqldel_ris = "DELETE FROM return_item_shipment WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_ris = pg_query($sqldel_ris) or die("Query failed: " . pg_last_error());

        $sqldel_si = "DELETE FROM shipment_item WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_si = pg_query($sqldel_si) or die("Query failed: " . pg_last_error());

        $sqldel_ss = "DELETE FROM shipment_status WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_ss = pg_query($sqldel_ss) or die("Query failed: " . pg_last_error());

        $sqldel_os = "DELETE FROM order_shipment WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_os = pg_query($sqldel_os) or die("Query failed: " . pg_last_error());

        $sqldel_pp = "DELETE FROM picklist_packed WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_pp = pg_query($sqldel_pp) or die("Query failed: " . pg_last_error());

        $sqldel_sp = "DELETE FROM shipment_package WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_sp = pg_query($sqldel_sp) or die("Query failed: " . pg_last_error());

        $sqldel_risinv = "DELETE FROM return_item_shipment_inventory WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_risinv = pg_query($sqldel_risinv) or die("Query failed: " . pg_last_error());

        $sqldel_ship = "DELETE FROM shipment WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
        $hasildel_ship = pg_query($sqldel_ship) or die("Query failed: " . pg_last_error());

        if (!isset($shipment_id)) {
            if ($hasildel_ship) {
                $inship++;
            }
        }
    }
}
if (!isset($shipment_id)) {
    if ($inship > 0) {
        echo "Shipment Lama Telah Berhasil Dihapus";
    } else {
        echo "Tidak ada data yang dihapus";
    }
    pg_close($conn);
}