<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\User;

use App\Form\StudentsType;
use App\Form\StudentType;

use App\Repository\StudentRepository;

use App\Service\FileUploader;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

class StudentController extends AbstractController
{
    /**
     * Function that displays the /students template with data
     * @param Request $request
     * @param PaginatorInterface $paginator - Interface for paginate the Student entities
     * @return Response
     */
    public function studentsShow(Request $request, PaginatorInterface $paginator) : Response{

        $allStudents = $this->getDoctrine()->getRepository(Student::class)->findBY(array(),array('name' => 'ASC'));
        $study_groups = $this->getDoctrine()->getRepository(StudyGroup::class)->findBY(array(),array('group_name' => 'ASC'));
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $student_in_groups = 0;

        foreach ($study_groups as $group){

            $student_in_groups += count($group->getEnrolledStudents());
        }

        $students = $paginator->paginate($allStudents, $request->query->getInt('page',1),10);

        return $this->render('views/students.html.twig', array('students' => $students, 'allStudents' => $allStudents,'study_groups' => $study_groups, 'users' => $users, 'student_in_groups' => $student_in_groups));
    }

    /**
     * Edit multiple selected Students entity (max 5 due to email sending limit)
     * @param Request $request
     * @param FileUploader $fileUploader - To upload the student's image
     * @param MainController $mainController - To reach the sendEmail method in MainController
     * @param MailerInterface $mailer - Interface to send the email
     * @return Response
     */
    public function editStudent(Request $request, FileUploader $fileUploader, MainController $mainController, MailerInterface $mailer): Response
    {
        $idArray = $request->get("id");
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $studentArray = array();
        $oldSGroup = array();
        $changes = array();

        if($idArray != null && count($idArray) < 6) {

            $study_Group = new StudyGroup();

            foreach ($idArray as $id){

                $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
                $oldValue = '';
                foreach ($student->getStudyGroups() as $tag){
                    $oldValue .= $tag->getId() . ";";
                }

                array_push($oldSGroup,$oldValue);

                array_push($studentArray,$student);
                $study_Group->getEnrolledStudents()->add($student);

            }

            $form = $this->createForm(StudentsType::class,  $study_Group, ['option' => 2]);

            for ($i = 0; $i < count($idArray); $i++){

                $form->get('enrolled_students')[$i]->get('oldValue')->setData($oldSGroup[$i]);
            }

            $form->handleRequest($request);

                 if ($form->isSubmitted() && $form->isValid()) {

                        $entityManager = $this->getDoctrine()->getManager();

                        for ($i = 0; $i < count($idArray); $i++){

                            $sGroups = $form->get('enrolled_students')[$i]->get('study_groups')->getData();

                            if (count($sGroups) > 4) {

                                $this->addFlash('error','Not allowed more than 4 study groups!');
                                return $this->redirectToRoute('edit_student', array('id' => $idArray));

                            }

                            $uploadedImage = $form->get('enrolled_students')[$i]->get('imageFile')->getData();

                            if($uploadedImage != null) {

                                $imageFile = $studentArray[$i]->getStudentImageFilename();
                                $fileUploader->deleteFile('../public/images/studentspics/',$imageFile);

                                $newFilename = $fileUploader->upload($uploadedImage);

                                $studentArray[$i]->setStudentImageFilename($newFilename);
                            }

                            $uow = $entityManager->getUnitOfWork();
                            $uow->computeChangeSets();
                            $changeSet = $uow->getEntityChangeSet($studentArray[$i]);
                            $change = $uow->getScheduledCollectionUpdates($studentArray[$i]);


                            if (isset($changeSet['name'])) {
                                array_push($changes, "Your profile name has been changed to: " . $studentArray[$i]->getName() . "!");
                            }

                            if (isset($changeSet['sex'])) {
                                array_push($changes, "Your sex has been changed to: " . $studentArray[$i]->getSex() . "!");
                            }

                            if (isset($changeSet['place_of_birth'])) {
                                array_push($changes, "Your place of birth has been changed to: " . $studentArray[$i]->getPlaceofBirth() . "!");
                            }

                            if (isset($changeSet['date_of_birth'])) {
                                array_push($changes, "Your date of birth has been changed to: " . $studentArray[$i]->getDateofBirth()->format("Y.m.d") . "!");
                            }

                            if (isset($changeSet['email_address'])) {
                                array_push($changes, "Your email address has been changed to: " . $studentArray[$i]->getEmailAddress() . "!");
                            }

                            if (isset($changeSet['studentImage_filename'])) {
                                array_push($changes, "Your profile picture has been changed!");
                            }

                            if(isset($change['study_groups'])){
                                array_push($changes, "Your study groups are changed!");
                            }

                            $oldValue = $form->get('enrolled_students')[$i]->get('oldValue')->getData();
                            $oldValues = explode(';', $oldValue);
                            array_pop($oldValues);
                            $newValues = $studentArray[$i]->getStudyGroups();

                            $leave = false;
                            $join = false;


                            foreach ($oldValues as $oGroup) {

                                foreach ($newValues as $nGroup) {

                                    if ($oGroup == $nGroup->getId()) {
                                        $leave = true;
                                    }
                                }

                                if ($leave == false) {
                                    $oldStudyGroup = $this->getDoctrine()->getRepository(StudyGroup::class)->find($oGroup);
                                    array_push($changes, "The profile named " . $studentArray[$i]->getName() . " has been deleted from the " . $oldStudyGroup->getGroupname() . " study group!");
                                }

                                $leave = false;
                            }

                            foreach ($newValues as $nGroup) {

                                foreach ($oldValues as $oGroup) {

                                    if ($nGroup->getId() == $oGroup) {
                                        $join = true;
                                    }
                                }
                                if ($join == false) {
                                    $newStudyGroup = $this->getDoctrine()->getRepository(StudyGroup::class)->find($nGroup);
                                    array_push($changes, "The profile named " . $studentArray[$i]->getName() . " has been joined to the " . $newStudyGroup->getGroupname() . " study group!");
                                }

                                $join = false;
                            }

                            if ($changes != null) {

                                $mainController->sendEmail($mailer, $studentArray[$i], 2, $changes);
                            }

                            $changes = array();

                        }

                        $entityManager->flush();

                        return $this->redirectToRoute('students');
            }

            return $this->render('views/edit_student.html.twig', array('form' => $form->createView(), 'users' => $users));
        }
        else{
            $this->addFlash('result','No student or more than 5 students have been selected for edit!');
        }
        return $this->redirectToRoute('students');
    }

    /**
     * Delete the selected Student entities
     * @param Request $request
     * @param FileUploader $fileUploader - To delete the deletes students' image
     * @param MainController $mainController - To reach the sendEmail method in MainController
     * @param MailerInterface $mailer - Interface to send the email
     * @return Response
     */
    public function deleteStudents(Request $request, FileUploader $fileUploader, MainController $mainController, MailerInterface $mailer): Response{

        $idArray = $request->get("id");
        $entityManager = $this->getDoctrine()->getManager();

        if($idArray != null) {
            foreach ($idArray as $id) {
                $student = $this->getDoctrine()->getRepository(Student::class)->find($id);

                $imageFile = $student->getStudentImageFilename();
                $fileUploader->deleteFile('../public/images/studentspics/',$imageFile);

                $mainController->sendEmail($mailer, $student, 3, null);

                $entityManager->remove($student);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('students');
    }

    /**
     * Create a new Student entity
     * @param Request $request
     * @param FileUploader $fileUploader - To edit the new students' image
     * @param MainController $mainController - To reach the sendEmail method in MainController
     * @param MailerInterface $mailer - Interface to send the email
     * @return Response
     */
    public function newStudent(Request $request, FileUploader $fileUploader, MainController $mainController, MailerInterface $mailer): Response{

        $student = new Student();
        $changes = array();

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $form = $this->createForm(StudentType::class, $student, array('option' => 1));

        $form->handleRequest($request);

        $sGroups = $form['study_groups']->getData();

        if(count($sGroups) > 4){

            return $this->redirectToRoute('new_student');
        }
        elseif($form->isSubmitted() && $form->isValid()){
            $student = $form->getData();

            $uploadedImage = $form['imageFile']->getData();

            $newFilename = $fileUploader->upload($uploadedImage);

            $student->setStudentImageFilename($newFilename);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            foreach ($student->getStudyGroups() as $id){

                $study_group = $this->getDoctrine()->getRepository(StudyGroup::class)->find($id);
                array_push($changes, "You has been joined to the ".$study_group->getGroupname()." study group!");
            }

            $mainController->sendEmail($mailer, $student, 1, $changes);

            return $this->redirectToRoute('students');
        }

        return $this->render('views/new_student.html.twig', array('form' => $form->createView(), 'users' => $users));
    }

    /**
     * Search by name among Student entities
     * @param Request $request
     * @param PaginatorInterface $paginator - Interface for paginate the Student entities
     * @param StudentRepository $studentRepository - Custom repository for the Student entities
     * @return Response
     */
    public function name_search(Request $request, PaginatorInterface $paginator, StudentRepository $studentRepository) : Response{

        $allStudents = $this->getDoctrine()->getRepository(Student::class)->findBY(array(),array('name' => 'ASC'));
        $study_groups = $this->getDoctrine()->getRepository(StudyGroup::class)->findAll();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $student_in_groups = 0;

        foreach ($study_groups as $group){

            $student_in_groups += count($group->getEnrolledStudents());
        }

        if($request->isMethod("POST")){

            $nameSearch = $request->get("nameSearch");

            if($nameSearch == null){

                return $this->redirectToRoute('students');
            }

            $students = $studentRepository->findStudentByName($nameSearch);
        }
        else{

            $students = $allStudents;
        }

        $students = $paginator->paginate($students, $request->query->getInt('page',1),2);
        return $this->render('views/students.html.twig', array('students' => $students, 'study_groups' => $study_groups, 'users' => $users, 'allStudents' => $allStudents, 'student_in_groups' => $student_in_groups));
    }

    /**
     * Filter by selected study groups among Student entities
     * @param Request $request
     * @param PaginatorInterface $paginator - Interface for paginate the Student entities
     * @param StudentRepository $studentRepository - Custom repository for the Student entities
     * @return Response
     */
    public function group_filter(Request $request, PaginatorInterface $paginator, StudentRepository $studentRepository): Response{

        $allStudents = $this->getDoctrine()->getRepository(Student::class)->findBY(array(),array('name' => 'ASC'));
        $study_groups = $this->getDoctrine()->getRepository(StudyGroup::class)->findBY(array(),array('group_name' => 'ASC'));
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $student_in_groups = 0;

        if($request->isMethod("POST")){

            $checkSearch = $request->get("checkid");

            if($checkSearch == null){

                return $this->redirectToRoute('students');
            }

            $students = $studentRepository->filterStudents($checkSearch);
        }
        else{

            $students = $allStudents;
        }

        foreach ($study_groups as $group){

            $student_in_groups += count($group->getEnrolledStudents());
        }

        $students = $paginator->paginate($students, $request->query->getInt('page',1),5);

        return $this->render('views/students.html.twig', array( 'students' => $students ,'study_groups' => $study_groups, 'users' => $users, 'allStudents' => $students, 'student_in_groups' => $student_in_groups));
    }

}