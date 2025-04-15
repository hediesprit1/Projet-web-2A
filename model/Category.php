<?php

class Category
{
    private $id = null;
    private $name = null;
    private $description = null;

    // Constructor
    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
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

    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
