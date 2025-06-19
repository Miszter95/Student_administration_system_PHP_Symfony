<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\UserType;

use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * Edit the User entity
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param $id - Given user ID
     * @return Response
     */
    public function userEditSettings(Request $request, FileUploader $fileUploader, $id): Response{

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $form = $this->createForm(UserType::class, $user, array('option' => 2));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $uploadedImage = $form['imageFile']->getData();

            if($uploadedImage != null) {

                $imageFile = $user->getStudentImageFilename();
                $fileUploader->deleteFile('../public/images/studentspics/',$imageFile);

                $newFilename = $fileUploader->upload($uploadedImage);

                $user->setImageFilename($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('students');
        }

        return $this->render('views/user_settings.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new User entity
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function newUser(Request $request, FileUploader $fileUploader): Response{

        $user = new User();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $form = $this->createForm(UserType::class, $user, array('option' => 1));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $uploadedImage = $form['imageFile']->getData();

            $newFilename = $fileUploader->upload($uploadedImage);

            $user->setImageFilename($newFilename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('students');
        }

        return $this->render('views/new_user.html.twig', array('form' => $form->createView(), 'users' => $users));
    }

    /**
     * Delete all User entities
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function delete_users(FileUploader $fileUploader): Response{

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $entityManager = $this->getDoctrine()->getManager();
        foreach ($users as $user){

            $imageFile = $user->getImageFilename();

            $fileUploader->deleteFile('../public/images/studentspics/',$imageFile);

            $this->addFlash('result',$imageFile);

            $entityManager->remove($user);
        }
        $entityManager->flush();

        return $this->redirectToRoute('new_user');
    }

}