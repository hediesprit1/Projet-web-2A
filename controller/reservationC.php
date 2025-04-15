<?php
require_once('C:/xampp/htdocs/web/config.php');
include 'C:/xampp/htdocs/web/model/reservation.php';

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
}
