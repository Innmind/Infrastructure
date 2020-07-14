<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class TablePlus
{
    public static function install(): Gene
    {
        return InstallViaDmg::for(
            'TablePlus',
            Url::of('https://tableplus.com/download'),
            Template::of('/release/osx/tableplus_latest'),
        );
    }
}
