<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 3/10/18
 * Time: 11:49 PM
 */

namespace Smk\ShareCart\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Setup\Exception;
use Smk\ShareCart\Helper\Email;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    CONST XML_PATH_SALES_EMAIL_EMAIL = 'trans_email/ident_sales/email';
    CONST XML_PATH_SALES_EMAIL_NAME = 'trans_email/ident_sales/name';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var Email Helper
     */
    protected $helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        ScopeConfigInterface $storeConfig,
        Email $helper
    )
    {
        $this->_scopeConfig = $storeConfig;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|
     * \Magento\Framework\Controller\ResultInterface|
     * \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        //$validFormKey = $this->formKeyValidator->validate($this->getRequest());

        //if ($validFormKey && $this->getRequest()->isPost()) {
        if ($this->getRequest()->isPost()) {

            /* Assign values for your template variables  */
            $emailTemplateVariables = $this->getRequest()->getPostValue();

            /**
             *  Receiver Detail
             */
            $receiverInfo = [
                'name' => $emailTemplateVariables['name'],
                'email' => $emailTemplateVariables['email']
            ];

            /* Sender Detail  */

            $senderInfo = [
                'name' => $this->getConfigValue(self::XML_PATH_SALES_EMAIL_NAME, $this->getStore()->getStoreId()),
                'email' => $this->getConfigValue(self::XML_PATH_SALES_EMAIL_EMAIL, $this->getStore()->getStoreId())
            ];

            try {
                $this->helper->mailSend(
                    $emailTemplateVariables,
                    $senderInfo,
                    $receiverInfo
                );
                $message = '<br />
                        <div role="alert" class="messages">
                            <div class="message-success success message" data-ui-id="message-success">
                                <div data-bind="html: message.text"><b>Success!</b> Email has been sent.</div>
                            </div>
                        </div>';

                echo $message; exit;
            } catch (\Exception $e) {
                $error_msg =   '<br />
                                <div role="alert" class="messages">
                                    <div class="message-warning warning message" data-ui-id="message-warning">
                                        <div data-bind="html: message.text"><b>Error!</b> Email not sent.</div>
                                    </div>
                                </div>';
                echo $error_msg; exit;
            }

        }
    }
}
