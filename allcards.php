<?php

// Inclure le fichier `getAccessToken.php` ici
include 'getAccessToken.php';

// Remplacez 'client_id' et 'client_secret' par les valeurs réelles de l'application Blizzard
$client_id = '779309c24ff44c89a22bf75ce274df34';
$client_secret = 'YnHkthF0sfirpZMK2Dl5WMDiUaYd8Z1d';

// Obtenir le token d'accès
$access_token = getAccessToken($client_id, $client_secret);

if ($access_token) {
    // Configuration initiale
    $pageSize = 50; // Nombre de cartes par page
    $totalCards = 5250;
    $totalPages = ceil($totalCards / $pageSize);

    // Traitement du formulaire de filtre
    $selectedSet = isset($_GET['set']) ? $_GET['set'] : '';
    $selectedClassId = isset($_GET['class']) ? $_GET['class'] : '';
    $selectedManaCost = isset($_GET['manaCost']) ? $_GET['manaCost'] : '';
    $selectedAttack = isset($_GET['attack']) ? $_GET['attack'] : '';
    $selectedHealth = isset($_GET['health']) ? $_GET['health'] : '';
    $selectedRarity = isset($_GET['rarity']) ? $_GET['rarity'] : '';
    $selectedMinionType = isset($_GET['minionType']) ? $_GET['minionType'] : '';
    $selectedSpellSchool = isset($_GET['spellSchool']) ? $_GET['spellSchool'] : '';
    $textFilter = isset($_GET['textFilter']) ? $_GET['textFilter'] : '';
    
    // Page actuelle
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

    // URL pour afficher toutes les cartes
    $all_cards_url = 'https://eu.api.blizzard.com/hearthstone/cards?locale=fr_FR&sort=classes:asc,manaCost:asc&pageSize=' . $pageSize . '&access_token=' . $access_token;

    // URL pour le tri avec des filtres
    $filtered_cards_url = 'https://eu.api.blizzard.com/hearthstone/cards?locale=fr_FR&sort=set:asc,manaCost:asc';

    // Ajout des paramètres de filtre à la requête
    if ($selectedSet !== '') {
        $filtered_cards_url .= '&set=' . $selectedSet;
    }

    if ($selectedClassId !== '') {
        $filtered_cards_url .= '&class=' . $selectedClassId;
    }

    if ($selectedManaCost !== '') {
        $filtered_cards_url .= '&manaCost=' . $selectedManaCost;
    }

    if ($selectedAttack !== '') {
        $filtered_cards_url .= '&attack=' . $selectedAttack;
    }

    if ($selectedHealth !== '') {
        $filtered_cards_url .= '&health=' . $selectedHealth;
    }

    if ($selectedRarity !== '') {
        $filtered_cards_url .= '&rarity=' . $selectedRarity;
    }

    if ($selectedMinionType !== '') {
        $filtered_cards_url .= '&minionType=' . $selectedMinionType;
    }

    if ($selectedSpellSchool !== '') {
        $filtered_cards_url .= '&spellSchool=' . $selectedSpellSchool;
    }

    if ($textFilter !== '') {
        $filtered_cards_url .= '&textFilter=' . urlencode($textFilter);
    }

    $filtered_cards_url .= '&access_token=' . $access_token;

    // Sélection de l'URL appropriée en fonction des filtres
    $cards_url = empty($selectedSet) && empty($selectedClassId) && empty($selectedManaCost) && empty($selectedAttack) && empty($selectedHealth) && empty($selectedRarity) && empty($selectedMinionType) && empty($selectedSpellSchool)
        ? $all_cards_url
        : $filtered_cards_url;

// Enregistrement des informations dans la base de données
$conn = new mysqli("localhost", "root", "root", "hearthstone_api_db", 3306);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO user_filters (set_filter, class_filter, mana_cost_filter, attack_filter, health_filter, rarity_filter, minion_type_filter, spell_school_filter, text_filter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $selectedSet, $selectedClassId, $selectedManaCost, $selectedAttack, $selectedHealth, $selectedRarity, $selectedMinionType, $selectedSpellSchool, $textFilter);

if ($stmt->execute()) {
    echo "R";
} else {
    echo "E" . $stmt->error;
}



    // Boucle pour récupérer les pages successives
    for ($page = $currentPage; $page <= $currentPage; $page++) {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $cards_url . '&pageSize=' . $pageSize . '&page=' . $page);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $cards_response = curl_exec($ch);

        // Vérification des erreurs
        if (curl_errno($ch)) {
            echo 'Erreur cURL : ' . curl_error($ch);
        }

        // Décodage de la réponse JSON
        $cards_data = json_decode($cards_response, true);

        // Affichage des détails des cartes (seulement l'image)
        foreach ($cards_data['cards'] as $card) {
            echo '<div class="cards"><img src="' . $card['image'] . '" alt="Card Image"></div>';
        }

        // Fermeture de la session cURL
        curl_close($ch);
    }

    // Affichage de la pagination
    echo '<div class="pagination">';
    for ($page = 1; $page <= $totalPages; $page++) {
        $activeClass = ($page == $currentPage) ? 'active' : '';
        echo '<a class="' . $activeClass . '" href="#" onclick="changePage(' . $page . ')">' . $page . '</a> ';
    }
    echo '</div>';

    $conn->close();
}
?>