<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class Paw
{
    public static function install(): Gene
    {
        return InstallViaZip::for(
            'Paw',
            Url::of('https://paw.cloud'),
            Template::of('/download'),
        );
    }
}
