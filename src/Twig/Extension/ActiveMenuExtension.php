<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ActiveMenuExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ActiveMenuExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('activeMenu', [ActiveMenuExtensionRuntime::class, 'activeMenu']),
        ];
    }
}
