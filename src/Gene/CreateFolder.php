<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\Gene;

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

final class CreateFolder implements Gene
{
    private string $folder;

    public function __construct(string $folder)
    {
        $this->folder = $folder;
    }

    public function name(): string
    {
        return "Create folder {$this->folder}";
    }

    public function express(
        OperatingSystem $local,
        Server $target,
        History $history
    ): History {
        try {
            $install = new Script(
                Command::foreground('mkdir')
                    ->withShortOption('p')
                    ->withArgument($this->folder),
            );
            $install($target);
        } catch (ScriptFailed $e) {
            throw new ExpressionFailed($this->name());
        }

        return $history;
    }
}
