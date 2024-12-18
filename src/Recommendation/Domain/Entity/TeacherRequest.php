<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Domain\Entity;

class TeacherRequest
{
    public function __construct(private array $topics)
    {
        arsort($topics);
        $this->topics = array_slice($topics, 0, 3, true);
    }

    public function getTopics(): array
    {
        return $this->topics;
    }
}
