<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $username;

    /**
     * @ORM\Column(type="text")
     */
    private $email_address;

    /**
     * @ORM\Column(type="text")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $image_filename;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(){
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getEmailAddress()
    {
        return $this->email_address;
    }

    public function setEmailAddress($email_address)
    {
        $this->email_address = $email_address;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getImageFilename()
    {
        return $this->image_filename;
    }

    public function setImageFilename($image_filename)
    {
        $this->image_filename = $image_filename;
    }
}
