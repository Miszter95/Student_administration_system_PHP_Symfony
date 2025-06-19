<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\User;

use App\Form\SGroupsType;
use App\Form\StudyGroupType;

use App\Repository\StudyGroupRepository;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

class StudyGroupController extends AbstractController
{
    /**
     * Function that displays the /study_groups template with data
     * @param Request $request
     * @param PaginatorInterface $paginator - Interface for paginate the Study Group entities
     * @return Response
     */
    public function studentsGroupShow(Request $request, PaginatorInterface $paginator): Response{

        $allStudy_groups = $this->getDoctrine()->getRepository(StudyGroup::class)->findBY(array(),array('group_name' => 'ASC'));
        $students = $this->getDoctrine()->getRepository(Student::class)->findBY(array(),array('name' => 'ASC'));
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $student_in_groups = 0;

        foreach ($allStudy_groups as $group){

            $student_in_groups += count($group->getEnrolledStudents());
        }

        $study_groups = $paginator->paginate($allStudy_groups, $request->query->getInt('page',1),10);

        return $this->render('views/study_groups.html.twig', array('study_groups' => $study_groups, 'allStudy_groups' => $allStudy_groups, 'students' => $students, 'users' => $users, 'student_in_groups' => $student_in_groups));
    }

    /**
     * Edit multiple selected Study Group entity
     * @param Request $request
     * @return Response
     */
    public function editSGroup(Request $request): Response{

        $idArray = $request->get("id");
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        if($idArray != null) {

            $student = new Student();

            foreach ($idArray as $id){

                $SGroup = $this->getDoctrine()->getRepository(StudyGroup::class)->find($id);
                $student->getStudyGroups()->add($SGroup);
            }

            $form = $this->createForm(SGroupsType::class, $student, array('option' => 2));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                return $this->redirectToRoute('study_groups');
            }

            return $this->render('views/edit_sgroup.html.twig', array('form' => $form->createView(), 'users' => $users));
        }
        else{
            $this->addFlash('result','No study group have been selected for edit!');
        }
        return $this->redirectToRoute('study_groups');
    }

    /**
     * Delete the selected Study Group entities
     * @param Request $request
     * @param MainController $mainController
     * @param MailerInterface $mailer - Interface to send the email
     * @return Response
     */
    public function deleteStudyGroups(Request $request, MainController $mainController, MailerInterface $mailer): Response{

        $idArray = $request->get("id");
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($idArray as $id){
            $study_group = $this->getDoctrine()->getRepository(StudyGroup::class)->find($id);

            foreach ($study_group->getEnrolledStudents() as $sid){

                $student = $this->getDoctrine()->getRepository(Student::class)->find($sid);

                $mainController->sendEmail($mailer, $student, 4 , $study_group);
            }
            $entityManager->remove($study_group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('study_groups');
    }

    /**
     * Create a new Study Group entity
     * @param Request $request
     * @return Response
     */
    public function newSGroup(Request $request): Response{

        $studyGroup = new StudyGroup();

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $form = $this->createForm(StudyGroupType::class, $studyGroup, array('option' => 1));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $studyGroup = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($studyGroup);
            $entityManager->flush();

            return $this->redirectToRoute('study_groups');
        }

        return $this->render('views/new_sgroup.html.twig', array('form' => $form->createView(), 'users' => $users));
    }

    /**
     * Search by group name among Study Group entities
     * @param Request $request
     * @param PaginatorInterface $paginator - Interface for paginate the Study Group entities
     * @param StudyGroupRepository $studyGroupRepository - Custom repository for the Study Group entities
     * @return Response
     */
    public function name_search_in_groups(Request $request, PaginatorInterface $paginator, StudyGroupRepository $studyGroupRepository) : Response{

        $allStudy_groups = $this->getDoctrine()->getRepository(StudyGroup::class)->findBY(array(),array('group_name' => 'ASC'));
        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $student_in_groups = 0;

        foreach ($allStudy_groups as $group){

            $student_in_groups += count($group->getEnrolledStudents());
        }

        if($request->isMethod("POST")){

            $nameSearch = $request->get("nameSearch");

            if($nameSearch == null){

                return $this->redirectToRoute('study_groups');
            }

            $study_groups = $studyGroupRepository->findStudyGroupByName($nameSearch);

        }
        else{

            $study_groups = $allStudy_groups;
        }

        $study_groups = $paginator->paginate($study_groups, $request->query->getInt('page',1),2);

        return $this->render('views/study_groups.html.twig', array('study_groups' => $study_groups, 'allStudy_groups' => $allStudy_groups, 'students' => $students, 'users' => $users, 'student_in_groups' => $student_in_groups));
    }

    /**
     * Filter by selected students among Study Group entities
     * @param Request $request
     * @param PaginatorInterface $paginator - Interface for paginate the Study Group entities
     * @param StudyGroupRepository $studyGroupRepository - Custom repository for the Study Group entities
     * @return Response
     */
    public function student_filter(Request $request, PaginatorInterface $paginator, StudyGroupRepository $studyGroupRepository): Response{

        $allStudy_groups = $this->getDoctrine()->getRepository(StudyGroup::class)->findBY(array(),array('group_name' => 'ASC'));
        $students = $this->getDoctrine()->getRepository(Student::class)->findBY(array(),array('name' => 'ASC'));
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $student_in_groups = 0;

        if($request->isMethod("POST")){

            $checkSearch = $request->get("checkid");

            if($checkSearch == null){

                return $this->redirectToRoute('study_groups');
            }

            $study_groups = $studyGroupRepository->filterSGroups($checkSearch);
        }
        else{

            $study_groups = $allStudy_groups;
        }

        foreach ($allStudy_groups as $group){

            $student_in_groups += count($group->getEnrolledStudents());
        }

        $study_groups = $paginator->paginate($study_groups, $request->query->getInt('page',1),5);

        return $this->render('views/study_groups.html.twig', array('study_groups' => $study_groups, 'allStudy_groups' => $study_groups, 'students' => $students, 'users' => $users, 'student_in_groups' => $student_in_groups));
    }

}