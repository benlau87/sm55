<?php
namespace Magento\Catalog\Model\Attribute\Config;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Attribute\Config
 */
class Interceptor extends \Magento\Catalog\Model\Attribute\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\Attribute\Config\Data $dataStorage)
    {
        $this->___init();
        parent::__construct($dataStorage);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeNames($groupName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeNames');
        if (!$pluginInfo) {
            return parent::getAttributeNames($groupName);
        } else {
            return $this->___callPlugins('getAttributeNames', func_get_args(), $pluginInfo);
        }
    }
}
