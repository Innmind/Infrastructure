<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

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
use Innmind\Url\Path;
use Innmind\TimeContinuum\Earth\Period\Second;

final class Mackup implements Gene
{
    private Script $install;

    private function __construct(Script $install)
    {
        $this->install = $install;
    }

    public static function useICloud(): self
    {
        return new self(new Script(
            Command::foreground('echo')
                ->withArgument("[storage]\nengine = icloud")
                ->overwrite(Path::of('.mackup.cfg')),
        ));
    }

    public static function restore(): self
    {
        return new self(new Script(
            Command::foreground('mackup')
                ->withArgument('restore'),
        ));
    }

    public function name(): string
    {
        return 'Mackup';
    }

    public function express(
        OperatingSystem $local,
        Server $target,
        History $history
    ): History {
        try {
            $preCondition = new Script(
                Command::foreground('which')->withArgument('mackup'),
            );
            $preCondition($target);
        } catch (ScriptFailed $e) {
            throw new PreConditionFailed('mackup is missing');
        }

        try {
            ($this->install)($target);
        } catch (ScriptFailed $e) {
            throw new ExpressionFailed($this->name());
        }

        return $history;
    }
}
