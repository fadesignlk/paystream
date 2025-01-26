<?php
$hashedPassword = password_hash("2662123", PASSWORD_DEFAULT);
echo $hashedPassword; // Output the hash to use in your SQL insert
?>