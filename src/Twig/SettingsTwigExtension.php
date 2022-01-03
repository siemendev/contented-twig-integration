<?php

namespace Contented\TwigIntegration\Twig;

use Contented\Settings\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsTwigExtension extends AbstractExtension
{
    public function __construct(private SettingsManager $manager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('contented_setting', [$this->manager, 'resolve']),
        ];
    }
}