<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class Dash
{
    public static function install(): Gene
    {
        return InstallViaZip::for(
            'Dash',
            Url::of('https://kapeli.com/dash'),
            Template::of('downloads/v5/Dash.zip'),
        );
    }
}
