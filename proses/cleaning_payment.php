<?php

//echo "Proses Cleaning Payment Lama sedang berlangsung. Harap tunggu beberapa saat.\n Jangan CLOSE Tab ini!!";

if (isset($payment_id)) {
    $sql_payment = "SELECT * FROM payment WHERE payment_id = '" . $payment_id . "'";
} else {
    $inpay = 0;
    $sql_payment = "SELECT * FROM payment WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
}

//$sql_payment = "SELECT * FROM payment WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
$hasil_payment = pg_query($sql_payment) or die("Query failed: " . pg_last_error());
while ($baris_payment = pg_fetch_array($hasil_payment)) {
    $delete = "true";
    $sql_pa = "SELECT * FROM payment_application WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
    $hasil_pa = pg_query($sql_pa) or die("Query failed: " . pg_last_error());
    while ($baris_pa = pg_fetch_array($hasil_pa)) {
        $sql_inv = "SELECT * FROM invoice WHERE invoice_id = '" . $baris_pa['invoice_id'] . "'";
        $hasil_inv = pg_query($sql_inv) or die("Query failed: " . pg_last_error());
        while ($baris_inv = pg_fetch_array($hasil_inv)) {
            if (strtotime($thrudate) < strtotime($baris_inv['created_stamp'])) {
                $delete = "false";
            }
            if (strtotime($fromdate)> strtotime($baris_inv['created_stamp'])) {
                $delete = "false";
            }
        }
    }

    if ($delete == "true") {
        $sqldel_pa = "DELETE FROM payment_application WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasildel_pa = pg_query($sqldel_pa) or die("Query failed: " . pg_last_error());

        $sql_at = "SELECT * FROM acctg_trans WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasil_at = pg_query($sql_at) or die("Query failed: " . pg_last_error());
        while ($baris_at = pg_fetch_array($hasil_at)) {
            $sqldel_ate = "DELETE FROM acctg_trans_entry WHERE acctg_trans_id = '" . $baris_at['acctg_trans_id'] . "'";
            $hasildel_ate = pg_query($sqldel_ate) or die("Query failed: " . pg_last_error());

            $sqldel_at = "DELETE FROM acctg_trans WHERE acctg_trans_id = '" . $baris_at['acctg_trans_id'] . "'";
            $hasildel_at = pg_query($sqldel_at) or die("Query failed: " . pg_last_error());
        }

        $sqldel_fat = "DELETE FROM fin_account_trans WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasildel_fat = pg_query($sqldel_fat) or die("Query failed: " . pg_last_error());

        $sqldel_pda = "DELETE FROM payment_delete_approval WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasildel_pda = pg_query($sqldel_pda) or die("Query failed: " . pg_last_error());

        $sqldel_pea = "DELETE FROM payment_edit_approval WHERE new_payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasildel_pea = pg_query($sqldel_pea) or die("Query failed: " . pg_last_error());

        $sqldel_peoa = "DELETE FROM payment_edit_approval WHERE old_payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasildel_peoa = pg_query($sqldel_peoa) or die("Query failed: " . pg_last_error());

        $sqldel_pl = "DELETE FROM payment_log WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasildel_pl = pg_query($sqldel_pl) or die("Query failed: " . pg_last_error());

		//Sunavets 2017-03-21 Tambah hapus return terlebih dahulu
		$sqlsel_ris = "SELECT * FROM return_item_response WHERE payment_id='".$baris_payment['payment_id'] . "'";
		$hasil_ris = pg_query($sqlsel_ris) or die("Query failed: " . pg_last_error());
        while ($baris_ris = pg_fetch_array($hasil_ris)) {
        	$sqlsel_return = "SELECT * FROM return_item WHERE return_item_response_id = '" . $baris_ris['return_item_response_id'] . "'";
        	$hasil_return = pg_query($sqlsel_return) or die("Query failed: " . pg_last_error());
        	while ($baris_return = pg_fetch_array($hasil_return)) {
	        	$return_id = $baris_return['return_id'];
	            include 'cleaning_return.php';
	        }
        }
        
    	$sqldel_ris = "DELETE FROM return_item_response WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasildel_ris = pg_query($sqldel_ris) or die("Query failed: " . pg_last_error());
		//--Sunavets 2017-03-21
        $sqldel_payment = "DELETE FROM payment WHERE payment_id = '" . $baris_payment['payment_id'] . "'";
        $hasildel_payment = pg_query($sqldel_payment) or die("Query failed: " . pg_last_error());
        if (!isset($payment_id)) {
            if ($hasildel_payment) {
                $inpay++;
            }
        }
        
    }
}
if (!isset($payment_id)) {
    if ($inpay > 0) {
        echo "Payment Lama Telah Berhasil Dihapus";
    } else {
        echo "Tidak ada data yang dihapus";
    }
    pg_close($conn);
}
