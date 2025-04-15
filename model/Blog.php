<?php

class Blog
{
    private $id = null;
    private $title = null;
    private $content = null;
    private $category_id = null;

    // Constructor
    public function __construct($title, $content, $category_id)
    {
        $this->title = $title;
        $this->content = $content;
        $this->category_id = $category_id;
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

    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContent()
    {
        return $this->content;
    }
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }
}
