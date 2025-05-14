<?php
class EmailService {
    private $smtp_host = 'smtp.gmail.com';
    private $smtp_port = 587;
    private $smtp_username = 'hamilay711@gmail.com'; // Adresse email à utiliser pour l'envoi
    private $smtp_password = 'jbvh wyty zmqa bjdk'; // Clé d'application corrigée
    private $smtp_from_email = 'hamilay711@gmail.com';
    private $smtp_from_name = 'ShareMyRide';
    private $admin_email = 'hamilay711@gmail.com'; // Adresse email de l'administrateur qui reçoit les notifications
    private $debug = true; // Pour afficher les erreurs pendant le développement

    /**
     * Envoie un email de notification pour un nouveau véhicule
     * 
     * @param array $vehicule Les détails du véhicule ajouté
     * @return bool True si l'email a été envoyé avec succès, False sinon
     */
    public function sendVehicleAddedEmail($vehicule) {
        // Assurer que la session est démarrée
        if (!isset($_SESSION)) {
            session_start();
        }
        
        // Déterminer le destinataire de l'email
        $recipient_email = $this->admin_email; // Par défaut, l'administrateur
        
        // Si un utilisateur est connecté, envoyer l'email à cet utilisateur
        if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
            $recipient_email = $_SESSION['user_email'];
        }
        
        // Générer le contenu HTML de l'email
        $message = $this->generateVehicleEmailTemplate($vehicule);
        
        // Sujet de l'email
        $subject = "Nouveau véhicule ajouté : {$vehicule['marque']} {$vehicule['modele']}";
        
        try {
            // Utiliser PHPMailer pour l'envoi d'email
            require_once(__DIR__ . '/../vendor/phpmailer/PHPMailer.php');
            
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host($this->smtp_host);
            $mail->Port($this->smtp_port);
            $mail->SMTPAuth(true);
            $mail->Username($this->smtp_username);
            $mail->Password($this->smtp_password);
            $mail->SMTPSecure('tls');
            $mail->From($this->smtp_from_email);
            $mail->FromName($this->smtp_from_name);
            $mail->Subject($subject);
            $mail->Body($message);
            $mail->isHTML(true);
            $mail->addAddress($recipient_email);
            
            if ($this->debug) {
                $mail->SMTPDebug(true);
                // Log le destinataire pour le débogage
                file_put_contents(__DIR__ . '/../logs/email_recipient.log', 
                    date('Y-m-d H:i:s') . ': Email destiné à ' . $recipient_email . "\n", 
                    FILE_APPEND);
            }
            
            return $mail->send();
            
        } catch (Exception $e) {
            if ($this->debug) {
                error_log("Erreur d'envoi d'email: " . $e->getMessage());
                // Enregistrer l'erreur dans un fichier pour le débogage
                file_put_contents(__DIR__ . '/../logs/email_error.log', date('Y-m-d H:i:s') . ': ' . $e->getMessage() . "\n", FILE_APPEND);
            }
            
            // En cas d'échec, tenter d'utiliser mail() en dernier recours
            return $this->sendWithMailFunction($recipient_email, $subject, $message);
        }
    }
    
    /**
     * Methode de secours en utilisant la fonction mail() standard
     */
    private function sendWithMailFunction($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->smtp_from_name} <{$this->smtp_from_email}>" . "\r\n";
        
        return mail($to, $subject, $message, $headers);
    }
    
    /**
     * Génère le template HTML de l'email avec les détails du véhicule
     */
    private function generateVehicleEmailTemplate($vehicule) {
        // Le template HTML reste identique à votre code d'origine
        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Nouveau véhicule ajouté</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                }
                .header {
                    text-align: center;
                    padding: 20px;
                    background-color: #0d6efd;
                    color: white;
                    border-radius: 5px 5px 0 0;
                }
                .content {
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-top: none;
                    border-radius: 0 0 5px 5px;
                }
                .vehicle-title {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 15px;
                    color: #0d6efd;
                }
                .vehicle-image {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .vehicle-image img {
                    max-width: 100%;
                    max-height: 300px;
                    border-radius: 5px;
                }
                .vehicle-details {
                    background-color: #f9f9f9;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .detail-row {
                    display: flex;
                    margin-bottom: 10px;
                }
                .detail-label {
                    font-weight: bold;
                    width: 120px;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 12px;
                    color: #666;
                }
                .btn {
                    display: inline-block;
                    background-color: #0d6efd;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 15px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>ShareMyRide</h1>
                <p>Un nouveau véhicule a été ajouté</p>
            </div>
            <div class="content">
                <div class="vehicle-title">
                    '.htmlspecialchars($vehicule['marque']).' '.htmlspecialchars($vehicule['modele']).'
                </div>';
                
        // Ajouter l'image si disponible
        if (!empty($vehicule['image'])) {
            $imageUrl = 'http://'.$_SERVER['HTTP_HOST'].'/gvehicule/'.$vehicule['image'];
            $html .= '
                <div class="vehicle-image">
                    <img src="'.$imageUrl.'" alt="'.htmlspecialchars($vehicule['marque']).' '.htmlspecialchars($vehicule['modele']).'">
                </div>';
        }
        
        $html .= '
                <div class="vehicle-details">
                    <div class="detail-row">
                        <div class="detail-label">Marque:</div>
                        <div>'.htmlspecialchars($vehicule['marque']).'</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Modèle:</div>
                        <div>'.htmlspecialchars($vehicule['modele']).'</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Matricule:</div>
                        <div>'.htmlspecialchars($vehicule['matricule']).'</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Couleur:</div>
                        <div>'.htmlspecialchars($vehicule['couleur']).'</div>
                    </div>';
                    
        // Ajouter le type s'il est disponible
        if (!empty($vehicule['type'])) {
            $html .= '
                    <div class="detail-row">
                        <div class="detail-label">Type:</div>
                        <div>'.htmlspecialchars($vehicule['type']).'</div>
                    </div>';
        }
        
        // Ajouter la capacité si elle est disponible
        if (!empty($vehicule['capacite'])) {
            $html .= '
                    <div class="detail-row">
                        <div class="detail-label">Capacité:</div>
                        <div>'.htmlspecialchars($vehicule['capacite']).' places</div>
                    </div>';
        }
        
        // Ajouter la catégorie si elle est disponible
        if (!empty($vehicule['categorie'])) {
            $html .= '
                    <div class="detail-row">
                        <div class="detail-label">Catégorie:</div>
                        <div>'.htmlspecialchars($vehicule['categorie']).'</div>
                    </div>';
        }
        
        $html .= '
                </div>
                
                <div style="text-align: center;">
                    <a href="http://'.$_SERVER['HTTP_HOST'].'/gvehicule/view/front-office/vehicule-details.php?id='.$vehicule['id'].'" class="btn">Voir les détails du véhicule</a>
                </div>
                
                <div class="footer">
                    <p>Ce message a été envoyé automatiquement par le système ShareMyRide.</p>
                    <p>© '.date('Y').' ShareMyRide - Tous droits réservés</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
?> 