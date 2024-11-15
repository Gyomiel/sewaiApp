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

class AnswerController extends AbstractController
{
    private $httpClient;
    private $questionRepository;

    public function __construct(HttpClientInterface $httpClient, QuestionRepository $questionRepository) {
        $this->httpClient = $httpClient;
        $this->questionRepository = $questionRepository;
    }

    #[Route('/answer', name: 'aiAnswer', methods: ['GET', 'POST'])]
    public function getFeedback(Request $request)
    {
        if ($request->isMethod('POST')) {
            try {
                $answer = $request->get('answer');
                $questionId = $request->get('question_id');
                $question = $this->questionRepository->find($questionId);

                if (!$question) {
                    return new JsonResponse(['message' => 'Question not found'], 404);
                }

                if (!$answer) {
                    return new JsonResponse(['error' => 'No answer provided'], 400);
                }

                $prompt = "You are an expert in mental health issues. This is the question: '{$question->getTitle()}' and this is the answer: '{$answer}'. I don't want you to ask any questions, just give tips and feedback about the users' feelings and answers to these questions. Don't make them feel judged ever. Don't mention the question either. No more than 300 tokens.";

                $payload = [
                    'model' => 'Llama 3 8B Instruct',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.28
                ];

                $response = $this->httpClient->request('POST', 'http://localhost:4891/v1/chat/completions', [
                    'json' => $payload,
                ]);

                $statusCode = $response->getStatusCode();
                $content = $response->toArray();
                return $this->render('answer/index.html.twig', ['response'=> $content['choices'][0]['message']['content']]);

               // return new JsonResponse($content, $statusCode);

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
