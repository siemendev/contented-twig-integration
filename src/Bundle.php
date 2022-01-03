<?php
namespace Contented\TwigIntegration;

use Contented\TwigIntegration\DependencyInjection\ContentedTwigIntegrationExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

class Bundle extends BaseBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new ContentedTwigIntegrationExtension();
        }

        return $this->extension;
    }
}