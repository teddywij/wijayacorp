<?php

/*$user_name = "postgres";
$password = "ofbiz";
$database = "ofbiz";
$host_name = "localhost";*/

$conn = pg_connect("host=localhost dbname=ofbiz user=postgres password=ofbiz");

//$sql = "SELECT * FROM party";
//$result = pg_query($sql) or die("Query failed: " . pg_last_error());

//while ($line = pg_fetch_row($result)){
//    echo $line[0], ": ", $line[1], "<br>";
//}
//pg_close($conn);