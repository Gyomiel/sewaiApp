<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout() {
        return $this->redirectToRoute('home');
    }

    #[Route('/dashboard', name: 'dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        CourseRepository $courseRepository,
        LessonRepository $lessonRepository
    ): Response
    {

        $courses = $courseRepository->findAll();
        $lessons = $lessonRepository->findAll();
        $user = $this->getUser();
    
        $courseId = $request->get('course_id');
        $lessonId = $request->get('lesson_id');
    
        $course = null;
        $lesson = null;
    
        if ($courseId) {
            $course = $courseRepository->find($courseId);
            $lessons = $course ? $course->getLessons() : [];
        }
    
        if ($lessonId) {
            $lesson = $lessonRepository->find($lessonId);
            $course = null;
            $courses = null;
        }
    
        return $this->render('dashboard/dashboard.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'courses' => $courses,
            'selectedCourse' => $course,
            'lessons' => $lessons,
            'selectedLesson' => $lesson,
        ]);
    }
    
}
