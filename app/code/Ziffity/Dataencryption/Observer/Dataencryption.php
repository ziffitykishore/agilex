<?php

namespace Ziffity\Dataencryption\Observer;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Dataencryption implements ObserverInterface {

    CONST CR_STATUS = 'ziffity/ziffity_dataencryption/status';
    CONST CR_PATH = 'ziffity/ziffity_dataencryption/folder_name';
    CONST XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';
    CONST DAYS_DURATION = 'ziffity/ziffity_dataencryption/days_duration';
    CONST PGP_PUBLIC_KEY = 'ziffity/pgp_configuration/upload_public_key';
    CONST PGP_RECIPIENT = 'ziffity/pgp_configuration/pgp_recipient';
    CONST ENCRYPT_FOLDER = 'ziffity/pgp_configuration/encrypt_folder';
    CONST DS = DIRECTORY_SEPARATOR;
    CONST MAIL_RECEIVER = 'ziffity/pgp_configuration/mail_receiver_email';
    CONST MAIL_RECEIVER_NAME = 'ziffity/pgp_configuration/mail_receiver_name';
    CONST MAIL_SENDER = 'ziffity/pgp_configuration/mail_sender_email';
    CONST MAIL_SENDER_NAME = 'ziffity/pgp_configuration/mail_sender_name';
    CONST MAIL_CC = 'ziffity/pgp_configuration/mail_cc';
    CONST MAIL_BCC = 'ziffity/pgp_configuration/mail_bcc';
    CONST MAIL_BODY = 'ziffity/pgp_configuration/mail_body';

    protected $date;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    protected $_messageManager;
    protected $scopeConfig;
    protected $logger;
    protected $collectionFactory;
    protected $directoryList;
    protected $modelFatory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
         \Ziffity\Dataencryption\Model\ResourceModel\Dataencryption\CollectionFactory $collectionFactory,
         \Magento\Framework\Stdlib\DateTime\DateTime $date, 
         \Magento\Store\Model\StoreManagerInterface $storeManager,
         \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
         \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
         \Magento\Framework\Message\ManagerInterface $messageManager,
         \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
         \Psr\Log\LoggerInterface $logger,
         \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
         \Ziffity\Dataencryption\Model\DataencryptionFactory $modelFatory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->_directoryList = $directoryList;
        $this->modelFatory = $modelFatory;
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    public function getConfigValue($path) {
        $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        return $this->scopeConfig->getValue(
                        $path, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function execute(Observer $observer) {
        if ($this->getConfigValue(self::CR_STATUS)) {
            $folderName = $this->getConfigValue(self::CR_PATH);
            $encFolderName = $this->getConfigValue(self::ENCRYPT_FOLDER);
            $varDir = $this->_directoryList->getPath('var');
            $baseDir = $this->_directoryList->getRoot();
            $encDir = $varDir . self::DS . $encFolderName . self::DS;
            $mailSent = 0;
            $model = $this->modelFatory->create();
            $this->logger->alert('File Encryption Cron Action!!! ');
            $files = $this->gpgEncrption($varDir, $folderName, $encDir, $encFolderName);
            foreach ($files as $file) {
                if (is_file($file)) {
                    $filesArray = $this->getModelFilename();
                    if (!in_array(basename($file), $filesArray)) {
                        $mailSent = $this->sendMail($file, $mailSent);
                        $this->saveFileDetails($model, $mailSent, $file, $encFolderName, $varDir, $folderName);
                    }
                    $this->removeEncFiles($file, $encFolderName, $varDir);
                    continue;
                }
            }
        }
    }

    public function sendMail($file, $mailSent) {
        $sender = array();
        $sender['email'] = $this->getConfigValue(self::MAIL_SENDER);
        $sender['name'] = $this->getConfigValue(self::MAIL_SENDER_NAME);
        $receiver = $this->getConfigValue(self::MAIL_RECEIVER);
        $receiver_mails = explode(',', $receiver);
        $receiverName = $this->getConfigValue(self::MAIL_RECEIVER_NAME);
        $data = array();
        $pathInfo = pathinfo($file);
        $filename = basename($file);
        $filebasename = $pathInfo['filename'];
        $data['filename'] = substr($filebasename, 0, strpos($filebasename, '.'));
        $data['content'] = $this->getConfigValue(self::MAIL_BODY);
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($data);
        $this->inlineTranslation->suspend();
        try {
            $storeScope = ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                    ->setTemplateIdentifier("ziffity_dataencryption_email_template")
                    ->setTemplateOptions(
                            [
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                            ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($sender)
                    ->addTo($receiver_mails, \Magento\Store\Model\Store::DEFAULT_STORE_ID)
                    ->addCc($this->getConfigValue(self::MAIL_CC))
                    ->addBcc($this->getConfigValue(self::MAIL_BCC))
                    ->addAttachment(file_get_contents($file), 'text/csv', \Zend_Mime::DISPOSITION_ATTACHMENT, \Zend_Mime::ENCODING_BASE64, $file)
                    ->getTransport();
            $transport->sendMessage();
            $this->logger->alert(__("Mail Sent Successfully!"));
            $mailSent = 1;
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->logger->critical($e->getMessage());
            $mailSent = 0;
        }
        return $mailSent;
    }

    public function gpgEncrption($varDir, $folderName, $encDir, $encFolderName) {
        $gpg = '/usr/bin/gpg';
        $recipient = $this->getConfigValue(self::PGP_RECIPIENT);
        $pubkey = $this->getConfigValue(self::PGP_PUBLIC_KEY);
        $pubDir = $this->_directoryList->getPath('pub');
        $pubkeyDir = $pubDir . self::DS . "media/key". self::DS. $pubkey;
        $files1 = glob($varDir . self::DS . $folderName . self::DS . '*');
        echo shell_exec("$gpg --import $pubkeyDir");
        foreach ($files1 as $ccfile) {
            if (is_file($ccfile)) {
                $this->logger->alert('File Encryption started!!! ');
                echo shell_exec("$gpg --encrypt --no-tty --yes --always-trust --recipient $recipient $ccfile");
                $gpgfile = $ccfile . ".gpg";
                if (file_exists($ccfile . ".gpg")) {
                    rename($gpgfile, $encDir . basename($gpgfile));
                    $this->logger->alert('File Encryption ended!!! ');
                }
            }
        }
        $files = glob($varDir . self::DS . $encFolderName . self::DS . '*');
        return $files;
    }

    public function saveFileDetails($model, $mailSent, $file, $encFolderName, $varDir, $folderName) {
        $data['filename'] = basename($file);
        $data['mail_sent'] = $mailSent;
        $currentDateTime = $this->date->gmtDate();
        $data['mail_sent_date'] = $currentDateTime;
        $model->setFilename($data['filename']);
        $model->setMailSent($data['mail_sent']);
        $model->setMailSentDate($data['mail_sent_date']);
        try {
            $model->save();
            $model->unsetData();
            $this->removeCcFiles($file, $mailSent, $folderName, $varDir);
        } catch (Exception $ex) {
            $this->logger('Error Capture :: ' . $ex->getMessage());
        }
    }

    public function getModelFilename() {
        $files = array();
        $collection = $this->collectionFactory->create();
        foreach ($collection as $collection) {
            $files[] = $collection->getFilename();
        }
        return $files;
    }

    public function removeCcFiles($file, $mailSent, $folderName, $varDir) {
        $files = glob($varDir . self::DS . $folderName . self::DS . '*');
        $pathInfo = pathinfo($file);
        foreach ($files as $ccfile) {
            if ((is_file($ccfile)) && ($mailSent == 1)) {
                if ($pathInfo['filename'] == basename($ccfile)) {
                    $this->logger->alert('CC file deleteAction Started :: ');
                    unlink($ccfile);
                    $this->logger->alert($ccfile);
                    $this->logger->alert('CC file deleteAction Ended:: ');
                }
            } else {
                $this->logger->alert('CC file deleteAction Failed:: ');
            }
        }
    }

    public function removeEncFiles($file, $encFolderName, $varDir) {
        $model = $this->modelFatory->create();
        $rowId = $this->getRowId($model, $file);
        if (isset($rowId)) {
            $row = $model->load($rowId[0]);
            $mailSentDate = $row->getMailSentDate();
            $mailSent = $row->getMailSent();
            $currentDate = $this->date->gmtDate();
            $dayDiff = $this->getDateDiffInDays($currentDate, $mailSentDate);
            $daysToRemove = $this->getConfigValue(self::DAYS_DURATION);
            if ((is_file($file)) && ($mailSent == 1)) {
                if ($dayDiff > $daysToRemove) {
                    $this->logger->alert('Encrypted  file deleteAction Started :: ');
                    unlink($file);
                    $this->logger->alert($file);
                    $this->logger->alert('Encrypted file deleteAction Ended:: ');
                }
            } else {
                $this->logger->alert('Encrypted file deleteAction Failed:: ');
            }
        }
    }

    public function getRowId($model, $file) {
        $filename = basename($file);
        $collections = $model->getCollection()
                ->addFieldToFilter('filename', array('eq' => $filename))
                ->addFieldToSelect('id');
        $rowIds = $collections->getColumnValues('id');
        return $rowIds;
    }

    public function getDateDiffInDays($date1, $date2) {
        $diff = abs(strtotime($date2) - strtotime($date1));
        $days = round($diff / (60 * 60 * 24));
        return $days;
    }

}
