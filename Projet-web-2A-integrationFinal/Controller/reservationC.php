<?php
require_once('C:/xampp/htdocs/ss/config.php');
include 'C:/xampp/htdocs/ss/model/reservation.php';

class reservationC
{
    public function create($reservation)
    {
        $sql = "INSERT INTO reservation (date_reservation, id_offre, id_vehicule, user_id)
                VALUES (:date_reservation, :id_offre, :id_vehicule, :user_id)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'date_reservation' => $reservation->getDateReservation(),
                'id_offre' => $reservation->getIdOffre(),
                'id_vehicule' => $reservation->getIdVehicule(),
                'user_id' => $reservation->getUserId()
            ]);
            $to_email = "myriamslatni12@gmail.com";
            $subject = "Confirmation de votre réservation";

            // HTML email body
            $body = '
            <html>
            <head>
            <style>
                .email-container {
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
                padding: 20px;
                }
                .content {
                background-color: #ffffff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }
                .title {
                font-size: 20px;
                color: #333333;
                margin-bottom: 15px;
                }
                .text {
                font-size: 16px;
                color: #555555;
                }
                .footer {
                font-size: 14px;
                color: #999999;
                margin-top: 20px;
                }
            </style>
            </head>
            <body>
            <div class="email-container">
                <div class="content">
                <div class="title">Confirmation de votre réservation</div>
                <div class="text">
                    Bonjour,<br><br>
                    Merci pour votre réservation. Nous avons bien reçu votre demande pour la date sélectionnée.<br><br>
                    Vous recevrez une notification lorsque le paiement sera traité avec succès.
                </div>
                <div class="footer">
                    Merci,<br>
                    L’équipe de réservation
                </div>
                </div>
            </div>
            </body>
            </html>
            ';

            // Email headers
            $headers  = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: myriamslatni12@gmail.com";

            // Send email
            if (mail($to_email, $subject, $body, $headers)) {
                echo "Email envoyé avec succès.";
            } else {
                echo "Échec de l'envoi de l'email.";
            }
            header('Location:reservations.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read()
    {
        $sql = "SELECT * FROM reservation";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }


    public function findone($id)
    {
        $sql = "SELECT * FROM reservation WHERE id = '$id'";
        $db = config::getConnexion();
        try {
            $res = $db->query($sql);
            return $res->fetch();
        } catch (Exception $e) {
            die('Erreur:' . $e->getMessage());
        }
    }

    public function update($reservation, $id)
    {
        $sql = "UPDATE reservation SET 
                date_reservation = :date_reservation
                WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'date_reservation' => $reservation->getDateReservation(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function delete()
    {
        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $sql = "DELETE FROM reservation WHERE id = '$id'";
            $db = config::getConnexion();
            try {
                $db->prepare($sql)->execute();
            } catch (Exception $e) {
                die('Erreur:' . $e->getMessage());
            }
        }
    }

    public function generatePDF($reservationId)
    {
        // Fetch the details of the specific reservation
        $reservation = $this->findone($reservationId);

        // Create the HTML for the reservation details
        $html = '<h1>Détails de la réservation</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%">';
        $html .= '<tr><th>Nom</th><th>Date de Réservation</th><th>Offre</th><th>Véhicule</th><th>ID Utilisateur</th></tr>';
        $html .= '<tr>';
        $html .= '<td>' . $reservation['user_id'] . '</td>';  // Assuming user_id is displayed here
        $html .= '<td>' . $reservation['date_reservation'] . '</td>';
        $html .= '<td>' . $reservation['id_offre'] . '</td>';
        $html .= '<td>' . $reservation['id_vehicule'] . '</td>';
        $html .= '<td>' . $reservation['user_id'] . '</td>';  // Assuming this is the user ID
        $html .= '</tr>';
        $html .= '</table>';

        // URL encode the HTML content for the PDF conversion
        $html = urlencode($html);

        // Your pdflayer API key
        $access_key = '89cfd287ca98334e9114fdc7b5239215';

        // API request to generate the PDF
        $pdf = file_get_contents("https://api.pdflayer.com/api/convert?access_key=$access_key&document_html=$html");

        // Save the generated PDF
        file_put_contents("reservation_$reservationId.pdf", $pdf);

        echo "PDF généré avec succès.";
    }
}
