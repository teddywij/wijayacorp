<?php

//echo "Proses Cleaning Invoice Lama sedang berlangsung. Harap tunggu beberapa saat.\n Jangan CLOSE Tab ini!!";
$ind = 0;

$sql_invoice = "SELECT * FROM invoice WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
$hasil_invoice = pg_query($sql_invoice) or die("Query failed: " . pg_last_error());

while ($baris_inv = pg_fetch_array($hasil_invoice)) {
    $delete = "true";
    $sql_pa = "SELECT * FROM payment_application WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
    $hasil_pa = pg_query($sql_pa) or die("Query failed: " . pg_last_error());
    while ($baris_pa = pg_fetch_array($hasil_pa)) {
        $sql_payment = "SELECT * FROM payment WHERE payment_id = '" . $baris_pa['payment_id'] . "'";
        $hasil_payment = pg_query($sql_payment) or die("Query failed: " . pg_last_error());
        while ($baris_payment = pg_fetch_array($hasil_payment)) {
            if (strtotime($thrudate) < strtotime($baris_payment['created_stamp'])) {
                $delete = "false";
            }
            if (strtotime($fromdate) > strtotime($baris_payment['created_stamp'])) {
				echo ' masukdelete2';
                $delete = "false";
            }
        }
    }

    if ($delete == "true") {
        $sql_at = "SELECT * FROM acctg_trans WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasil_at = pg_query($sql_at) or die("Query failed: " . pg_last_error());
        while ($baris_at = pg_fetch_array($hasil_at)) {
            $sqldel_ate = "DELETE FROM acctg_trans_entry WHERE acctg_trans_id = '" . $baris_at['acctg_trans_id'] . "'";
            $hasildel_ate = pg_query($sqldel_ate) or die("Query failed: " . pg_last_error());
            if ($hasildel_ate) {
                $sqldel_at = "DELETE FROM acctg_trans WHERE acctg_trans_id = '" . $baris_at['acctg_trans_id'] . "'";
                $hasildel_at = pg_query($sqldel_at) or die("Query failed: " . pg_last_error());
            }
        }

        $sqldel_oib = "DELETE FROM order_item_billing WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_oib = pg_query($sqldel_oib) or die("Query failed: " . pg_last_error());

        $sqldel_rib = "DELETE FROM return_item_billing WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_rib = pg_query($sqldel_rib) or die("Query failed: " . pg_last_error());

        $sqldel_sib = "DELETE FROM shipment_item_billing WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_sib = pg_query($sqldel_sib) or die("Query failed: " . pg_last_error());

        $sqldel_ir = "DELETE FROM invoice_role WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_ir = pg_query($sqldel_ir) or die("Query failed: " . pg_last_error());

        $sqldel_is = "DELETE FROM invoice_status WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_is = pg_query($sqldel_is) or die("Query failed: " . pg_last_error());

        $sqldel_pa = "DELETE FROM payment_application WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_pa = pg_query($sqldel_pa) or die("Query failed: " . pg_last_error());

        $sqldel_icm = "DELETE FROM invoice_contact_mech WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_icm = pg_query($sqldel_icm) or die("Query failed: " . pg_last_error());

        $sqldel_riwob = "DELETE FROM return_item_without_order_billing WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_riwob = pg_query($sqldel_riwob) or die("Query failed: " . pg_last_error());

        $sqldel_oab = "DELETE FROM order_adjustment_billing WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_oab = pg_query($sqldel_oab) or die("Query failed: " . pg_last_error());

        $sql_ii = "SELECT * FROM invoice_item WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasil_ii = pg_query($sql_ii) or die("Query failed: " . pg_last_error());
        while ($baris_ii = pg_fetch_array($hasil_ii)) {
            $sqldel_iic = "DELETE FROM invoice_item WHERE parent_invoice_id = '" . $baris_ii['invoice_id'] . "' "
                    . "AND parent_invoice_item_seq_id = '" . $baris_ii['invoice_item_seq_id'] . "'";
            $hasildel_iic = pg_query($sqldel_iic) or die("Query failed: " . pg_last_error());
            if ($hasildel_iic) {
                $sqldel_ii = "DELETE FROM invoice_item WHERE invoice_id = '" . $baris_inv['invoice_id'] . "' "
                        . "AND invoice_item_seq_id = '" . $baris_ii['invoice_item_seq_id'] . "'";
                $hasildel_ii = pg_query($sqldel_ii) or die("Query failed: " . pg_last_error());
            }
        }

        $sqldel_ia = "DELETE FROM invoice_approval WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_ia = pg_query($sqldel_ia) or die("Query failed: " . pg_last_error());

        $sqldel_iat = "DELETE FROM invoice_attribute WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_iat = pg_query($sqldel_iat) or die("Query failed: " . pg_last_error());

        $sql_ich = "SELECT * FROM invoice_cicilan_header WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasil_ich = pg_query($sql_ich) or die("Query failed: " . pg_last_error());
        while ($baris_ich = pg_fetch_array($hasil_ich)) {
            $sqldel_ici = "DELETE FROM invoice_cicilan_item WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
            $hasildel_ici = pg_query($sqldel_ici) or die("Query failed: " . pg_last_error());
            if ($hasildel_ici) {
                $sqldel_ich = "DELETE FROM invoice_cicilan_header WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
                $hasildel_ich = pg_query($sqldel_ich) or die("Query failed: " . pg_last_error());
            }
        }

        $sqldel_icon = "DELETE FROM invoice_content WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_icon = pg_query($sqldel_icon) or die("Query failed: " . pg_last_error());

        $sqldel_iiaf = "DELETE FROM invoice_item_assoc WHERE invoice_id_from = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_iiaf = pg_query($sqldel_iiaf) or die("Query failed: " . pg_last_error());

        $sqldel_iiat = "DELETE FROM invoice_item_assoc WHERE invoice_id_to = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_iiat = pg_query($sqldel_iiat) or die("Query failed: " . pg_last_error());

        $sqldel_iiattr = "DELETE FROM invoice_item_attribute WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_iiattr = pg_query($sqldel_iiattr) or die("Query failed: " . pg_last_error());

        $sqldel_iil = "DELETE FROM invoice_item_log WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_iil = pg_query($sqldel_iil) or die("Query failed: " . pg_last_error());

        $sqldel_te = "DELETE FROM time_entry WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_te = pg_query($sqldel_te) or die("Query failed: " . pg_last_error());

        $sqldel_web = "DELETE FROM work_effort_billing WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_web = pg_query($sqldel_web) or die("Query failed: " . pg_last_error());

        $sqldel_in = "DELETE FROM invoice_note WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_in = pg_query($sqldel_in) or die("Query failed: " . pg_last_error());

        $sql_iterm = "SELECT * FROM invoice_term WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasil_iterm = pg_query($sql_iterm) or die("Query failed: " . pg_last_error());
        while ($baris_iterm = pg_fetch_array($hasil_iterm)) {
            $sqldel_ita = "DELETE FROM invoice_term_attribute WHERE invoice_term_id = '" . $baris_iterm['invoice_term_id'] . "'";
            $hasildel_ita = pg_query($sqldel_ita) or die("Query failed: " . pg_last_error());
            if ($hasildel_ita) {
                $sqldel_iterm = "DELETE FROM invoice_term WHERE invoice_term_id = '" . $baris_iterm['invoice_term_id'] . "'";
                $hasildel_iterm = pg_query($sqldel_iterm) or die("Query failed: " . pg_last_error());
            }
        }

        $sqldel_inv = "DELETE FROM invoice WHERE invoice_id = '" . $baris_inv['invoice_id'] . "'";
        $hasildel_inv = pg_query($sqldel_inv) or die("Query failed: " . pg_last_error());
        if ($hasildel_inv) {
            $ind++;
        }
    }
}
if ($ind > 0) {
    echo "Invoice Lama Telah Berhasil Dihapus";
} else {
    echo "Tidak ada data yang dihapus";
}
pg_close($conn);
