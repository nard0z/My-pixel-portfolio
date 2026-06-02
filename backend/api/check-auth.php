<?php
require_once 'config.php';

if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true) {
    echo json_encode(["authenticated" => true]);
} else {
    echo json_encode(["authenticated" => false]);
}
?>
