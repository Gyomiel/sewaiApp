<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ChatboxController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/aichat', name: 'aiChatbox', methods: ['GET', 'POST'])]
    public function chat(Request $request)
    {
        $aiResponse = null;
        $messages = $request->getSession()->get('chat_messages', []);

        if ($request->isMethod('POST')) {
            try {
                $content = $request->getContent();
                $data = json_decode($content, true);
                if (!isset($data['message']) || empty($data['message'])) {
                    return new JsonResponse(['error' => 'No message provided'], 400);
                }
                
                $userMessage = $data['message'];
                $messages[] = ['role' => 'user', 'content' => $userMessage];
                
                $payload = [
                    'model' => 'Llama 3.2 1B Instruct',
                    'messages' => array_merge(
                        [['role' => 'system', 'content' => "You are a helpful assistant."]],
                        $messages,
                        [['role' => 'user', 'content' => $userMessage]]
                    ),
                    'max_tokens' => 300,
                    'temperature' => 0.5,
                ];

                $response = $this->httpClient->request('POST', 'http://localhost:4891/v1/chat/completions', [
                    'json' => $payload,
                ]);

                $content = $response->toArray();
                
                if (!isset($content['choices'][0]['message']['content'])) {
                    return new JsonResponse([
                        'error' => 'Invalid response from external API',
                        'details' => $content
                    ], 500);
                }

                $aiResponse = $content['choices'][0]['message']['content'];
                $messages[] = ['role' => 'ai', 'content' => $aiResponse];
                $request->getSession()->set('chat_messages', $messages);
                
            } catch (ClientExceptionInterface | ServerExceptionInterface $httpException) {
                return new JsonResponse([
                    'error' => 'Error from external API',
                    'details' => $httpException->getMessage()
                ], $httpException->getCode() ?: 500);
            
            } catch (TransportExceptionInterface | \Exception $exception) {
                return new JsonResponse([
                    'error' => 'An unexpected error occurred',
                    'details' => $exception->getMessage()
                ], 500);
            }
        }

        return $this->render('chatbox/index.html.twig', [
            'messages' => $messages,
        ]);
    }
}