<?php

//set_time_limit(0);
//include '../config/config.php';

/*$fromdate = $_POST['fromdate'];
$thrudate = $_POST['thrudate'];
$action = $_POST['action'];*/

$ind = 0;
$sql_acctg = "SELECT * FROM acctg_trans WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
$hasil_acctg = pg_query($sql_acctg) or die("Query failed: " . pg_last_error());
while ($baris_acctg = pg_fetch_array($hasil_acctg)) {
    $delete = "true";
    $sql_payment = "SELECT * FROM payment WHERE payment_id = '" . $baris_acctg['payment_id'] . "'";
    $hasil_payment = pg_query($sql_payment) or die("Query failed: " . pg_last_error());
    while ($baris_payment = pg_fetch_array($hasil_payment)) {
        if (strtotime($thrudate) < strtotime($baris_payment['created_stamp'])) {
            $delete = "false";
        }
        if (strtotime($fromdate) > strtotime($baris_payment['created_stamp'])) {
            $delete = "false";
        }

        $sql_pa = "SELECT * FROM payment_application WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasil_pa = pg_query($sql_pa) or die("Query failed: " . pg_last_error());
        while ($baris_pa = pg_fetch_array($hasil_pa)) {
            $sql_invoice = "SELECT * FROM invoice WHERE invoice_id = '" . $baris_pa['invoice_id'] . "'";
            $hasil_invoice = pg_query($sql_invoice) or die("Query failed: " . pg_last_error());
            while ($baris_invoice = pg_fetch_array($hasil_invoice)) {
                if (strtotime($thrudate) < strtotime($baris_invoice['created_stamp'])) {
                    $delete = "false";
                }
                if (strtotime($fromdate)> strtotime($baris_invoice['created_stamp'])) {
                    $delete = "false";
                }
            }
        }
    }

    $sql_invoice2 = "SELECT * FROM invoice WHERE invoice_id = '" . $baris_acctg['invoice_id'] . "'";
    $hasil_invoice2 = pg_query($sql_invoice2) or die("Query failed: " . pg_last_error());
    while ($baris_invoice2 = pg_fetch_array($hasil_invoice2)) {
        if (strtotime($thrudate) < strtotime($baris_invoice2['created_stamp'])) {
            $delete = "false";
        }
        if (strtotime($fromdate) > strtotime($baris_invoice2['created_stamp'])) {
            $delete = "false";
        }

        $sql_pa2 = "SELECT * FROM payment_application WHERE payment_id = '" . $baris_invoice2['invoice_id'] . "'";
        $hasil_pa2 = pg_query($sql_pa2) or die("Query failed: " . pg_last_error());
        while ($baris_pa2 = pg_fetch_array($hasil_pa2)) {
            $sql_payment2 = "SELECT * FROM payment WHERE payment_id = '" . $baris_pa2['payment_id'] . "'";
            $hasil_payment2 = pg_query($sql_payment2) or die("Query failed: " . pg_last_error());
            while ($baris_payment2 = pg_fetch_array($hasil_payment2)) {
                if (strtotime($thrudate) < strtotime($baris_payment2['created_stamp'])) {
                    $delete = "false";
                }
                if (strtotime($fromdate) > strtotime($baris_payment2['created_stamp'])) {
                    $delete = "false";
                }
            }
        }
    }

    if ($delete == "true") {
        $sql_acctg_te = "SELECT * FROM acctg_trans_entry WHERE acctg_trans_id = '" . $baris_acctg['acctg_trans_id'] . "'";
        $hasil_acctg_te = pg_query($sql_acctg_te) or die("Query failed: " . pg_last_error());
        while ($baris_acctg_te = pg_fetch_array($hasil_acctg_te)) {
            /* $sql_glre = "SELECT * FROM gl_reconciliation_entry WHERE acctg_trans_id = " . $baris_acctg_te['acctg_trans_id'] . " "
              . "AND acctg_trans_entry_seq_id = " . $baris_acctg_te['acctg_trans_entry_seq_id']; */
            $sqldel_glre = "DELETE FROM gl_reconciliation_entry WHERE acctg_trans_id = '" . $baris_acctg_te['acctg_trans_id'] . "' "
                    . "AND acctg_trans_entry_seq_id = '" . $baris_acctg_te['acctg_trans_entry_seq_id'] . "'";
            $hasildel_glre = pg_query($sqldel_glre) or die("Query failed: " . pg_last_error());

            $sqldel_acctg_te = "DELETE FROM acctg_trans_entry WHERE acctg_trans_id = '" . $baris_acctg_te['acctg_trans_id'] . "' "
                    . "AND acctg_trans_entry_seq_id = '" . $baris_acctg_te['acctg_trans_entry_seq_id'] . "'";
            $hasildel_acctg_te = pg_query($sqldel_acctg_te) or die("Query failed: " . pg_last_error());
        }

        $sqldel_acctg = "DELETE FROM acctg_trans WHERE acctg_trans_id = '" . $baris_acctg['acctg_trans_id'] . "'";
        $hasildel_acctg = pg_query($sqldel_acctg) or die("Query failed: " . pg_last_error());
        if ($hasildel_acctg) {
            $ind++;
        }
    }
}
if ($ind > 0) {
    echo "Acctg Trans Lama Telah Berhasil Dihapus";
} else {
    echo "Tidak ada data yang dihapus";
}

pg_close($conn);
