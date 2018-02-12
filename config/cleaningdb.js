function cekTanggal() {
    if ($('input[name="fromdate"]').val() === "") {
        alert('Kolom From Date masih kosong!!');
        $('input[name="fromdate"]').focus();
        return false;
    }
    if ($('input[name="thrudate"]').val() === "") {
        alert('Kolom Thru Date masih kosong!!');
        $('input[name="thrudate"]').focus();
        return false;
    }
    return true;
}

function cekUser() {
    if ($('input[name="username"]').val() === "") {
        alert('Kolom Username masih kosong!!');
        $('input[name="username"]').focus();
        return false;
    }
    if ($('input[name="pass"]').val() === "") {
        alert('Kolom Password masih kosong!!');
        $('input[name="pass"]').focus();
        return false;
    }
    return true;
}

$(document).ready(function () {
    /*$('#cleaning_submit_btn').on('click', function () {
     $('#cleaningdb_form').submit();
     $('.container').html('<h3><p>Proses Cleaning Database sedang berlangsung</p><p>Jangan CLOSE Tab ini!!</p></h3>');
     });*/

    $('#login_form').on('submit', function (e) {
        e.preventDefault();
        if (cekUser() === true) {
            $.ajax({
                type: "POST",
                url: "proses/ceklogin.php",
                data: $('#login_form').serialize(),
                success: function (data) {
                    if (data === 'scd') {
                        window.location.reload();
                    } else if (data === 'false') {
                        alert('Anda tidak memiliki hak akses halaman ini');
                    } else if (data === 'false1') {
                        alert('Username atau Password salah');
                    }
                }
            });
        }
    });

    $('#cleaningdb_form').on('submit', function (e) {
        /*$('#info').html('<div class="alert alert-info" role="alert"><p><span><img src="config/util/ajax-loader.gif"></span> Proses Cleaning Database sedang berlangsung</p><p>Jangan CLOSE Tab ini!!</p></div>');*/
        e.preventDefault();
        if (cekTanggal() === true) {
            if (confirm('Apakah Anda yakin akan menghapus data ini?\nPastikan INPUT TANGGAL yang dimasukkan sudah BENAR!')) {
                $('#info').load('config/alert.php');
                $.ajax({
                    type: "POST",
                    url: "proses/action_process.php",
                    data: $('#cleaningdb_form').serialize() + "&action=" + action,
                    success: function (data) {
                        $('#info').html('<div class="alert alert-success" role="alert">' + data + '</div>');
                    }
                });
            }
        }
    });
});