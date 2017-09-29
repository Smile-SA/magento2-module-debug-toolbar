<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface as MagentoLayout;
use Magento\Framework\View\Layout\Element;

/**
 * Helper: Layout
 *
 * @package   Smile\DebugToolbar\Helper
 * @copyright 2017 Smile
 */
class Layout extends AbstractHelper
{
    /**
     * @var MagentoLayout
     */
    protected $layout;

    /**
     * Layout constructor.
     * @param Context $context
     * @param MagentoLayout $layout
     */
    public function __construct(
        Context       $context,
        MagentoLayout $layout
    ) {
        parent::__construct($context);

        $this->layout = $layout;
    }

    /**
     * build the layout
     *
     * @param string $parentNode
     *
     * @return array
     */
    protected function buildLayout($parentNode)
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
                    'name'      => $childName,
                    'parent'    => $parentNode,
                    'type'      => $type,
                    'cacheable' => $this->isBlockCacheable($childName),
                    'cache_ttl' => '',
                    'cache_key' => '',
                    'scope'     => '',
                    'classname' => '',
                    'filename'  => '',
                    'template'  => '',
                ];

                /** @var AbstractBlock $block */
                $block = $this->layout->getBlock($childName);

                if (is_object($block)) {
                    $reflectionClass = new \ReflectionClass($block);
                    $layout[$childName]['cache_key'] = $block->getCacheKey();
                    $layout[$childName]['scope']     = $block->isScopePrivate();
                    $layout[$childName]['classname'] = $this->cleanClassname(get_class($block));
                    $layout[$childName]['filename']  = $this->cleanFilename($reflectionClass->getFileName());
                    $layout[$childName]['template']  = $this->cleanFilename($block->getTemplateFile());
                }

                $layout[$childName]['children'] = $this->buildLayout($childName);
                $layout[$childName]['nb_child'] = count($layout[$childName]['children']);
            }
        }

        return $layout;
    }

    /**
     * Check if a template can be cached.
     *
     * @param string $blockName
     *
     * @return bool
     */
    protected function isBlockCacheable($blockName)
    {
        /** @var Element[] $element */
        $element = $this->layout->getXPath('//' . Element::TYPE_BLOCK . '[@name="' . $blockName . '"]');

        $cacheable = empty($element) ? null : $element[0]->getAttribute('cacheable');
        return is_null($cacheable) ? true : $cacheable == 'true';
    }

    /**
     * Clean a classname
     *
     * @param string $classname
     *
     * @return string
     */
    protected function cleanClassname($classname)
    {
        return preg_replace('/\\\\Interceptor$/', '', $classname);
    }

    /**
     * Clean a filename
     *
     * @param string $filename
     *
     * @return string
     */
    protected function cleanFilename($filename)
    {

        return str_replace(BP.'/', '', $filename);
    }

    /**
     * get the event stats
     *
     * @return array
     */
    public function getLayoutBuild()
    {
        return $this->buildLayout('root');
    }

    /**
     * Get updated handles
     *
     * @return array
     */
    public function getHandles()
    {
        return $this->layout->getUpdate()->getHandles();
    }
}
