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
use Psr\Log\LoggerInterface;

class ChatboxController extends AbstractController
{
    private $httpClient;
    private $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    #[Route('/aichat', name: 'aiChatbox', methods: ['GET', 'POST'])]
    public function chat(Request $request)
    {
        $aiResponse = null;
        $messages = $request->getSession()->get('chat_messages', []);

        $this->logger->info('Current Session Messages: ' . json_encode($messages));

        if ($request->isMethod('POST')) {
            try {
                $content = $request->getContent();
                $data = json_decode($content, true);

                if (!$request->get('message')) {
                    return new JsonResponse(['error' => 'No message provided'], 400);
                }

                $userMessage = $request->get('message');
                $messages[] = ['role' => 'user', 'content' => $userMessage];

                $payload = [
                    'model' => 'llama3.2',
                    'prompt' => `Please act as a therapist who listens attentively and offers thoughtful advice to the user. Be empathetic, non-judgmental, and supportive. Respond to the user's message with care and understanding. Do not make any list or make words on bold and cursive font. Keep your response within 200 characters. If the user mentions any self-harm thought or idea, mention the phone number 024; it's a Suicide Prevention Hotline.` . $userMessage,
                    'stream' => false
                ];

                $this->logger->info('Sending Payload to AI API: ' . json_encode($payload));

                $response = $this->httpClient->request('POST', 'http://localhost:11434/api/generate/', [
                    'json' => $payload,
                ]);

                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    $errorDetails = $response->getContent(false); // Get raw error content
                    $this->logger->error('API Error - Status Code: ' . $statusCode . ' - ' . $errorDetails);

                    return new JsonResponse([
                        'error' => 'Error from external API',
                        'status_code' => $statusCode,
                        'details' => $errorDetails
                    ], $statusCode);
                }

                $content = $response->toArray();

                $this->logger->info('AI Response: ' . json_encode($content));

                if (!isset($content['response']) || empty($content['response']) || !isset($content['response'])) {
                    $this->logger->error('Invalid response from external API', [
                        'response' => $content
                    ]);

                    return new JsonResponse([
                        'error' => 'Invalid response from external API',
                        'details' => $content
                    ], 500);
                }

                $aiResponse = $content['response'];
                $messages[] = ['role' => 'ai', 'content' => $aiResponse];

                $request->getSession()->set('chat_messages', $messages);

            } catch (ClientExceptionInterface | ServerExceptionInterface $httpException) {
                $this->logger->error('API HTTP error: ' . $httpException->getMessage());
                return new JsonResponse([
                    'error' => 'Error from external API',
                    'details' => $httpException->getMessage()
                ], $httpException->getCode() ?: 500);

            } catch (TransportExceptionInterface | \Exception $exception) {
                $this->logger->error('An unexpected error occurred: ' . $exception->getMessage());
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
