<?php

include '../confog/config.php';

$username = $_POST['n_username'];
$pass = $_POST['n_pass'];
$rpass = $_POST['n_rpass'];

if ($pass == $rpass) {
    $option = [
        'cost' => 11,
        'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
    ];
    $passwordhash = password_hash($pass, PASSWORD_BCRYPT, $option);

    //Bikin function buat cek user_id di DB user_login

    $sql_indb = "INSERT INTO user_login_rekap_database (user_login_id, password, enabled, party_id) "
            . "VALUES ('$username', '$passwordhash', 'Y', 'party')";
    $hasil_indb = pg_query($conn, $sql_indb) or die("Gagal: " . mysqli_error($conn));
    if ($hasil_indb) {
        echo "Akun telah berhasil dibuat";
    }
}
pg_close($conn);
