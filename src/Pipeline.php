<?php

declare(strict_types=1);

namespace SimpleMehanizm\Pipeline;

use SimpleMehanizm\Pipeline\Exceptions\InvalidStageException;

class Pipeline
{
    protected object $state;
    protected array $stages;
    protected array $stageCollection;
    protected array $errors;
    protected string $viaMethod = 'handle';
    protected array $performance = [];

    public function send(object $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function through(array $stages, string $viaMethod = 'handle'): self
    {
        $this->stages = $stages;
        $this->viaMethod = $viaMethod;

        return $this;
    }

    public function then(callable $callback): self
    {
        if(empty($this->stages)) return $callback($this->state);

        $this->instantiateStages();

        $this->ensureNoStageErrors();

        foreach($this->stageCollection as $stage)
        {
            $this->state = $stage->{$this->viaMethod}($this->state);
        }

        return $callback($this->state);
    }

    public function getPerformance(): array
    {
        return $this->performance;
    }

    protected function instantiateStages(): void
    {
        foreach($this->stages as $stage_blueprint)
        {
            $stage = new $stage_blueprint;

            match(method_exists($stage, $this->viaMethod))
            {
                true => $this->stageCollection[] = new $stage_blueprint,
                false => $this->errors[] = sprintf('Stage [%s] does not contain method [%s] to handle the state.', $stage, $this->viaMethod)
            };
        }
    }

    protected function ensureNoStageErrors(): true
    {
        if(empty($this->errors)) return true;

        throw new InvalidStageException('Invalid stage classes provided', $this->errors);
    }

    protected function performance(string $stage, callable $callable): void
    {
        $start = microtime(true);

        $callable($stage);

        $total = microtime(true) - $start;

        $this->performance[] = [
            'stage' => $stage,
            'time' => sprintf("%s ms", $total)
        ];
    }
}