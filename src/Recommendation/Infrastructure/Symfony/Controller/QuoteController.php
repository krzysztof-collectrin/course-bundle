<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Infrastructure\Symfony\Controller;

use CourseBundle\Recommendation\Application\Request\TeacherRequestDTO;
use CourseBundle\Recommendation\Application\Service\QuoteProcessGenerator;
use CourseBundle\Recommendation\Domain\Exception\InvalidTeacherRequestException;
use CourseBundle\Recommendation\Infrastructure\Provider\ProviderConfigLoader;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Stopwatch\Stopwatch;

class QuoteController extends AbstractController
{
    public function __construct(
        private readonly QuoteProcessGenerator $quoteProcessGenerator,
        private readonly ProviderConfigLoader $loader,
        private readonly Stopwatch $stopwatch,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/api/quotes', name: 'quote_generate', methods: ['POST'])]
    public function generateQuotes(
        #[MapRequestPayload] TeacherRequestDTO $teacherRequestDTO,
        Request $request,
        RateLimiterFactory $quoteGenerateLimiter,
    ): JsonResponse {
        $this->stopwatch->start('quote_generation');
        $this->denyAccessUnlessGranted('ROLE_USER');
        try {
            $this->handleRateLimit($quoteGenerateLimiter, $request);

            $quotes = $this->quoteProcessGenerator
                ->generateQuotes(
                    $teacherRequestDTO->getTopics(),
                    $this->loader->load(),
            );
        } catch (InvalidTeacherRequestException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (TooManyRequestsHttpException $e) {
            return $this->json(['error' => $e->getMessage()], 429);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Something went wrong.'], 500);
        }

        $event = $this->stopwatch->stop('quote_generation');
        $this->logger->info('Performance data', [
            'duration' => $event->getDuration(),
            'memory' => $event->getMemory(),
        ]);

        return $this->json($quotes);
    }

    private function handleRateLimit(
        RateLimiterFactory $quoteGenerateLimiter,
        Request $request,
    ): void {
        $limiter = $quoteGenerateLimiter->create($request->getClientIp());

        if (false === $limiter->consume()->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }
    }
}
