<?php

//echo "Proses Cleaning Order Lama sedang berlangsung. Harap tunggu beberapa saat.\n Jangan CLOSE Tab ini!!";
$ind = 0;

$sql_oh = "SELECT * FROM order_header WHERE created_stamp BETWEEN DATE('$fromdate') AND DATE('$thrudate')";
$hasil_oh = pg_query($sql_oh) or die("Query failed: " . pg_last_error());
while ($baris_oh = pg_fetch_array($hasil_oh)) {
    $delete = "true";
    $sqldel_oib = "DELETE FROM order_item_billing WHERE order_id = '" . $baris_oh['order_id'] . "'";
    $hasildel_oib = pg_query($sqldel_oib) or die("Query failed: " . pg_last_error());

    $sql_opp = "SELECT * FROM order_payment_preference WHERE order_id = '" . $baris_oh['order_id'] . "'";
    $hasil_opp = pg_query($sql_opp) or die("Query failed: " . pg_last_error());
    while ($baris_opp = pg_fetch_array($hasil_opp)) {
        $sql_payment = "SELECT * FROM payment WHERE payment_preference_id = '" . $baris_opp['order_payment_preference_id'] . "'";
        $hasil_payment = pg_query($sql_payment) or die("Query failed: " . pg_last_error());
        while ($baris_payment = pg_fetch_array($hasil_payment)) {
            if (strtotime($thrudate) < strtotime($baris_payment['created_stamp'])) {
                $delete = "false";
            }
            if (strtotime($fromdate) > strtotime($baris_payment['created_stamp'])) {
                $delete = "false";
            }
        }
    }

    //JL 2017-03-22
    if ($baris_oh['order_type_id'] == "BORROW_ORDER") {
        $return_qty = 0;
        $order_qty = 0;
        $sqlcrhi = "SELECT return_item.return_quantity FROM return_header, return_item WHERE return_item.order_id = '" . $baris_oh['order_id'] . "' "
                . "AND return_header.status_id != 'RETURN_CANCELLED' AND return_header.return_id = return_item.return_id";
        $hasilcrhi = pg_query($sqlcrhi) or die("Query failed: " . pg_last_error());
        if (pg_num_rows($hasilcrhi) > 0) {
            while ($bariscrhi = pg_fetch_row($hasilcrhi)) {
                $return_qty = $return_qty + $bariscrhi[0];
            }

            $sqlcoi = "SELECT quantity, cancel_quantity FROM order_item WHERE order_id = '" . $baris_oh['order_id'] . "' AND status_id != 'ITEM_CANCELLED'";
            $hasilcoi = pg_query($sqlcoi) or die("Query failed: " . pg_last_error());
            while ($bariscoi = pg_fetch_row($hasilcoi)) {
                $order_qty = $order_qty + $bariscoi[0];
				//Sunavets 2017-03-22 Tambah cek quantity yang dibatalkan
				if(!is_null($bariscoi[1]))
				{
					$order_qty = $order_qty - $bariscoi[1];
				}
				//--Sunavets 2017-03-22
            }

            if ($return_qty != $order_qty) {
                $delete = "false";
            }
        }
		//Sunavets 2017-03-22 Jika tidak ada retur maka borrow order ini jangan dihapus
		else
		{
			 $delete = "false";
		}
		//--Sunavets 2017-03-22
    }
    //JL 2017-03-22

    if ($delete == "true") {
        $sql_ship = "SELECT * FROM shipment WHERE primary_order_id = '" . $baris_oh['order_id'] . "'";
        $hasil_ship = pg_query($sql_ship) or die("Query failed: " . pg_last_error());
        while ($baris_ship = pg_fetch_array($hasil_ship)) {
            $sql_iis = "SELECT * FROM item_issuance WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
            $hasil_iis = pg_query($sql_iis) or die("Query failed: " . pg_last_error());
            while ($baris_iis = pg_fetch_array($hasil_iis)) {
                $sqlup_iid = "UPDATE inventory_item_detail SET order_id = NULL, order_item_seq_id = NULL, item_issuance_id = NULL "
                        . "WHERE item_issuance_id = '" . $baris_iis['item_issuance_id'] . "'";
                $hasilup_iid = pg_query($sqlup_iid) or die("Query failed: " . pg_last_error());

                $sqldel_iir = "DELETE FROM item_issuance_role WHERE item_issuance_id = '" . $baris_iis['item_issuance_id'] . "'";
                $hasildel_iir = pg_query($sqldel_iir) or die("Query failed: " . pg_last_error());

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

            $sql_si = "SELECT * FROM shipment_item WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
            $hasil_si = pg_query($sql_si) or die("Query failed: " . pg_last_error());
            while ($baris_si = pg_fetch_array($hasil_si)) {
                if (!empty($baris_si['shipment_id'])) {
                    $shipment_id = $baris_si['shipment_id'];
                    include 'delete_shipment_lama.php';
                }
            }

            $sqldel_ss = "DELETE FROM shipment_status WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
            $hasildel_ss = pg_query($sqldel_ss) or die("Query failed: " . pg_last_error());

            $sqldel_os = "DELETE FROM order_shipment WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
            $hasildel_os = pg_query($sqldel_os) or die("Query failed: " . pg_last_error());

            $sqldel_pp = "DELETE FROM picklist_packed WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
            $hasildel_pp = pg_query($sqldel_pp) or die("Query failed: " . pg_last_error());

            $sqldel_sp = "DELETE FROM shipment_package WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
            $hasildel_sp = pg_query($sqldel_sp) or die("Query failed: " . pg_last_error());

            $sql_at = "SELECT * FROM acctg_trans WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
            $hasil_at = pg_query($sql_at) or die("Query failed: " . pg_last_error());
            while ($baris_at = pg_fetch_array($hasil_at)) {
                $sqldel_ate = "DELETE FROM acctg_trans_entry WHERE acctg_trans_id = '" . $baris_at['acctg_trans_id'] . "'";
                $hasildel_ate = pg_query($sqldel_ate) or die("Query failed: " . pg_last_error());

                $sqldel_at = "DELETE FROM acctg_trans WHERE acctg_trans_id = '" . $baris_at['acctg_trans_id'] . "'";
                $hasildel_at = pg_query($sqldel_at) or die("Query failed: " . pg_last_error());
            }

            $picklist_ids = array();
            $sql_pb = "SELECT * FROM picklist_bin WHERE primary_order_id = '" . $baris_oh['order_id'] . "'";
            $hasil_pb = pg_query($sql_pb) or die("Query failed: " . pg_last_error());
            while ($baris_pb = pg_fetch_array($hasil_pb)) {
                $x = 0;
                if (in_array($baris_pb['picklist_id'], $picklist_ids)) {
                    $x = 1;
                }

                if ($x == "0") {
                    array_push($picklist_ids, $baris_pb['picklist_id']);
                }

                $sqldel_pi = "DELETE FROM picklist_item WHERE picklist_bin_id = '" . $baris_pb['picklist_bin_id'] . "'";
                $hasildel_pi = pg_query($sqldel_pi) or die("Query failed: " . pg_last_error());

                $sqldel_pb = "DELETE FROM picklist_bin WHERE picklist_bin_id = '" . $baris_pb['picklist_bin_id'] . "'";
                $hasildel_pb = pg_query($sqldel_pb) or die("Query failed: " . pg_last_error());
            }

            for ($i = 0; $i < count($picklist_ids); $i++) {
                $sqldel_p = "DELETE FROM picklist WHERE picklist_id = '" . $picklist_ids[$i] . "'";
                $hasildel_p = pg_query($sqldel_p) or die("Query failed: " . pg_last_error());
            }

            $sqldel_ship = "DELETE FROM shipment WHERE shipment_id = '" . $baris_ship['shipment_id'] . "'";
            $hasildel_ship = pg_query($sqldel_ship) or die("Query failed: " . pg_last_error());
        }

        /* $sql_oa = "SELECT * FROM order_adjustment WHERE order_id = '" . $baris_oh['order_id'] . "'";
          $hasil_oa = pg_query($sql_oa) or die("Query failed: " . pg_last_error());
          while ($baris_oa = pg_fetch_array($hasil_oa)) {
          $sqldel_oab = "DELETE FROM order_adjustment_billing WHERE order_adjustment_id = '" . $baris_oa['order_adjustment_id'] . "'";
          $hasildel_oab = pg_query($sqldel_oab) or die("Query failed: " . pg_last_error());

          $sqldel_oa = "DELETE FROM order_adjustment WHERE order_adjustment_id = '" . $baris_oa['order_adjustment_id'] . "'";
          $hasildel_oa = pg_query($sqldel_oa) or die("Query failed: " . pg_last_error());
          } */

        $sqldel_ocm = "DELETE FROM order_contact_mech WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_ocm = pg_query($sqldel_ocm) or die("Query failed: " . pg_last_error());

        $sqldel_oisga = "DELETE FROM order_item_ship_group_assoc WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oisga = pg_query($sqldel_oisga) or die("Query failed: " . pg_last_error());

        $sqldel_oisgir = "DELETE FROM order_item_ship_grp_inv_res WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oisgir = pg_query($sqldel_oisgir) or die("Query failed: " . pg_last_error());

        $sqldel_oisg = "DELETE FROM order_item_ship_group WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oisg = pg_query($sqldel_oisg) or die("Query failed: " . pg_last_error());

        $sql_opp = "SELECT * FROM order_payment_preference WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasil_opp = pg_query($sql_opp) or die("Query failed: " . pg_last_error());
        while ($baris_opp = pg_fetch_array($hasil_opp)) {
            $sql_payment = "SELECT * FROM payment WHERE payment_preference_id = '" . $baris_opp['order_payment_preference_id'] . "'";
            $hasil_payment = pg_query($sql_payment) or die("Query failed: " . pg_last_error());
            while ($baris_payment = pg_fetch_array($hasil_payment)) {
                $payment_id = $baris_payment['payment_id'];
                include 'cleaning_payment.php';
            }
            $sqldel_opp = "DELETE FROM order_payment_preference WHERE order_payment_preference_id = '" . $baris_opp['order_payment_preference_id'] . "'";
            $hasildel_opp = pg_query($sqldel_opp) or die("Query failed: " . pg_last_error());
        }

        $sqldel_or = "DELETE FROM order_role WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_or = pg_query($sqldel_or) or die("Query failed: " . pg_last_error());

        $sqldel_os = "DELETE FROM order_status WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_os = pg_query($sqldel_os) or die("Query failed: " . pg_last_error());

        $sqldel_oifc = "DELETE FROM order_item_force_complete WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oifc = pg_query($sqldel_oifc) or die("Query failed: " . pg_last_error());

        $sqldel_ofc = "DELETE FROM order_force_complete WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_ofc = pg_query($sqldel_ofc) or die("Query failed: " . pg_last_error());

        $sqldel_oic = "DELETE FROM order_item_change WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oic = pg_query($sqldel_oic) or die("Query failed: " . pg_last_error());

        $sqldel_oipi = "DELETE FROM order_item_price_info WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oipi = pg_query($sqldel_oipi) or die("Query failed: " . pg_last_error());

        $sql_sr = "SELECT * FROM shipment_receipt WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasil_sr = pg_query($sql_sr) or die("Query failed: " . pg_last_error());
        while ($baris_sr = pg_fetch_array($hasil_sr)) {
            $sqlup_iid = "UPDATE inventory_item_detail SET order_id = NULL, order_item_seq_id = NULL, return_id = NULL, "
                    . "return_item_seq_id = NULL, shipment_id = NULL, shipment_item_seq_id = NULL, receipt_id = NULL, "
                    . "item_issuance_id = NULL WHERE receipt_id = '" . $baris_sr['receipt_id'] . "'";
            $hasilup_iid = pg_query($sqlup_iid) or die("Query failed: " . pg_last_error());

            $sqldel_sr = "DELETE FROM shipment_receipt WHERE receipt_id = '" . $baris_sr['receipt_id'] . "'";
            $hasildel_sr = pg_query($sqldel_sr) or die("Query failed: " . pg_last_error());
        }

        $sqldel_on = "DELETE FROM order_header_note WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_on = pg_query($sqldel_on) or die("Query failed: " . pg_last_error());

        $sqldel_oap = "DELETE FROM order_approval WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oap = pg_query($sqldel_oap) or die("Query failed: " . pg_last_error());

        $sqldel_oia = "DELETE FROM order_item_assoc WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oia = pg_query($sqldel_oia) or die("Query failed: " . pg_last_error());

        $sqldel_toia = "DELETE FROM order_item_assoc WHERE to_order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_toia = pg_query($sqldel_toia) or die("Query failed: " . pg_last_error());

        $sql_ri = "SELECT * FROM return_item WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasil_ri = pg_query($sql_ri) or die("Query failed: " . pg_last_error());
        while ($baris_ri = pg_fetch_array($hasil_ri)) {
            $return_id = $baris_ri['return_id'];
            include 'cleaning_return.php';
        }

        //JL 2017-02-23
        $sql_oa = "SELECT * FROM order_adjustment WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasil_oa = pg_query($sql_oa) or die("Query failed: " . pg_last_error());
        while ($baris_oa = pg_fetch_array($hasil_oa)) {
            $sqldel_oab = "DELETE FROM order_adjustment_billing WHERE order_adjustment_id = '" . $baris_oa['order_adjustment_id'] . "'";
            $hasildel_oab = pg_query($sqldel_oab) or die("Query failed: " . pg_last_error());

            $sqldel_oa = "DELETE FROM order_adjustment WHERE order_adjustment_id = '" . $baris_oa['order_adjustment_id'] . "'";
            $hasildel_oa = pg_query($sqldel_oa) or die("Query failed: " . pg_last_error());
        }
        //JL 2017-02-23
        //JL 2017-03-02
        $sql_iis2 = "SELECT * FROM item_issuance WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasil_iis2 = pg_query($sql_iis2) or die("Query failed: " . pg_last_error());
        while ($baris_iis2 = pg_fetch_array($hasil_iis2)) {
            $sqlup_iid2 = "UPDATE inventory_item_detail SET order_id = NULL, order_item_seq_id = NULL, item_issuance_id = NULL "
                    . "WHERE item_issuance_id = '" . $baris_iis2['item_issuance_id'] . "'";
            $hasilup_iid2 = pg_query($sqlup_iid2) or die("Query failed: " . pg_last_error());

            $sqldel_iir2 = "DELETE FROM item_issuance_role WHERE item_issuance_id = '" . $baris_iis2['item_issuance_id'] . "'";
            $hasildel_iir2 = pg_query($sqldel_iir2) or die("Query failed: " . pg_last_error());

            $sqldel_iis2 = "DELETE FROM item_issuance WHERE item_issuance_id = '" . $baris_iis2['item_issuance_id'] . "'";
            $hasildel_iis2 = pg_query($sqldel_iis2) or die("Query failed: " . pg_last_error());
        }
        //JL 2017-03-02

        $sqldel_oi = "DELETE FROM order_item WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oi = pg_query($sqldel_oi) or die("Query failed: " . pg_last_error());

        $sqldel_rir = "DELETE FROM return_item_response WHERE replacement_order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_rir = pg_query($sqldel_rir) or die("Query failed: " . pg_last_error());

        $sqldel_ot = "DELETE FROM order_term WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_ot = pg_query($sqldel_ot) or die("Query failed: " . pg_last_error());

        //JL 2017-03-02
        $sqldel_os2 = "DELETE FROM order_shipment WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_os2 = pg_query($sqldel_os2) or die("Query failed: " . pg_last_error());
        //JL 2017-03-02
        
        //JL 2017-03-08
        $sqldel_pp2 = "DELETE FROM picklist_packed WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_pp2 = pg_query($sqldel_pp2) or die("Query failed: " . pg_last_error());
        
        $sql_pb2 = "DELETE FROM picklist_bin WHERE primary_order_id = '" . $baris_oh['order_id'] . "'";
        $hasil_pb2 = pg_query($sql_pb2) or die("Query failed: " . pg_last_error());
        
        $sql_ship2 = "SELECT * FROM shipment WHERE primary_order_id = '".$baris_oh['order_id']."'";
        $hasil_ship2 = pg_query($sql_ship2) or die("Query failed: " . pg_last_error());
        while ($baris_ship2 = pg_fetch_array($hasil_ship2)){
            $shipment_id = $baris_ship2['shipment_id'];
            include 'delete_shipment_lama.php';
        }
        
        $sqldel_ship2 = "DELETE FROM shipment WHERE primary_order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_ship2 = pg_query($sqldel_ship2) or die("Query failed: " . pg_last_error());
        //JL 2017-03-08
		
		//Sunavets 2017-03-21 Hapus index promo detail lalu rekap itu
		$sql_indexPromoDetail = "SELECT * FROM index_promo_detail WHERE order_id = '".$baris_oh['order_id']."'";
        $hasil_indexPromoDetail = pg_query($sql_indexPromoDetail) or die("Query failed: " . pg_last_error());
        while ($indexPromoDetail = pg_fetch_array($hasil_indexPromoDetail)){
			 $sql_indexPromoRecap = "SELECT * FROM index_promo_detail_recap WHERE index_promo_id = '".$indexPromoDetail['index_promo_id']."' AND party_id='".$indexPromoDetail['party_id']."'";;
			 $hasil_indexPromoRecap = pg_query($sql_indexPromoRecap) or die("Query failed: " . pg_last_error());
			 if(pg_num_rows($hasil_indexPromoRecap) != 0)
			 {
			 	while ($indexPromoRecap = pg_fetch_array($hasil_indexPromoRecap))
			 	{
					$sql_indexPromoRecapUpdate = "UPDATE index_promo_detail_recap SET s_o=s_o+" .$indexPromoDetail['s_o'] . ", completed=completed+". $indexPromoDetail['completed'] . ", cancelled=cancelled+"
					 . $indexPromoDetail['cancelled']. ", total=total+". $indexPromoDetail['total']
					 ."WHERE index_promo_id='".$indexPromoDetail['index_promo_id']."' AND party_id='".$indexPromoDetail['party_id']."'";
					 $hasil_indexPromoRecapUpdate = pg_query($sql_indexPromoRecapUpdate) or die("Query failed: " . pg_last_error());
				}
			 }
			 else{
			 	$sql_countRow=pg_query("SELECT count(*) as total from index_promo_detail_recap");
				$data=pg_fetch_assoc($sql_countRow);
				$id_recap = $data['total'] + 1;
			 	$sql_indexPromoRecapInsert = "INSERT INTO index_promo_detail_recap (index_promo_recap_id, party_id, index_promo_id, s_o, completed, cancelled, total, created_by) VALUES ('" . $id_recap . "', '". 
			 	$indexPromoDetail['party_id']. "', '". 
			 	$indexPromoDetail['index_promo_id']. "', '". 
			 	$indexPromoDetail['s_o']. "', '". 
			 	$indexPromoDetail['completed']. "', '". 
			 	$indexPromoDetail['cancelled']. "', '". 
			 	$indexPromoDetail['total']. "', '". 
			 	$_SESSION['userlogin']. "')";
			 	$hasil_indexPromoRecapInsert = pg_query($sql_indexPromoRecapInsert) or die("Query failed: " . pg_last_error());
			 }
			$sql_hapusIndexPromoDetail = "DELETE FROM index_promo_detail WHERE order_id='" . $indexPromoDetail['order_id'] ."' AND party_id='" .$indexPromoDetail['party_id'] . "'";
        	$hasil_hapusIndexPromoDetail = pg_query($sql_hapusIndexPromoDetail) or die("Query failed: " . pg_last_error());
        }
		
		$sql_tebusMurahDetail = "SELECT * FROM tebus_murah_detail WHERE order_id = '".$baris_oh['order_id']."'";
        $hasil_tebusMurahDetail = pg_query($sql_tebusMurahDetail) or die("Query failed: " . pg_last_error());
        while ($tebusMurahDetail = pg_fetch_array($hasil_tebusMurahDetail)){
			 $sql_tebusMurahRecap = "SELECT * FROM tebus_murah_detail_recap WHERE tebus_murah_id = '".$tebusMurahDetail['tebus_murah_id']."' AND party_id='".$tebusMurahDetail['party_id']."'";
			 $hasil_tebusMurahRecap = pg_query($sql_tebusMurahRecap) or die("Query failed: " . pg_last_error());
			 if(pg_num_rows($hasil_tebusMurahRecap) != 0)
			 {
			 	while ($tebusMurahRecap = pg_fetch_array($hasil_tebusMurahRecap))
			 	{
					$sql_tebusMurahRecapUpdate = "UPDATE tebus_murah_detail_recap SET s_o=s_o+" .$tebusMurahDetail['s_o'] . ", completed=completed+". $tebusMurahDetail['completed'] . ", cancelled=cancelled+"
					 . $tebusMurahDetail['cancelled']. ", total=total+". $tebusMurahDetail['total']
					 ."WHERE tebus_murah_id='".$tebusMurahDetail['tebus_murah_id']."' AND party_id='".$tebusMurahDetail['party_id']."'";
					 $hasil_tebusMurahRecapUpdate = pg_query($sql_tebusMurahRecapUpdate) or die("Query failed: " . pg_last_error());
				}
			 }
			 else{
			 	$sql_countRow=pg_query("SELECT count(*) as total from tebus_murah_detail_recap");
				$data=pg_fetch_assoc($sql_countRow);
				$id_recap = $data['total'] + 1;
			 	$sql_tebusMurahRecapInsert = "INSERT INTO tebus_murah_detail_recap (tebus_murah_recap_id, party_id, tebus_murah_id, s_o, completed, cancelled, total, created_by) VALUES ('" . $id_recap. "', '". 
			 	$tebusMurahDetail['party_id']. "', '". 
			 	$tebusMurahDetail['tebus_murah_id']. "', '". 
			 	$tebusMurahDetail['s_o']. "', '". 
			 	$tebusMurahDetail['completed']. "', '". 
			 	$tebusMurahDetail['cancelled']. "', '". 
			 	$tebusMurahDetail['total']. "', '". 
			 	$_SESSION['userlogin'] . "')";
			 	$hasil_tebusMurahRecapInsert = pg_query($sql_tebusMurahRecapInsert) or die("Query failed: " . pg_last_error());
			 }
			 $sql_hapusTebusMurahDetail = "DELETE FROM tebus_murah_detail WHERE order_id='" . $tebusMurahDetail['order_id'] ."' AND party_id='" .$tebusMurahDetail['party_id'] . "'";
        	 $hasil_hapusTebusMurahDetail = pg_query($sql_hapusTebusMurahDetail) or die("Query failed: " . pg_last_error());
        }
		//--Sunavets 2017-03-21
		
        $sqldel_oh = "DELETE FROM order_header WHERE order_id = '" . $baris_oh['order_id'] . "'";
        $hasildel_oh = pg_query($sqldel_oh) or die("Query failed: " . pg_last_error());
        if ($hasildel_oh) {
            $ind++;
        }
    }
}
if ($ind > 0) {
    echo "Order Lama Telah Berhasil Dihapus";
} else {
    echo "Tidak ada data yang dihapus";
}
pg_close($conn);
