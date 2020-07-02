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

final class Docker implements Gene
{
    private function __construct()
    {
    }

    public static function install(): self
    {
        return new self;
    }

    public function name(): string
    {
        return 'Docker install';
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
                    ->withArgument('Docker.app')
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
                    ->withShortOption('O')
                    ->withShortOption('L')
                    ->withShortOption('C')
                    ->withArgument('-')
                    ->withArgument('https://download.docker.com/mac/stable/Docker.dmg')
                    ->withWorkingDirectory(Path::of('Downloads')),
                Command::foreground('open')
                    ->withArgument('Docker.dmg')
                    ->withWorkingDirectory(Path::of('Downloads')),
            );
            $download($target);

            $this->wait($local, $target);

            $install = new Script(
                Command::foreground('cp')
                    ->withShortOption('r') // because the app is a directory
                    ->withArgument('/Volumes/Docker/Docker.app')
                    ->withArgument('/Applications/'),
                Command::foreground('diskutil')
                    ->withArgument('unmountDisk')
                    ->withArgument('/Volumes/Docker'),
                Command::foreground('rm')
                    ->withArgument('Docker.dmg')
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
                        ->withArgument('/Volumes/Docker/'),
                );
                $checkItExist($target);

                return;
            } catch (ScriptFailed $e) {
                $local->process()->halt(new Second(1));
            }
        } while (true);
    }
}
