<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Console\Input\InputOption;

class ComposerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'composer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Commposer Command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('install')) {
            $command = sprintf(
                '%s install -o -q --ignore-platform-reqs --no-interaction --no-progress',
                $this->getComposerPath()
            );

            $this->execPhpCommand($command, [
                'MFOX_CACHE_DRIVER' => null,
                'COMPOSER_HOME'     => base_path(),
            ]);
        }

        if ($this->option('dump')) {
            $command = sprintf(
                '%s dumpautoload -q -o --ignore-platform-reqs',
                $this->getComposerPath()
            );

            $this->execPhpCommand($command, [
                'MFOX_CACHE_DRIVER' => null,
                'COMPOSER_HOME'     => base_path(),
            ]);
        }

        return 0;
    }


    /**
     * @return string|null
     */
    private function getPhpPath(): ?string
    {
        $pathToPhp = null;
        if (defined('PHP_BINDIR')) {
            $pathToPhp = sprintf('%s/php', PHP_BINDIR);
        }

        if ($pathToPhp && is_executable($pathToPhp)) {
            return $pathToPhp;
        }

        return null;
    }


    private function getComposerPath()
    {
        return base_path('composer');
    }


    /**
     * @param       $command
     * @param  array  $env
     */
    private function execPhpCommand($command, array $env = [])
    {
        $pathToPhp = $this->getPhpPath();

        $this->execCommand(sprintf('%s %s', $pathToPhp, $command), $env, false);
    }


    private function execCommand($command, $env = [], $throw = true)
    {
        $this->log(sprintf('exec command %s', $command));

        $output = [];
        $result = 0;

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes, base_path(), $env);

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $output .= stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $result = proc_close($process);
        }

        if ($result != 0 && $throw) {
            throw new RuntimeException(sprintf(
                'command: %s, result=%s; command output: %s',
                $command,
                $result,
                $output
            ));
        }

        return $result === 0;
    }

    public function log(string $message): void
    {
        $this->comment($message);
    }


    public function getOptions()
    {
        return [
            ['dump', null, InputOption::VALUE_NONE, 'Dumpautoload'],
            ['optimize', null, InputOption::VALUE_NONE, 'Run composer dumpautoload'],
            ['install', null, InputOption::VALUE_NONE, 'Run composer install'],
        ];
    }

}
