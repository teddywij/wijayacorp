<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location: login/loginpage.php');
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
        <script src="config/jquery-3.1.1.min.js"></script>
        <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
        <script src="config/cleaningdb.js"></script>
        <title></title>
    </head>
    <body>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-right">
                        <li><a href="login/proses/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php
        $active1 = $active2 = $active3 = $active4 = $active5 = $active6 = $active7 = $active8 = "";
        if (isset($_GET['action'])) {
            if ($_GET['action'] == "1") {
                $judul = "Cleaning Acctg Trans";
                $active1 = "active";
            } elseif ($_GET['action'] == "2") {
                $judul = "Cleaning Fin Misc Account";
                $active2 = "active";
            } elseif ($_GET['action'] == "3") {
                $judul = "Cleaning Invoice";
                $active3 = "active";
            } elseif ($_GET['action'] == "4") {
                $judul = "Cleaning Payment";
                $active4 = "active";
            } elseif ($_GET['action'] == "5") {
                $judul = "Cleaning Return";
                $active5 = "active";
            } elseif ($_GET['action'] == "6") {
                $judul = "Cleaning Picklist";
                $active6 = "active";
            } elseif ($_GET['action'] == "7") {
                $judul = "Cleaning Order";
                $active7 = "active";
            } elseif ($_GET['action'] == "8") {
                $judul = "Cleaning Shipment";
                $active8 = "active";
            }
            $action = $_GET['action'];
        } else {
            $judul = "Cleaning Acctg Trans";
            $action = 1;
            $active1 = "active";
        }
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="panel panel-primary">
                        <div class="panel-heading">Cleaning Database</div>
                        <ul class="nav nav-pills nav-stacked panel-body">
                            <li class="<?php echo $active1; ?>"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=1"; ?>">Acctg Trans</a></li>
                            <li class="<?php echo $active2; ?>"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=2"; ?>">Fin Misc Account</a></li>
                            <li class="<?php echo $active3; ?>"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=3"; ?>">Invoice</a></li>
                            <li class="<?php echo $active8; ?>"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=8"; ?>">Shipment</a></li>
                            <li class="<?php echo $active4; ?>"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=4"; ?>">Payment</a></li>
                            <li class="<?php echo $active5; ?>"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=5"; ?>">Return</a></li>
                            <li class="<?php echo $active6; ?>"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=6"; ?>">Picklist</a></li>
                            <li class="<?php echo $active7; ?>"><a href="<?php echo $_SERVER['PHP_SELF'] . "?action=7"; ?>">Order</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <script>
                        var action = '<?php echo $action ?>';
                    </script>
                    <ol class="breadcrumb">
                        <li class="active"><h4><?php echo $judul; ?></h4></li>
                    </ol>
                    <form class="form-horizontal" id="cleaningdb_form" method="post">
                        <div class="form-group">
                            <label class="col-md-4 control-label">From Date: </label>
                            <div class="col-md-8">
                                <input type="date" name="fromdate" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Thru Date: </label>
                            <div class="col-md-8">
                                <input type="date" name="thrudate" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <button id="cleaning_submit_btn" class="pull-right btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div id="info"></div>
                </div>
            </div>
        </div>
    </body>
</html>
