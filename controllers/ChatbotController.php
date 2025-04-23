<?php
class ChatbotController {
    public function index() {
        // Vérifier si l'utilisateur est connecté et est un utilisateur normal (non admin)
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
            // Rediriger vers le tableau de bord approprié
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                header("Location: index.php?action=backoffice_dashboard");
            } else {
                header("Location: index.php?action=login");
            }
            exit();
        }
        
        // Afficher la page du chatbot
        include 'views/chatbot/index.php';
    }

    public function processMessage() {
        // Vérifier si l'utilisateur est connecté et est un utilisateur normal (non admin)
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Accès non autorisé']);
            exit;
        }
        
        // Vérifier si la méthode de requête est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Méthode non autorisée']);
            exit;
        }

        // Récupérer le message de l'utilisateur
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        if (empty($message)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Message vide']);
            exit;
        }

        // Clé API Gemini
        $apiKey = "AIzaSyD_Twlo_xPehYT1TRKEo4otbVfRUF9mbeU";
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $apiKey;

        // Préparer les données pour l'API Gemini
        $data = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => $message
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "maxOutputTokens" => 800
            ]
        ];

        // Initier une session cURL
        $ch = curl_init($url);
        
        // Configurer la requête cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        // Exécuter la requête cURL
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Fermer la session cURL
        curl_close($ch);

        // Vérifier si la requête a réussi
        if ($httpCode == 200) {
            $responseData = json_decode($response, true);
            
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $botResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
                header('Content-Type: application/json');
                echo json_encode(['response' => $botResponse]);
                exit;
            }
        }

        // En cas d'erreur
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Erreur lors de la communication avec l\'API Gemini', 
            'details' => $response,
            'http_code' => $httpCode
        ]);
    }
} 