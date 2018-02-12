<?php

session_start();
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == "OWNER") {
        set_time_limit(0);
        include '../config/config.php';

        $fromdate = $_POST['fromdate'];
        $thrudate = $_POST['thrudate'];
        $action = $_POST['action'];

        if ($action == "1") {
            include 'acctgtrans.php';
        } elseif ($action == "2") {
            include 'cleaning_finmisacc.php';
        } elseif ($action == "3") {
            include 'cleaning_invoice.php';
        } elseif ($action == "4") {
            include 'cleaning_payment.php';
        } elseif ($action == "5") {
            include 'cleaning_return.php';
        } elseif ($action == "6") {
            include 'cleaning_picklist.php';
        } elseif ($action == "7") {
            include 'cleaning_order.php';
        } elseif ($action == "8") {
            include 'delete_shipment_lama.php';
        }
    }
}