<?php
include 'db.php';

$id = (int)$_GET['id'];
$conn->query("DELETE FROM sun_moon_pokemon WHERE id = $id");

header("Location: index.php");
exit;
?>
