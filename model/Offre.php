<?php

class Offre
{
    private $id = null;
    private $id_vehicule = null;
    private $dispo = null;
    private $prix = null;

    // Constructor
    public function __construct($id_vehicule, $dispo, $prix)
    {
        $this->id_vehicule = $id_vehicule;
        $this->dispo = $dispo;
        $this->prix = $prix;
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

    public function getIdVehicule()
    {
        return $this->id_vehicule;
    }
    public function setIdVehicule($id_vehicule)
    {
        $this->id_vehicule = $id_vehicule;
    }

    public function getDispo()
    {
        return $this->dispo;
    }
    public function setDispo($dispo)
    {
        $this->dispo = $dispo;
    }

    public function getPrix()
    {
        return $this->prix;
    }
    public function setPrix($prix)
    {
        $this->prix = $prix;
    }
}
