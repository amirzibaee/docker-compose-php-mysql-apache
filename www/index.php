<h1>Hello World!</h1>
<h4>Attempting a MySQL connection from PHP ...</h4>
<?php
$DB_HOST = 'mysql';
$DB_USER = 'app';
$DB_PASS = 'app';
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
} else {
    echo "Connection to MySQL successfully established!";
}
?>
