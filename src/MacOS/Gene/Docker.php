<?php
declare(strict_types = 1);

namespace Innmind\Infrastructure\MacOS\Gene;

use Innmind\Genome\Gene;
use Innmind\Url\Url;
use Innmind\UrlTemplate\Template;

final class Docker
{
    public static function install(): Gene
    {
        return InstallViaDmg::for(
            'Docker',
            Url::of('https://www.docker.com/products/docker-desktop'),
            Template::of('https://download.docker.com/mac/stable/Docker.dmg'),
        );
    }
}
