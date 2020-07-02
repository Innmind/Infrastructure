<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class SublimeText
{
    public static function install(): Gene
    {
        return InstallViaDmg::for(
            'Sublime Text',
            Url::of('https://www.sublimetext.com'),
            Template::of('https://download.sublimetext.com/Sublime Text Build {version}.dmg'),
        );
    }
}
