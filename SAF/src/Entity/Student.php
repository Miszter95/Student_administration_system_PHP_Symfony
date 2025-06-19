<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student
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
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $sex;

    /**
     * @ORM\Column(type="text")
     */
    private $place_of_birth;

    /**
     * @ORM\Column(type="date")
     */
    private $date_of_birth;

    /**
     * @ORM\Column(type="text")
     */
    private $email_address;

    /**
     * @ORM\Column(type="string")
     */
    private $studentImage_filename;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup", inversedBy="enrolled_students")
     * @ORM\JoinTable(name="students_study_groups")
     */
    private $study_groups;

    public function __construct()
    {
        $this->study_groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getSex(){
        return $this->sex;
    }

    public function setSex($sex){
        $this->sex = $sex;
    }

    public function getPlaceofBirth(){
        return $this->place_of_birth;
    }

    public function setPlaceofBirth($place_of_birth){
        $this->place_of_birth = $place_of_birth;
    }

    public function getDateofBirth(){
        return $this->date_of_birth;
    }

    public function setDateofBirth($date_of_birth){
        $this->date_of_birth = $date_of_birth;
    }

    public function getEmailAddress(){
        return $this->email_address;
    }

    public function setEmailAddress($email_address){
        $this->email_address = $email_address;
    }

    public function getStudentImageFilename()
    {
        return $this->studentImage_filename;
    }

    public function setStudentImageFilename($studentImage_filename)
    {
        $this->studentImage_filename = $studentImage_filename;
    }

    public function getStudyGroups(){
        return $this->study_groups;
    }

    public function setStudyGroups(StudyGroup $study_group){
        $study_group->setEnrolledStudents($this);

        $this->study_groups[] = $study_group;
    }
}
