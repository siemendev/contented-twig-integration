<?php

namespace Contented\TwigIntegration\Loader;

use Contented\ContentLoader\ContentLoaderInterface;
use Contented\ContentModule\ContentModuleInterface;
use Contented\ContentModule\GenericContentModule;
use Contented\ContentPage\ContentPageInterface;
use Contented\ContentPage\GenericContentPage;
use Contented\Exception\ContentModuleNotFoundException;
use Contented\Exception\ContentPageNotFoundException;
use Contented\TwigIntegration\Renderer\ContentModuleRenderer;
use Contented\TwigIntegration\Renderer\ContentPageRenderer;

class ContentLoader implements ContentLoaderInterface
{
    public function __construct(
        private ContentPageRenderer   $pageRenderer,
        private ContentModuleRenderer $moduleRenderer,
    ){
    }

    public function getContentPage(array $config): ContentPageInterface
    {
        $contentPage = new GenericContentPage();
        $contentPage->layout = $config['layout'];

        if ($this->pageRenderer->eligible($contentPage, $config)) {
            foreach ($config['areas'] as $area => $content) {
                foreach ($content as $module) {
                    $contentPage->addContentModule($area, $this->getContentModule($module), $module);
                }
            }

            return $contentPage;
        }

        throw new ContentPageNotFoundException($config['layout']);
    }

    public function getContentModule(array $config): ContentModuleInterface
    {
        $contentModule = new GenericContentModule();
        $contentModule->tag = $config['type'];

        if ($this->moduleRenderer->eligible($contentModule, $config)) {
            return $contentModule;
        }

        throw new ContentModuleNotFoundException($config['type']);
    }
}