<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\Crawler\Parser;

use Innmind\Crawler\{
    Parser,
    HttpResource\Attribute\Attribute,
    UrlResolver,
};
use Innmind\Xml\{
    Reader,
    Node,
};
use Innmind\Html\Element\A;
use Innmind\Http\Message\{
    Request,
    Response,
};
use Innmind\UrlTemplate\Template;
use Innmind\Url\Url;
use Innmind\Immutable\Map;

final class FindDownloadUrl implements Parser
{
    private Reader $read;
    private UrlResolver $resolve;
    private Template $template;

    public function __construct(
        Reader $read,
        UrlResolver $resolve,
        Template $template
    ) {
        $this->read = $read;
        $this->resolve = $resolve;
        $this->template = $template;
    }

    public function __invoke(
        Request $request,
        Response $response,
        Map $attributes
    ): Map {
        $document = ($this->read)($response->body());

        $url = $this->search($document);

        if (!$url) {
            return $attributes;
        }

        $url = ($this->resolve)($request, $attributes, $url);

        return ($attributes)(
            self::key(),
            new Attribute(self::key(), $url),
        );
    }

    public static function key(): string
    {
        return 'download_url';
    }

    private function search(Node $node): ?Url
    {
        if ($node instanceof A && $this->template->matches($node->href())) {
            return $node->href();
        }

        return $node->children()->reduce(
            null,
            fn(?Url $url, Node $child): ?Url => $url ?? $this->search($child),
        );
    }
}
