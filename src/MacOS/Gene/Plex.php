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

final class Plex implements Gene
{
    private string $version;

    private function __construct(string $version)
    {
        $this->version = $version;
    }

    public static function install(string $version): self
    {
        return new self($version);
    }

    public function name(): string
    {
        return 'Plex install';
    }

    public function express(
        OperatingSystem $local,
        Server $target,
        History $history
    ): History {
        try {
            $alreadyExist = new Script(
                Command::foreground('test')
                    ->withShortOption('d')
                    ->withArgument('Plex Media Server.app')
                    ->withWorkingDirectory(Path::of('/Applications/')),
            );
            $alreadyExist($target);

            return $history;
        } catch (ScriptFailed $e) {
            // do not exist, trying to install
        }

        try {
            $preCondition = new Script(
                Command::foreground('which')->withArgument('curl'),
            );
            $preCondition($target);
        } catch (ScriptFailed $e) {
            throw new PreConditionFailed('curl is missing');
        }

        try {
            $download = new Script(
                Command::foreground('curl')
                    ->withShortOption('o')
                    ->withArgument('plex.zip')
                    ->withShortOption('L')
                    ->withShortOption('C')
                    ->withArgument('-')
                    ->withArgument("https://downloads.plex.tv/plex-media-server-new/1.19.4.2935-79e214ead/macos/PlexMediaServer-{$this->version}.zip")
                    ->withWorkingDirectory(Path::of('Downloads')),
                Command::foreground('open')
                    ->withArgument('plex.zip')
                    ->withWorkingDirectory(Path::of('Downloads')),
            );
            $download($target);

            $this->wait($local, $target);

            $install = new Script(
                Command::foreground('mv')
                    ->withArgument('Plex Media Server.app')
                    ->withArgument('/Applications/')
                    ->withWorkingDirectory(Path::of('Downloads/')),
                Command::foreground('rm')
                    ->withArgument('plex.zip')
                    ->withWorkingDirectory(Path::of('Downloads')),
            );
            $install($target);
        } catch (ScriptFailed $e) {
            throw new ExpressionFailed($this->name());
        }

        return $history;
    }

    private function wait(OperatingSystem $local, Server $target): void
    {
        do {
            try {
                $checkItExist = new Script(
                    Command::foreground('test')
                        ->withShortOption('d')
                        ->withArgument('Plex Media Server.app')
                        ->withWorkingDirectory(Path::of('Downloads/')),
                );
                $checkItExist($target);

                return;
            } catch (ScriptFailed $e) {
                $local->process()->halt(new Second(1));
            }
        } while (true);
    }
}
