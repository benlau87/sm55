<?php
namespace MageBig\SocialLogin\Controller\Social\Callback;

/**
 * Interceptor class for @see \MageBig\SocialLogin\Controller\Social\Callback
 */
class Interceptor extends \MageBig\SocialLogin\Controller\Social\Callback implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Api\AccountManagementInterface $accountManager, \MageBig\SocialLogin\Helper\Social $apiHelper, \MageBig\SocialLogin\Model\Social $apiObject, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Model\Account\Redirect $accountRedirect, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory)
    {
        $this->___init();
        parent::__construct($context, $storeManager, $accountManager, $apiHelper, $apiObject, $customerSession, $accountRedirect, $resultRawFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute();
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkRequest($key, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkRequest');
        if (!$pluginInfo) {
            return parent::checkRequest($key, $value);
        } else {
            return $this->___callPlugins('checkRequest', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStore');
        if (!$pluginInfo) {
            return parent::getStore();
        } else {
            return $this->___callPlugins('getStore', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomerProcess($userProfile, $type)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createCustomerProcess');
        if (!$pluginInfo) {
            return parent::createCustomerProcess($userProfile, $type);
        } else {
            return $this->___callPlugins('createCustomerProcess', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomer($user, $type)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createCustomer');
        if (!$pluginInfo) {
            return parent::createCustomer($user, $type);
        } else {
            return $this->___callPlugins('createCustomer', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function emailRedirect($apiLabel, $needTranslate = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'emailRedirect');
        if (!$pluginInfo) {
            return parent::emailRedirect($apiLabel, $needTranslate);
        } else {
            return $this->___callPlugins('emailRedirect', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _appendJs($content = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '_appendJs');
        if (!$pluginInfo) {
            return parent::_appendJs($content);
        } else {
            return $this->___callPlugins('_appendJs', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($customer)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'refresh');
        if (!$pluginInfo) {
            return parent::refresh($customer);
        } else {
            return $this->___callPlugins('refresh', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        if (!$pluginInfo) {
            return parent::getActionFlag();
        } else {
            return $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        if (!$pluginInfo) {
            return parent::getRequest();
        } else {
            return $this->___callPlugins('getRequest', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        if (!$pluginInfo) {
            return parent::getResponse();
        } else {
            return $this->___callPlugins('getResponse', func_get_args(), $pluginInfo);
        }
    }
}
