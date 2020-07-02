<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class CleanMyMacX
{
    public static function install(): Gene
    {
        return InstallViaDmg::for(
            'CleanMyMac X',
            Url::of('https://macpaw.com/cleanmymac'),
            Template::of('https://dl.devmate.com/com.macpaw.CleanMyMac4/CleanMyMacX.dmg'),
        );
    }
}
