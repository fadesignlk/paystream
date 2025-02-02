<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($title) ? 'Paystream - ' . $title : 'Paystream Portal'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php') {
    include __DIR__ . '/navigation.php';
}
?>