<?php include 'db.php';

$id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM sun_moon_pokemon WHERE id = $id");
$pokemon = $result->fetch_assoc();
if (!$pokemon) die("Pokémon not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $height = (int)$_POST['height'];
    $weight = (int)$_POST['weight'];
    $type1 = $conn->real_escape_string($_POST['type1']);
    $type2 = empty($_POST['type2']) ? null : $conn->real_escape_string($_POST['type2']);
    $sprite = $conn->real_escape_string($_POST['sprite']);

    $sql = "UPDATE sun_moon_pokemon SET 
            name='$name', height=$height, weight=$weight,
            type1='$type1', type2=" . ($type2 ? "'$type2'" : "NULL") . ",
            sprite='$sprite' WHERE id = $id";

    if ($conn->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Pokémon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Edit Pokémon #<?= $id ?></h2>
    <form method="post">
        <input name="name" value="<?= htmlspecialchars($pokemon['name']) ?>" required>
        <input name="height" type="number" value="<?= $pokemon['height'] ?>">
        <input name="weight" type="number" value="<?= $pokemon['weight'] ?>">
        <input name="type1" value="<?= $pokemon['type1'] ?>" required>
        <input name="type2" value="<?= $pokemon['type2'] ?>">
        <input name="sprite" value="<?= $pokemon['sprite'] ?>">
        <button type="submit">Update Pokémon</button>
    </form>
    <a class="back-link" href="index.php">← Back to Pokémon List</a>
</div>
</body>
</html>
