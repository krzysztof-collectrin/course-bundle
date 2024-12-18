<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Infrastructure\Symfony\Controller;

use CourseBundle\Recommendation\Application\Request\TeacherRequestDTO;
use CourseBundle\Recommendation\Application\Service\QuoteProcessGenerator;
use CourseBundle\Recommendation\Domain\Exception\InvalidTeacherRequestException;
use CourseBundle\Recommendation\Infrastructure\Provider\ProviderConfigLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    public function __construct(
        private readonly QuoteProcessGenerator $quoteProcessGenerator,
        private readonly ProviderConfigLoader $loader,
    ) {
    }

    #[Route('/api/quotes', name: 'quote_generate', methods: ['POST'])]
    public function generateQuotes(
        #[MapRequestPayload] TeacherRequestDTO $teacherRequestDTO,
    ): JsonResponse {
        try {
            $quotes = $this->quoteProcessGenerator
                ->generateQuotes(
                    $teacherRequestDTO->getTopics(),
                    $this->loader->load(),
            );
        } catch (InvalidTeacherRequestException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Something went wrong.'], 500);
        }

        return $this->json($quotes);
    }
}
