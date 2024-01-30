<?php

// Fonction pour obtenir le token d'accès
function getAccessToken($client_id, $client_secret) {
    // URL de l'endpoint OAuth
    $oauth_url = "https://oauth.battle.net/token";

    // Paramètres de la requête
    $data = array(
        'grant_type' => 'client_credentials',
    );

    // Configuration de la requête cURL
    $options = array(
        CURLOPT_URL            => $oauth_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => "$client_id:$client_secret",
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $data,
        CURLOPT_SSL_VERIFYPEER => false, // Désactive la vérification SSL (attention : risque de sécurité)
    );

    // Initialisation de la session cURL
    $curl = curl_init();
    curl_setopt_array($curl, $options);

    // Exécution de la requête cURL
    $response = curl_exec($curl);

    // Vérification des erreurs
    if (curl_errno($curl)) {
        echo 'Erreur cURL : ' . curl_error($curl);
        return false;
    }

    // Fermeture de la session cURL
    curl_close($curl);

    // Conversion de la réponse JSON en tableau associatif
    $response_array = json_decode($response, true);

    // Vérifie si le token d'accès est présent dans la réponse
    if (isset($response_array['access_token'])) {
        return $response_array['access_token'];
    } else {
        echo "Erreur lors de la récupération du token.";
        return false;
    }
}

?>
