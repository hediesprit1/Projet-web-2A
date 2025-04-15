<?php

class Vehicule
{
    private $id = null;
    private $matricule = null;
    private $couleur = null;
    private $modele = null;
    private $marque = null;
    private $typevehicule_id = null; // Added the typevehicule_id

    // Constructor
    public function __construct($matricule, $couleur, $modele, $marque, $typevehicule_id = null)
    {
        $this->matricule = $matricule;
        $this->couleur = $couleur;
        $this->modele = $modele;
        $this->marque = $marque;
        $this->typevehicule_id = $typevehicule_id; // Set typevehicule_id
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

    public function getMatricule()
    {
        return $this->matricule;
    }
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;
    }

    public function getCouleur()
    {
        return $this->couleur;
    }
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;
    }

    public function getModele()
    {
        return $this->modele;
    }
    public function setModele($modele)
    {
        $this->modele = $modele;
    }

    public function getMarque()
    {
        return $this->marque;
    }
    public function setMarque($marque)
    {
        $this->marque = $marque;
    }

    // Getter and Setter for typevehicule_id
    public function getTypevehiculeId()
    {
        return $this->typevehicule_id;
    }

    public function setTypevehiculeId($typevehicule_id)
    {
        $this->typevehicule_id = $typevehicule_id;
    }
}
