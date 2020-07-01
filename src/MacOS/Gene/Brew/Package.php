<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene\Brew;

use Innmind\Genome\{
    Gene,
    History,
    Exception\PreConditionFailed,
    Exception\ExpressionFailed,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Server\Control\{
    Server,
    Server\Command,
    Server\Script,
    Exception\ScriptFailed,
};

final class Package implements Gene
{
    private string $package;

    private function __construct(string $package)
    {
        $this->package = $package;
    }

    public static function install(string $package): self
    {
        return new self($package);
    }

    public function name(): string
    {
        return "Brew {$this->package} install";
    }

    public function express(
        OperatingSystem $local,
        Server $target,
        History $history
    ): History {
        try {
            $preCondition = new Script(
                Command::foreground('which')->withArgument('brew'),
            );
            $preCondition($target);
        } catch (ScriptFailed $e) {
            throw new PreConditionFailed('brew is missing');
        }

        try {
            $install = new Script(
                Command::foreground('brew')
                    ->withArgument('install')
                    ->withArgument($this->package),
            );
            $install($target);
        } catch (ScriptFailed $e) {
            throw new ExpressionFailed($this->name());
        }

        return $history;
    }
}
