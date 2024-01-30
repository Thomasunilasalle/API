<?php

// Inclure le fichier `getAccessToken.php` ici
include 'getAccessToken.php';

// Remplacez 'client_id' et 'client_secret' par les valeurs réelles de l'application Blizzard
$client_id = '779309c24ff44c89a22bf75ce274df34';
$client_secret = 'YnHkthF0sfirpZMK2Dl5WMDiUaYd8Z1d';

// Obtenez le token d'accès
$access_token = getAccessToken($client_id, $client_secret);

if ($access_token) {
    // Configuration initiale
    $pageSize = 50;
    $totalCards = 700;
    $totalPages = ceil($totalCards / $pageSize);
    

    // Récupérez le numéro de page actuel à partir de la requête GET
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

    // Traitement du formulaire de filtre
    $selectedTier = isset($_GET['tier']) ? $_GET['tier'] : '';
    $selectedAttack = isset($_GET['attack']) ? $_GET['attack'] : '';
    $selectedHealth = isset($_GET['health']) ? $_GET['health'] : '';


    // URL pour le tri avec des filtres
    $filtered_cards_url = 'https://eu.api.blizzard.com/hearthstone/cards?locale=fr_FR&gameMode=battlegrounds&sort=tier:asc';

    // Ajout des paramètres de filtre à la requête
    if ($selectedTier !== '') {
        $filtered_cards_url .= '&tier=' . $selectedTier;
    }
    if ($selectedAttack !== '') {
        $filtered_cards_url .= '&attack=' . $selectedAttack;
    }
    if ($selectedHealth !== '') {
        $filtered_cards_url .= '&health=' . $selectedHealth;
    }
    $filtered_cards_url .= '&access_token=' . $access_token;

    // Boucle pour récupérer les pages successives
    for ($page = 1; $page <= $totalPages; $page++) {
        // Exclure l'affichage des pages autres que la page actuelle
        if ($page != $currentPage) {
            continue;
        }

        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $filtered_cards_url . '&pageSize=' . $pageSize . '&page=' . $page);
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

    // Affichage des liens de pagination
    echo '<div class="pagination">';
    for ($page = 1; $page <= $totalPages; $page++) {
        echo '<a href="?page=' . $page . '&tier=' . $selectedTier . '&attack=' . $selectedAttack . '&health=' . $selectedHealth . '" onclick="filterCards(' . $page . ')">Page ' . $page . '</a> ';
    }
    echo '</div>';
}
?>
