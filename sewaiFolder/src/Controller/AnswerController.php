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

class AnswerController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    #[Route('/answer', name: 'aiAnswer', methods: ['GET', 'POST'])]
    public function getFeedback(Request $request)
    {
        if ($request->isMethod('POST')) {
            try {
                $answer = $request->get('answer');
                if (!$answer) {
                    return new JsonResponse(['error' => 'No answer provided'], 400);
                }

                $payload = [
                    'model' => 'Llama 3 8B Instruct',
                    'messages' => [
                        ['role' => 'user', 'content' => $answer]
                    ],
                    'max_tokens' => 50,
                    'temperature' => 0.28
                ];

                $response = $this->httpClient->request('POST', 'http://localhost:4891/v1/chat/completions', [
                    'json' => $payload,
                ]);

                $statusCode = $response->getStatusCode();
                $content = $response->toArray();

                return new JsonResponse($content, $statusCode);

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
