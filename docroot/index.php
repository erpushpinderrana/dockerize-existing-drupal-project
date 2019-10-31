<h1>Congratulations!! Docker setup connection is successful.</h1>

<h4>Checking MySQL integration with php.</h4>
<?php
$host = 'mysql';
$user = 'drupal';
$pass = 'drupal123';
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("MySql connection is failed: " . $conn->connect_error);
} else {
    echo "MySql connection is successful!";
}

echo phpinfo();

?>