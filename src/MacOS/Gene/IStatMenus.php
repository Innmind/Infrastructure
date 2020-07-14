<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class IStatMenus
{
    public static function install(): Gene
    {
        return InstallViaZip::for(
            'iStat Menus',
            Url::of('https://bjango.com/mac/istatmenus/'),
            Template::of('https://download.bjango.com/istatmenus/'),
        );
    }
}
