<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class MicroSnitch
{
    public static function install(): Gene
    {
        return InstallViaZip::for(
            'Micro Snitch',
            Url::of('https://www.obdev.at/products/microsnitch/download.html'),
            Template::of('https://www.obdev.at/downloads/MicroSnitch/MicroSnitch-{version}.zip'),
        );
    }
}
