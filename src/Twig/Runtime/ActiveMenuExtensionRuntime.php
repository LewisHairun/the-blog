<?php

namespace App\Twig\Runtime;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

class ActiveMenuExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function activeMenu(string $route): string
    {
        return $this->requestStack->getCurrentRequest()->get("_route") === $route ? "active" : "";
    }
}
