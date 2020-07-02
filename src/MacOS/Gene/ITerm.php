<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class ITerm
{
    public static function install(): Gene
    {
        return InstallViaZip::for(
            'iTerm',
            Url::of('https://iterm2.com/downloads.html'),
            Template::of('https://iterm2.com/downloads/stable/iTerm2-{version}.zip'),
        );
    }
}
