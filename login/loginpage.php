<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
session_start();
if (isset($_SESSION['username'])) {
    header('location: ../index.php');
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <script src="../config/jquery-3.1.1.min.js"></script>
        <script src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
        <script src="../config/cleaningdb.js"></script>
        <title></title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-offset-4 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">Login</div>
                        <div class="panel-body">
                            <form class="form-horizontal" id="login_form" method="post">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Username: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="username" autofocus="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Password: </label>
                                    <div class="col-md-8">
                                        <input type="password" class="form-control" name="pass">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="pull-right btn btn-primary" id="login_btn">Login</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="newusermodal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Daftarkan Akun</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-8">
                                    <form class="form-horizontal" id="buat_akun_form">
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Username: </label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="n_username" autofocus="true">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Password: </label>
                                            <div class="col-md-8">
                                                <input type="password" class="form-control" name="n_pass">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Ulangi Password: </label>
                                            <div class="col-md-8">
                                                <input type="password" class="form-control" name="n_rpass">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <button class="pull-right btn btn-primary" id="buat_akun_btn">Buat Akun</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Modal -->
        </div>
    </body>
</html>
