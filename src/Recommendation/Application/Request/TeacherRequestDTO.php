<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Application\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class TeacherRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\Count(min: 1)]
        #[Assert\All([
            new Assert\Type('integer'),
            new Assert\Positive(),
        ])]
        private array $topics,
    ) {
    }

    public function getTopics(): array
    {
        return $this->topics;
    }
}
