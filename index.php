<?php

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'pokemon_db';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM sun_moon_pokemon ORDER BY id";
$result = $conn->query($sql);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $searchSafe = $conn->real_escape_string($search);
    $sql = "SELECT * FROM sun_moon_pokemon WHERE name LIKE '%$searchSafe%' OR id = '$searchSafe' ORDER BY id";
} else {
    $sql = "SELECT * FROM sun_moon_pokemon ORDER BY id";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pokémon Sun & Moon Cards</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            max-width: 1400px;
            margin: 0 auto;
            gap: 20px;
        }
        .card {
            background: white;
            border: 6px solid #ccc;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card .sprite {
            height: 120px;
        }
        .name {
            font-size: 24px;
            font-weight: bold;
            text-transform: capitalize;
            margin: 15px 0 10px;
        }
        .info {
            font-size: 16px;
            margin: 4px 0;
        }
        .card {
            position: relative;
            overflow: hidden;
        }
        .card .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0,0,0,0.75);
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
            padding: 10px;
        }
        .card-header {
            font-size: 18px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
        }
        .poke-id {
            color: #555;
        }
        .poke-name {
            text-transform: capitalize;
        }
        .card:hover .overlay {
            opacity: 1;
        }
        .types {
            margin-top: 10px;
        }
        .type {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            color: white;
            margin: 4px 5px 0;
            font-size: 14px;
            text-transform: capitalize;
        }
        .type-icon {
            width: 32px;
            height: 32px;
            margin: 2px;
            vertical-align: middle;
        }
        .sprite {
            height: 120px;
            filter: drop-shadow(0 3px 5px rgba(0, 0, 0, 0.53));
        }
        .card.normal {
            background: linear-gradient(135deg, #e0e0e0, #cfcfcf);
        }
        .card.fire {
            background: linear-gradient(135deg, #ffe0c2, #ffb180);
        }
        .card.water {
            background: linear-gradient(135deg, #cce4ff, #a0caff);
        }
        .card.electric {
            background: linear-gradient(135deg, #fff6c2, #ffe066);
        }
        .card.grass {
            background: linear-gradient(135deg, #d4fddc, #a8e9af);
        }
        .card.ice {
            background: linear-gradient(135deg, #e0faff, #b0eafc);
        }
        .card.fighting {
            background: linear-gradient(135deg, #f5c7c7, #e08888);
        }
        .card.poison {
            background: linear-gradient(135deg, #529155, #428b46);
        }
        .card.ground {
            background: linear-gradient(135deg, #f8e0b0, #eac97a);
        }
        .card.flying {
            background: linear-gradient(135deg, #d6e4ff, #a8c8ff);
        }
        .card.psychic {
            background: linear-gradient(135deg, #ffd6f6, #f396d6);
        }
        .card.bug {
            background: linear-gradient(135deg, #e8fbcf, #c6e68b);
        }
        .card.rock {
            background: linear-gradient(135deg, #e0d1a5, #c4b383);
        }
        .card.ghost {
            background: linear-gradient(135deg, #d8c7f3, #b39ddb);
        }
        .card.dragon {
            background: linear-gradient(135deg, #c6d5f5, #9ab0dd);
        }
        .card.dark {
            background: linear-gradient(135deg, #d3d3d3, #9e9e9e);
        }
        .card.steel {
            background: linear-gradient(135deg, #e6eaf0, #c4cdd9);
        }
        .card.fairy {
            background: linear-gradient(135deg, #fde0f0, #f7b6d2);
        }

        .card:hover::after {
            content: "✨";
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            animation: sparkle 1s ease infinite;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 0.2; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); }
        }


        <?php
        $typeColors = [
            'normal' => '#b7aca6',
            'fire' => '#bd2027',
            'water' => '#0b76bc',
            'electric' => '#f9e105',
            'grass' => '#0e9948',
            'ice' => '#2e738c',
            'fighting' => '#a73922',
            'poison' => '#14c235', 
            'ground' => '#bba156',
            'flying' => '#7e6cb3',
            'psychic' => '#7f4293',
            'bug' => '#547c05',
            'rock' => '#a87756',
            'ghost' => '#453c69', 
            'dragon' => '#ab8230', 
            'dark' => '#0d323b',
            'steel' => '#93877b',
            'fairy' => '#d24677'
        ];
        foreach ($typeColors as $type => $color) {
            echo ".border-{$type} { border-color: {$color}; }";
            echo ".type-{$type} { background-color: {$color}; }";
        }
        ?>
    </style>
</head>
<body>

<h1>Pokémon - Sun & Moon</h1>

<p style="text-align:center;">
    <a href="create.php">➕ Add New Pokémon</a>
</p>

<form method="GET" style="text-align:center; margin-bottom:20px;">
    <input type="text" name="search" placeholder="Search by name or ID..." 
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
           style="padding:10px 15px; width:250px; border-radius:12px; border:1px solid #ccc;">
    <button type="submit" style="padding:10px 15px; border:none; background:#2ecc71; color:white; border-radius:12px; cursor:pointer;">
        🔍 Search
    </button>
</form>

<div class="grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()):
            $type1 = strtolower($row['type1']);
            $type2 = strtolower($row['type2']);
        ?>
        <div class="card border-<?= $type1 ?> <?= $type1 ?>">
            <img class="sprite" src="<?= htmlspecialchars($row['sprite']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <div class="card-header">
                <span class="poke-id">#<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span>
                <span class="poke-name"><?= htmlspecialchars($row['name']) ?></span>
            </div>
            <div class="info">Height: <?= $row['height'] ?></div>
            <div class="info">Weight: <?= $row['weight'] ?></div>
            <div class="types">
            <img src="types/<?= $type1 ?>.png" alt="<?= $type1 ?>" title="<?= ucfirst($type1) ?>" class="type-icon">
                <?php if (!empty($type2)): ?>
                    <img src="types/<?= $type2 ?>.png" alt="<?= $type2 ?>" title="<?= ucfirst($type2) ?>" class="type-icon">
                <?php endif; ?>
            </div>
            <a href="update.php?id=<?= $row['id'] ?>">Edit</a>
            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this Pokémon?')">Delete</a>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No Pokémon found.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
