<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class Archiver
{
    public static function install(): Gene
    {
        return InstallViaZip::for(
            'Archiver',
            Url::of('https://archiverapp.com'),
            Template::of('http://incrediblebee.com/download/archiver-3'),
        );
    }
}
