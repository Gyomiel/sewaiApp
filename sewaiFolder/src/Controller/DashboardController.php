<?php
namespace App\Controller;

use App\Repository\LessonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard/lesson/{id}', name: 'get_lesson_content', methods: ['GET'])]
    public function getLessonContent(int $id, LessonRepository $lessonRepository): Response
    {
        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            return new Response('Lesson not found', 404);
        }

        return $this->render('dashboard/_lesson_content.html.twig', [
            'lesson' => $lesson,
        ]);
    }
}
