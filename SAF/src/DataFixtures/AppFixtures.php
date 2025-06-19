<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Student;
use App\Entity\StudyGroup;

use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Load some dump data samples to the database
 */
class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 21; $i++) {
            $student = new Student();
            $student->setName('Student'.$i);

            if($i % 2 == 1){
                $student->setSex('male');
                $student->setDateofBirth(new DateTime('1996-10-26'));
                $student->setStudentImageFilename('man.png');
            }
            else{
                $student->setSex('female');
                $student->setDateofBirth(new DateTime('1986-07-08'));
                $student->setStudentImageFilename('women.png');
            }

            if ($i % 3 == 1){
                $student->setPlaceofBirth('Budapest');
            }
            elseif ($i % 3 == 2){
                $student->setPlaceofBirth('Hatvan');
            }
            else{
                $student->setPlaceofBirth('Szeged');
            }

            $student->setEmailAddress($i.'@gmail.com');

            $manager->persist($student);
        }

        for ($i = 1; $i < 21; $i++) {
            $sGroup = new StudyGroup();
            $sGroup->setGroupname('SGroup'.$i);

            if($i % 3 == 1){
                $sGroup->setGroupleader('Boros Ilona');
            }
            elseif ($i % 3 == 2){
                $sGroup->setGroupleader('Varga Jakab');
            }
            else{
                $sGroup->setGroupleader('Falusi Simon');
            }

            if($i % 2 == 1){
                $sGroup->setSubject('English');
                $sGroup->setDatetimeofStudyGroup(new DateTime('1972-05-18'));
            }
            else{
                $sGroup->setSubject('Chemistry');
                $sGroup->setDatetimeofStudyGroup(new DateTime('1989-09-10'));
            }

            $manager->persist($sGroup);
        }

        $user = new User();
        $user->setUsername('Kis Tibor');
        $user->setEmailAddress('kis_tibor@gmail.com');
        $user->setPassword('SAF1980');
        $user->setImageFilename('user.png');

        $manager->persist($user);

        $manager->flush();
    }
}
