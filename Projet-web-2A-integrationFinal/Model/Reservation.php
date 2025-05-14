<?php

class Reservation
{
    private $id = null;
    private $date_reservation = null;
    private $id_offre = null;
    private $id_vehicule = null;
    private $user_id = null;

    // Constructor
    public function __construct($date_reservation, $id_offre, $id_vehicule, $user_id)
    {
        $this->date_reservation = $date_reservation;
        $this->id_offre = $id_offre;
        $this->id_vehicule = $id_vehicule;
        $this->user_id = $user_id;
    }

    // Getters and Setters

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDateReservation()
    {
        return $this->date_reservation;
    }
    public function setDateReservation($date_reservation)
    {
        $this->date_reservation = $date_reservation;
    }

    public function getIdOffre()
    {
        return $this->id_offre;
    }
    public function setIdOffre($id_offre)
    {
        $this->id_offre = $id_offre;
    }

    public function getIdVehicule()
    {
        return $this->id_vehicule;
    }
    public function setIdVehicule($id_vehicule)
    {
        $this->id_vehicule = $id_vehicule;
    }

    public function getUserId()
    {
        return $this->user_id;
    }
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
}
