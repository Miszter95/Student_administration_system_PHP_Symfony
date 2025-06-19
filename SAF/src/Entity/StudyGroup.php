<?php

namespace App\Entity;

use App\Repository\StudyGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudyGroupRepository::class)
 */
class StudyGroup
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
    private $group_name;

    /**
     * @ORM\Column(type="string")
     */
    private $group_leader;

    /**
     * @ORM\Column(type="string")
     */
    private $subject;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime_of_study_group;

    /**
     * @ORM\ManyToMany(targetEntity="Student", mappedBy="study_groups")
     */
    private $enrolled_students;

    public function __construct()
    {
        $this->enrolled_students = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupname(){
        return $this->group_name;
    }

    public function setGroupname($group_name){
        $this->group_name = $group_name;
    }

    public function getGroupleader(){
        return $this->group_leader;
    }

    public function setGroupleader($group_leader){
        $this->group_leader = $group_leader;
    }

    public function getSubject(){
        return $this->subject;
    }

    public function setSubject($subject){
        $this->subject = $subject;
    }

    public function getDatetimeofStudyGroup(){
        return $this->datetime_of_study_group;
    }

    public function setDatetimeofStudyGroup($datetime_of_study_group){
        $this->datetime_of_study_group = $datetime_of_study_group;
    }

    public function getEnrolledStudents(){
        return $this->enrolled_students;
    }

    public function setEnrolledStudents(Student $enrolled_student){
        $this->enrolled_students[] = $enrolled_student;
    }
}
