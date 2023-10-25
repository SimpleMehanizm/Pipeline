<?php

namespace SimpleMehanizm\Pipeline\Exceptions;

use Throwable;
use Exception;

class InvalidStageException extends Exception
{
    protected array $stageErrors;

    public function __construct(string $message, array $stageErrors, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->stageErrors = $stageErrors;
    }

    public function getStageErrors(): array
    {
        return $this->stageErrors;
    }
}