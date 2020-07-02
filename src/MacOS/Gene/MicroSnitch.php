<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Infrastructure\Crawler\Parser\FindDownloadUrl;
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
use Innmind\HttpTransport\{
    Transport,
    ThrowOnErrorTransport,
};
use Innmind\Crawler\{
    Crawler\Crawler,
    Parser\HtmlParser,
    Parser\SequenceParser,
    Parser\Html\BaseParser,
    Parser\Http\ContentTypeParser,
    UrlResolver,
};
use Innmind\UrlResolver\UrlResolver as BaseResolver;
use Innmind\Url\{
    Url,
    Path,
};
use Innmind\UrlTemplate\Template;
use Innmind\TimeContinuum\Earth\Period\Second;
use Innmind\Http\{
    Message\Request\Request,
    Message\Method,
    ProtocolVersion,
};
use function Innmind\Html\bootstrap as reader;

final class MicroSnitch implements Gene
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
        return "Micro Snitch install";
    }

    public function express(
        OperatingSystem $local,
        Server $target,
        History $history
    ): History {
        try {
            $url = $this->search($local->remote()->http());
        } catch (\Throwable $e) {
            throw new ExpressionFailed($this->name());
        }

        try {
            $alreadyExist = new Script(
                Command::foreground('test')
                    ->withShortOption('d')
                    ->withArgument('Micro Snitch.app')
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
                    ->withArgument('microsnitch.zip')
                    ->withShortOption('L')
                    ->withShortOption('C')
                    ->withArgument('-')
                    ->withArgument($url->toString())
                    ->withWorkingDirectory(Path::of('Downloads')),
                Command::foreground('open')
                    ->withArgument('microsnitch.zip')
                    ->withWorkingDirectory(Path::of('Downloads')),
            );
            $download($target);

            $this->wait($local, $target);

            $install = new Script(
                Command::foreground('mv')
                    ->withArgument('Micro Snitch.app')
                    ->withArgument('/Applications/')
                    ->withWorkingDirectory(Path::of('Downloads/')),
                Command::foreground('rm')
                    ->withArgument('microsnitch.zip')
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
                        ->withArgument('Micro Snitch.app')
                        ->withWorkingDirectory(Path::of('Downloads/')),
                );
                $checkItExist($target);

                return;
            } catch (ScriptFailed $e) {
                $local->process()->halt(new Second(1));
            }
        } while (true);
    }

    private function search(Transport $http): Url
    {
        $reader = reader();
        $crawl = new Crawler(
            new ThrowOnErrorTransport($http),
            new SequenceParser(
                new ContentTypeParser,
                new HtmlParser(
                    new SequenceParser(
                        new BaseParser($reader),
                        new FindDownloadUrl(
                            $reader,
                            new UrlResolver(new BaseResolver),
                            Template::of('https://www.obdev.at/downloads/MicroSnitch/MicroSnitch-{version}.zip')
                        ),
                    ),
                ),
            ),
        );

        $resource = $crawl(new Request(
            Url::of('https://www.obdev.at/products/microsnitch/download.html'),
            Method::get(),
            new ProtocolVersion(2, 0),
        ));

        if (!$resource->attributes()->contains(FindDownloadUrl::key())) {
            throw new \RuntimeException('Download url not found');
        }

        /** @var Url */
        return $resource->attributes()->get(FindDownloadUrl::key())->content();
    }
}
