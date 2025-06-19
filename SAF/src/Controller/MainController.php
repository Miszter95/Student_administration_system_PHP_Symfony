<?php
    namespace App\Controller;

    use App\Entity\Student;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;

    use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Bridge\Twig\Mime\TemplatedEmail;

    class MainController extends AbstractController{


        /**
         * Function that displays the home page
         */
        public function index() : Response{

            return $this->render('views/index.html.twig');
        }

        /**
         * E-mail sending function
         * @param MailerInterface $mailer - Helps to send the email
         * @param Student $student - Addressed Student entity
         * @param $option - Required to separate different events in the e-mail template
         * @param $changes - Various events related to the Student entity (create, edit, etc.)
         */
        public function sendEmail(MailerInterface $mailer, Student $student, $option, $changes){

            $email = (new TemplatedEmail())
                ->from('info@saf.com')
                ->to($student->getEmailAddress())
                ->subject('Information about your profile in the Student Administration Service (SAF)')
                ->context(array('option' => $option, 'student' => $student, 'changes' => $changes))
                ->htmlTemplate('includes/email.html.twig');

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('result','Problem occurred during email sent!');
                $this->redirectToRoute('students');
            }
        }

    }