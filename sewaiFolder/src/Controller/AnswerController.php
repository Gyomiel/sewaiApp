<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\QuestionRepository;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AnswerController extends AbstractController
{
    private $httpClient;
    private $questionRepository;

    public function __construct(HttpClientInterface $httpClient, QuestionRepository $questionRepository) {
        $this->httpClient = $httpClient;
        $this->questionRepository = $questionRepository;
    }

    #[Route('/answer', name: 'aiAnswer', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getFeedback(Request $request)
    {

        if ($request->isMethod('POST')) {
            try {
                $answer = $request->get('answer');
                $questionId = $request->get('question_id');
                $question = $this->questionRepository->find($questionId);
                $courseId = $request->get('course_id');

                if (!$question) {
                    return new JsonResponse(['message' => 'Question not found'], 404);
                }

                if (!$answer) {
                    return new JsonResponse(['error' => 'No answer provided'], 400);
                }

                $prompt = "You are an expert in mental health issues. This is the question: '{$question->getTitle()}' and this is the answer: '{$answer}'. Focus on offering empathetic tips and feedback about the emotions and themes expressed. Do not ask any questions, and ensure the response is supportive and free of judgment. Avoid referencing the question or answer directly. Don't try to make lists or make words bolder or in cursive font. Just plain text. Keep the response concise, within 200-250 characters.";

                $payload = [
                    'model' => 'llama3.2:1b',
                    'prompt' => $prompt,
                    'stream' => false
                ];

                $response = $this->httpClient->request('POST', 'http://localhost:11434/api/generate', [
                    'json' => $payload,
                ]);

                $statusCode = $response->getStatusCode();
                $content = $response->toArray();
                return $this->render('answer/index.html.twig', ['response'=> $content['response']]);

            } catch (ClientExceptionInterface | ServerExceptionInterface $httpException) {
                return new JsonResponse([
                    'error' => 'Error from external API',
                    'details' => $httpException->getMessage()
                ], $httpException->getCode() ?: 500);

            } catch (TransportExceptionInterface $transportException) {
                return new JsonResponse([
                    'error' => 'Error communicating with external API',
                    'details' => $transportException->getMessage()
                ], 500);

            } catch (\Exception $exception) {
                return new JsonResponse(['error' => $exception->getMessage()], 500);
            }
        }

        return $this->render('answer/index.html.twig');
    }
}
