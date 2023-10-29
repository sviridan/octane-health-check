<?php

namespace Ahtinurme;

use Laravel\Octane\RoadRunner\ServerProcessInspector as RoadRunnerServerProcessInspector;
use Laravel\Octane\Swoole\ServerProcessInspector as SwooleServerProcessInspector;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class OctaneCheck extends Check
{
    protected string $server = 'swoole';

    protected ?string $name = 'Octane';

    public function run(): Result
    {
        $result = Result::make();

        try {
            $server = $this->server ?: config('octane.server');

            $isRunning = match ($server) {
                'swoole' => $this->isSwooleServerRunning(),
                'roadrunner' => $this->isRoadRunnerServerRunning(),
                default => $this->invalidServer($result, $server),
            };
        } catch (\Exception) {
            return $result->failed('Octane does not seem to be installed correctly.');
        }

        if (! $isRunning) {
            return $result
                ->failed('Octane server is not running')
                ->shortSummary('Not running');
        }

        return $result
            ->ok()
            ->shortSummary('Octane server is running');
    }

    public function setServer(string $server): static
    {
        $this->server = $server;

        return $this;
    }

    protected function isSwooleServerRunning(): bool
    {
        return app(SwooleServerProcessInspector::class)
            ->serverIsRunning();
    }

    /**
     * Check if the RoadRunner server is running.
     */
    protected function isRoadRunnerServerRunning(): bool
    {
        return app(RoadRunnerServerProcessInspector::class)
            ->serverIsRunning();
    }

    protected function invalidServer(Result $result, string $server): Result
    {
        return $result
            ->failed('Octane server is not valid')
            ->shortSummary('Not valid');
    }
}
