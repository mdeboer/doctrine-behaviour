<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Trait;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;

trait MockedTimeTrait
{
    protected ClockInterface $originalClock;
    protected ClockInterface $clock;

    #[Before]
    protected function mockTime(): void
    {
        $this->originalClock = Clock::get();

        $this->clock = new MockClock('now', 'Europe/Amsterdam');
        Clock::set($this->clock);
    }

    #[After]
    protected function unmockTime(): void
    {
        Clock::set($this->originalClock);
    }
}
