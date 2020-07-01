<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\{
    Gene,
    History,
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

final class VimRC implements Gene
{
    private function __construct()
    {
    }

    public static function syntaxOn(): self
    {
        return new self;
    }

    public function name(): string
    {
        return 'Enable vim syntax highlighting';
    }

    public function express(
        OperatingSystem $local,
        Server $target,
        History $history
    ): History {
        try {
            $enable = new Script(
                Command::foreground('echo')
                    ->withArgument('syntax on')
                    ->overwrite(Path::of('.vimrc')),
            );
            $enable($target);
        } catch (ScriptFailed $e) {
            throw new ExpressionFailed($this->name());
        }

        return $history;
    }
}
