<?php

declare(strict_types=1);

namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Layout as MagentoLayout;
use Magento\Framework\View\Layout\Element;
use ReflectionClass;
use ReflectionException;
use Smile\DebugToolbar\Layout\Builder;

/**
 * Layout helper.
 */
class Layout extends AbstractHelper
{
    protected MagentoLayout $layout;
    protected DirectoryList $directoryList;

    public function __construct(
        Context $context,
        MagentoLayout $layout,
        Builder $builder,
        DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->layout = $layout;
        $this->layout->setBuilder($builder);
        $this->directoryList = $directoryList;
    }

    /**
     * Build the layout.
     *
     * @throws ReflectionException
     */
    protected function buildLayout(string $parentNode): array
    {
        $layout = [];

        $childNames = $this->layout->getChildNames($parentNode);
        if ($parentNode === 'root') {
            $parentNode = '';
        }
        if (count($childNames)) {
            foreach ($childNames as $childName) {
                $type = 'container';
                if ($this->layout->isBlock($childName)) {
                    $type = 'block';
                }

                $layout[$childName] = [
                    'name' => $childName,
                    'parent' => $parentNode,
                    'type' => $type,
                    'cacheable' => $this->isBlockCacheable($childName),
                    'cache_ttl' => '',
                    'scope' => '',
                    'classname' => '',
                    'filename' => '',
                    'template' => '',
                ];

                /** @var AbstractBlock $block */
                $block = $this->layout->getBlock($childName);

                if (is_object($block)) {
                    $template = (string) $block->getTemplate();
                    $templateFile = $template !== ''
                        ? $this->cleanFilename((string) $block->getTemplateFile($template))
                        : '';

                    $reflectionClass = new ReflectionClass($block);
                    $layout[$childName]['scope'] = $block->isScopePrivate();
                    $layout[$childName]['classname'] = $this->cleanClassname(get_class($block));
                    $layout[$childName]['filename'] = $this->cleanFilename($reflectionClass->getFileName());
                    $layout[$childName]['template'] = $templateFile;
                }

                $layout[$childName]['children'] = $this->buildLayout($childName);
                $layout[$childName]['nb_child'] = count($layout[$childName]['children']);
            }
        }

        return $layout;
    }

    /**
     * Check if a template can be cached.
     */
    protected function isBlockCacheable(string $blockName): bool
    {
        /** @var Element[] $element */
        $element = $this->layout->getXPath('//' . Element::TYPE_BLOCK . '[@name="' . $blockName . '"]');

        $cacheable = empty($element) ? null : $element[0]->getAttribute('cacheable');
        return $cacheable === null || $cacheable === 'true';
    }

    /**
     * Clean a classname.
     */
    protected function cleanClassname(string $classname): string
    {
        return preg_replace('/\\\\Interceptor$/', '', $classname);
    }

    /**
     * Clean a filename.
     */
    protected function cleanFilename(string $filename): string
    {
        return str_replace($this->directoryList->getRoot() . '/', '', $filename);
    }

    /**
     * Get the event stats.
     *
     * @throws ReflectionException
     */
    public function getLayoutBuild(): array
    {
        return $this->buildLayout('root');
    }

    /**
     * Get updated handles.
     */
    public function getHandles(): array
    {
        return $this->layout->getUpdate()->getHandles();
    }
}
