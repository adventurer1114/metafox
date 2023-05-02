<?php

namespace MetaFox\Platform\HealthCheck;

use DateTime;

class Result
{

    public DateTime $startedAt;

    public string $serverity = 'error';

    private array $reports = [];

    public function __construct()
    {
        $this->startedAt = now();
    }

    /**
     * @return array
     */
    public function getReports(): array
    {
        return $this->reports;
    }

    /**
     * @param  string  $msg
     */
    public function success(string $msg):void
    {
        $this->reports[] = ['severity' => 'success', 'message' => $msg];
    }

    /**
     * @param  string  $msg
     */
    public function error(string $msg):void
    {
        $this->reports[] = ['severity' => 'error', 'message' => $msg];

    }

    /**
     * @param  string  $msg
     */
    public function warn(string $msg): void
    {
        $this->reports[] = ['severity' => 'warn', 'message' => $msg];
    }


    /**
     * @param  string  $msg
     */
    public function debug(string $msg): void
    {
        $this->reports[] = ['severity' => 'debug', 'message' => $msg];
    }

    public function okay(): bool
    {
        foreach ($this->reports as $msg) {
            if ($msg['severity'] == 'error') {
                return false;
            }
        }

        return true;
    }
}