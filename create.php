<?php include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $height = (int)$_POST['height'];
    $weight = (int)$_POST['weight'];
    $type1 = $conn->real_escape_string($_POST['type1']);
    $type2 = empty($_POST['type2']) ? null : $conn->real_escape_string($_POST['type2']);
    $sprite = $conn->real_escape_string($_POST['sprite']);

    $sql = "INSERT INTO sun_moon_pokemon (id, name, height, weight, type1, type2, sprite)
            VALUES ($id, '$name', $height, $weight, '$type1', " . ($type2 ? "'$type2'" : "NULL") . ", '$sprite')";

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
    <title>Add Pokémon</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
<div class="container">
    <h2>Add New Pokémon</h2>
    <form method="post">
        <input name="id" type="number" placeholder="ID" required>
        <input name="name" placeholder="Name" required>
        <input name="height" type="number" placeholder="Height">
        <input name="weight" type="number" placeholder="Weight">
        <input name="type1" placeholder="Primary Type" required>
        <input name="type2" placeholder="Secondary Type (optional)">
        <input name="sprite" placeholder="Sprite URL">
        <button type="submit">Create Pokémon</button>
    </form>
    <a class="back-link" href="index.php">← Back to Pokémon List</a>
</div>
</body>
</html>

