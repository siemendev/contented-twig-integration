<?php

namespace Contented\TwigIntegration\Renderer;

use Contented\ContentPage\ContentPageInterface;
use Contented\ContentPage\Renderer\ContentPageRendererInterface;
use Contented\Exception\ContentPageNotFoundException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

if (class_exists('Twig\Environment')) {
    class ContentPageRenderer implements ContentPageRendererInterface
    {
        public const TEMPLATE_FORMATS = [
            'content_pages/%s.html.twig',
            'content_pages/%s.twig',
        ];
    
        public function __construct(protected Environment $environment)
        {
        }

        public function eligible(ContentPageInterface $contentPage, array $config): bool
        {
            foreach (self::TEMPLATE_FORMATS as $templateFormat) {
                if ($this->environment->getLoader()->exists(sprintf($templateFormat, $contentPage->getLayout()))) {
                    return true;
                }
            }

            return false;
        }

        public function render(ContentPageInterface $contentPage, array $config, array $contentAreasHtml): string
        {
            $previousException = null;
            try {
                foreach (self::TEMPLATE_FORMATS as $templateFormat) {
                    if ($this->environment->getLoader()->exists(sprintf($templateFormat, $contentPage->getLayout()))) {
                        return $this->environment->render(
                            sprintf($templateFormat, $contentPage->getLayout()),
                            [
                                'config' => $config,
                                'content_areas' => $contentAreasHtml,
                            ]
                        );
                    }
                }
            } catch (LoaderError|RuntimeError|SyntaxError $exception) {
                $previousException = $exception;
            }

            throw new ContentPageNotFoundException($contentPage->getLayout(), $previousException);
        }
    }
}