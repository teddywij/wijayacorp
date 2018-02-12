<?php

include '../../config/config.php';

$user_id = $_POST['username'];
$pass = $_POST['pass'];
//$rpass = $_POST['n_rpass'];
//if ($pass == $rpass) {
/* $stmt = $conn->prepare("SELECT current_password, party_id FROM user_login WHERE user_login_id = ?");
  $stmt->bind_param("s", $user_id);
  $stmt->pg_execute();
  $stmt->bind_result($password, $party_id);
  $stmt->fetch(); */

$sql = "SELECT * FROM user_login WHERE user_login_id = '" . $user_id . "'";
$hasil = pg_query($sql) or die("Query failed: " . pg_last_error());
while ($barisql = pg_fetch_array($hasil)) {
    $password = $barisql['current_password'];
    $party_id = $barisql['party_id'];
}

if (!empty($password) && !empty($party_id)) {
    $passchar = str_split($password);
    if ($passchar[0] == "$") {
        $code = 1;
    } elseif ($passchar[0] == "{") {
        $code = 2;
    }

    if ($code == "1") {
        $realpass = explode("$", $password);
        $crypted = $realpass[3];
        $salt = $realpass[2];
        $hasil = base64_encode(sha1($salt . $pass, true));
    } elseif ($code == "2") {
        $realpass = explode("}", $password);
        $crypted = $realpass[2];
        $hasil = base64_encode(sha1($pass, true));
    }

    $hasil = str_replace("+", "-", $hasil);
    $hasil = str_replace("=", "", $hasil);

    if ($hasil == $crypted) {
        $sqlcekrole = "SELECT * FROM party_role WHERE party_id = '" . $party_id . "'";
        $hasilcekrole = pg_query($sqlcekrole) or die("Query failed: " . pg_last_error());
        $ind = 0;
        while ($baris_role = pg_fetch_array($hasilcekrole)) {
            if ($baris_role['role_type_id'] == "OWNER") {
                $ind++;
            }
        }

        if ($ind > 0) {
            session_start();
            $_SESSION['username'] = $crypted;
            $_SESSION['role'] = "OWNER";
            //header('location: ' . $_SERVER['HTTP_REFERER']);
            echo "scd";
        } else {
            echo "false";
        }
    } else {
        echo "false1";
    }
}
//}

pg_close($conn);
