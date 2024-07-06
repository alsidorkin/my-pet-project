<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;

class AppExtension extends AbstractExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active', [$this, 'isActive']),
        ];
    }

    public function isActive(string $route): string
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request->get('_route') === $route ? 'active' : '';
    }
}
