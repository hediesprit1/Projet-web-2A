<?php

class TypeVehicule
{
    private $id = null;
    private $type = null;
    private $capacite = null;
    private $categorie = null;

    // Constructor
    public function __construct($type, $capacite, $categorie)
    {
        $this->type = $type;
        $this->capacite = $capacite;
        $this->categorie = $categorie;
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

    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }

    public function getCapacite()
    {
        return $this->capacite;
    }
    public function setCapacite($capacite)
    {
        $this->capacite = $capacite;
    }

    public function getCategorie()
    {
        return $this->categorie;
    }
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }
}
