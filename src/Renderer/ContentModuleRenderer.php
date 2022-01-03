<?php

namespace Contented\TwigIntegration\Renderer;

use Contented\ContentModule\ContentModuleInterface;
use Contented\ContentModule\Renderer\ContentModuleRendererInterface;
use Contented\Exception\ContentModuleNotFoundException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

if (class_exists('Twig\Environment')) {
    class ContentModuleRenderer implements ContentModuleRendererInterface
    {
        public const TEMPLATE_FORMATS = [
            'content_modules/%s.html.twig',
            'content_modules/%s.twig',
        ];
    
        public function __construct(protected Environment $environment)
        {
        }

        public function eligible(ContentModuleInterface $contentModule, array $config): bool
        {
            foreach (self::TEMPLATE_FORMATS as $templateFormat) {
                if ($this->environment->getLoader()->exists(sprintf($templateFormat, $contentModule->getTag()))) {
                    return true;
                }
            }

            return false;
        }

        public function render(ContentModuleInterface $contentModule, array $config): string
        {
            $previousException = null;
            try {
                foreach (self::TEMPLATE_FORMATS as $templateFormat) {
                    if ($this->environment->getLoader()->exists(sprintf($templateFormat, $contentModule->getTag()))) {
                        return $this->environment->render(sprintf($templateFormat, $contentModule->getTag()), $config);
                    }
                }
            } catch (LoaderError|RuntimeError|SyntaxError $exception) {
                $previousException = $exception;
            }

            // todo rename exception (content module is found, but rendering failed)
            throw new ContentModuleNotFoundException($contentModule->getTag(), $previousException);
        }
    }
}