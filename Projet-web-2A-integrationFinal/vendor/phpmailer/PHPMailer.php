<?php
/**
 * Simple PHPMailer Implementation
 * Version simplifiée pour ce projet spécifique
 */
class PHPMailer {
    // Propriétés privées
    private $Host = 'smtp.gmail.com';
    private $Port = 587;
    private $SMTPAuth = true;
    private $Username = '';
    private $Password = '';
    private $SMTPSecure = 'tls';
    private $From = '';
    private $FromName = '';
    private $Subject = '';
    private $Body = '';
    private $isHTML = true;
    private $to = [];
    private $debug = false;
    private $SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    
    // Constructeur
    public function __construct($exceptions = false) {
        // Rien à faire ici pour cette version simplifiée
    }
    
    // Méthodes de configuration
    public function isSMTP() {
        // Rien à faire, c'est toujours SMTP dans cette version
        return $this;
    }
    
    public function Host($host) {
        $this->Host = $host;
        return $this;
    }
    
    public function Port($port) {
        $this->Port = $port;
        return $this;
    }
    
    public function SMTPAuth($auth) {
        $this->SMTPAuth = $auth;
        return $this;
    }
    
    public function Username($username) {
        $this->Username = $username;
        return $this;
    }
    
    public function Password($password) {
        $this->Password = $password;
        return $this;
    }
    
    public function SMTPSecure($secure) {
        $this->SMTPSecure = $secure;
        return $this;
    }
    
    public function From($from) {
        $this->From = $from;
        return $this;
    }
    
    public function FromName($fromName) {
        $this->FromName = $fromName;
        return $this;
    }
    
    public function Subject($subject) {
        $this->Subject = $subject;
        return $this;
    }
    
    public function Body($body) {
        $this->Body = $body;
        return $this;
    }
    
    public function isHTML($isHTML) {
        $this->isHTML = $isHTML;
        return $this;
    }
    
    public function addAddress($address) {
        $this->to[] = $address;
        return $this;
    }
    
    public function SMTPDebug($debug) {
        $this->debug = (bool)$debug;
        return $this;
    }
    
    // Méthode principale pour envoyer l'email
    public function send() {
        // Pas d'adresses de destinataire
        if (empty($this->to)) {
            $this->logError("Aucune adresse de destinataire spécifiée");
            return false;
        }
        
        // Configuration du socket SMTP
        $errno = 0;
        $errstr = '';
        
        // Connexion au serveur SMTP avec le protocole approprié
        $protocol = ($this->Port == 465) ? 'ssl://' : '';
        
        // Configurer le contexte de stream pour désactiver la vérification SSL
        $context = stream_context_create($this->SMTPOptions);
        
        // Connexion avec le contexte SSL personnalisé
        $socket = stream_socket_client(
            $protocol . $this->Host . ':' . $this->Port, 
            $errno, 
            $errstr, 
            30, 
            STREAM_CLIENT_CONNECT, 
            $context
        );
        
        if (!$socket) {
            $this->logError("Connexion échouée au serveur SMTP: $errstr ($errno)");
            return false;
        }
        
        // Lire la réponse du serveur
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '220') {
            $this->logError("Réponse incorrecte du serveur: $response");
            fclose($socket);
            return false;
        }
        
        // Envoyer EHLO
        fputs($socket, "EHLO localhost\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            $this->logError("Échec de EHLO: $response");
            fclose($socket);
            return false;
        }
        
        // Vider les réponses EHLO supplémentaires
        while (substr($response, 3, 1) == '-') {
            $response = fgets($socket, 515);
        }
        
        // Démarrer TLS si nécessaire
        if ($this->SMTPSecure == 'tls') {
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '220') {
                $this->logError("Échec de STARTTLS: $response");
                fclose($socket);
                return false;
            }
            
            // Mettre à niveau la connexion vers TLS avec les options SSL personnalisées
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->logError("Échec de la mise à niveau TLS - Tentative de continuer sans chiffrement");
                // Continuer malgré l'erreur - certains serveurs peuvent fonctionner sans TLS
            }
            
            // Renvoyer EHLO après TLS
            fputs($socket, "EHLO localhost\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                $this->logError("Échec de EHLO après TLS: $response");
                fclose($socket);
                return false;
            }
            
            // Vider les réponses EHLO supplémentaires
            while (substr($response, 3, 1) == '-') {
                $response = fgets($socket, 515);
            }
        }
        
        // Authentification
        if ($this->SMTPAuth) {
            // Envoi de AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '334') {
                $this->logError("Échec de AUTH LOGIN: $response");
                fclose($socket);
                return false;
            }
            
            // Envoi du nom d'utilisateur encodé en base64
            fputs($socket, base64_encode($this->Username) . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '334') {
                $this->logError("Échec de l'authentification (nom d'utilisateur): $response");
                fclose($socket);
                return false;
            }
            
            // Envoi du mot de passe encodé en base64
            fputs($socket, base64_encode($this->Password) . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '235') {
                $this->logError("Échec de l'authentification (mot de passe): $response");
                fclose($socket);
                return false;
            }
        }
        
        // Définir l'expéditeur
        fputs($socket, "MAIL FROM: <{$this->From}>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            $this->logError("Échec de MAIL FROM: $response");
            fclose($socket);
            return false;
        }
        
        // Définir le destinataire
        foreach ($this->to as $recipient) {
            fputs($socket, "RCPT TO: <$recipient>\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                $this->logError("Échec de RCPT TO: $response");
                fclose($socket);
                return false;
            }
        }
        
        // Commencer l'envoi des données
        fputs($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '354') {
            $this->logError("Échec de DATA: $response");
            fclose($socket);
            return false;
        }
        
        // Construire les en-têtes
        $headers = "From: {$this->FromName} <{$this->From}>\r\n";
        $headers .= "Subject: {$this->Subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        // Type de contenu selon isHTML
        if ($this->isHTML) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";
        $headers .= "Date: " . date("D, j M Y H:i:s O") . "\r\n";
        $headers .= "To: " . implode(", ", $this->to) . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "\r\n"; // Ligne vide entre les en-têtes et le corps
        
        // Envoyer les en-têtes et le corps
        fputs($socket, $headers . $this->Body . "\r\n.\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) != '250') {
            $this->logError("Échec d'envoi du message: $response");
            fclose($socket);
            return false;
        }
        
        // Fin de la connexion
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        
        // Si nous arrivons ici, l'email a été envoyé avec succès
        if ($this->debug) {
            file_put_contents(__DIR__ . '/../../logs/email_success.log', date('Y-m-d H:i:s') . ': Email envoyé avec succès à ' . implode(", ", $this->to) . "\n", FILE_APPEND);
        }
        
        return true;
    }
    
    // Journalisation des erreurs
    private function logError($message) {
        if ($this->debug) {
            error_log($message);
            // Enregistrer aussi dans un fichier de log personnalisé
            file_put_contents(__DIR__ . '/../../logs/email_error.log', date('Y-m-d H:i:s') . ': ' . $message . "\n", FILE_APPEND);
        }
    }
}
?> 