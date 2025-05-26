<?php
set_time_limit(0);

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'pokemon_db'; 

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$createTableSQL = "
CREATE TABLE IF NOT EXISTS sun_moon_pokemon (
    id INT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    height INT,
    weight INT,
    type1 VARCHAR(30),
    type2 VARCHAR(30),
    sprite VARCHAR(255)
);
";
$conn->query($createTableSQL);

function fetchAPI($url) {
    $response = @file_get_contents($url);
    if ($response === FALSE) return null;
    return json_decode($response, true);
}

$generationData = fetchAPI("https://pokeapi.co/api/v2/generation/7/");
if (!$generationData) {
    die("Failed to fetch Generation VII data.");
}

$pokemonSpecies = $generationData['pokemon_species'];

foreach ($pokemonSpecies as $species) {
    try {
        $speciesName = $species['name'];
        echo "Processing $speciesName...\n";

        $speciesData = fetchAPI("https://pokeapi.co/api/v2/pokemon-species/{$speciesName}/");
        if (!$speciesData || empty($speciesData['varieties'])) {
            throw new Exception("Missing varieties");
        }

        $defaultVariety = null;
        foreach ($speciesData['varieties'] as $variety) {
            if ($variety['is_default']) {
                $defaultVariety = $variety['pokemon']['name'];
                break;
            }
        }

        if (!$defaultVariety) {
            throw new Exception("No default variety for $speciesName");
        }

        $pokemonData = fetchAPI("https://pokeapi.co/api/v2/pokemon/{$defaultVariety}");
        if (!$pokemonData) {
            throw new Exception("Failed to fetch details for $defaultVariety");
        }

        $id = $pokemonData['id'];
        $name = $conn->real_escape_string($pokemonData['name']);
        $height = $pokemonData['height'];
        $weight = $pokemonData['weight'];
        $types = $pokemonData['types'];

        $type1 = isset($types[0]) ? $types[0]['type']['name'] : null;
        $type2 = isset($types[1]) ? $types[1]['type']['name'] : null;

        $sprite = $conn->real_escape_string($pokemonData['sprites']['front_default'] ?? '');

        $sql = "
            INSERT INTO sun_moon_pokemon (id, name, height, weight, type1, type2, sprite)
            VALUES ($id, '$name', $height, $weight, '$type1', " . ($type2 ? "'$type2'" : "NULL") . ", '$sprite')
            ON DUPLICATE KEY UPDATE name='$name', height=$height, weight=$weight, type1='$type1', type2=" . ($type2 ? "'$type2'" : "NULL") . ", sprite='$sprite';
        ";

        $conn->query($sql);

        sleep(1); 
    } catch (Exception $e) {
        echo "Skipped $speciesName: " . $e->getMessage() . "\n";
    }
}

echo "Done importing Pokémon to MySQL.\n";
$conn->close();
