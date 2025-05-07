<?php
class User {

    public int $id;
    private string $name;
    private string $pwd;


    public function __construct($name=null, $pwd=null)
    {
        $this->name= $name;
        $this->pwd= $pwd;
    }

    /**
     * Get the value of name
     */ 
    public function getname()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setname($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of pwd
     *
     * @return string
     */
    public function getPwd(): string
    {
        return $this->pwd;
    }

    /**
     * Set the value of pwd
     *
     * @param string $pwd
     *
     * @return self
     */
    public function setPwd(string $pwd): self
    {
        $this->pwd = $pwd;

        return $this;
    }
}
?>