<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Cron;

/**
 * Class Run
 * @package Wyomind\MassStockUpdate\Cron
 */
class Run
{
    /**
     * @var string
     */
    public $module="massstockupdate";
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Wyomind\MassStockUpdate\Model\ResourceModel\Profiles\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $coreDate;
    /**
     * @var \Wyomind\Core\Helper\Data
     */
    protected $coreHelper;


    /**
     * Run constructor.
     * @param \Wyomind\MassStockUpdate\Model\ResourceModel\Profiles\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Wyomind\Core\Helper\Data $coreHelper
     */
    public function __construct(
        \Wyomind\MassStockUpdate\Model\ResourceModel\Profiles\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Wyomind\Core\Helper\Data $coreHelper
    ) {
        
        $this->logger=$objectManager->create("Wyomind\\" . $this->module . "\Logger\LoggerCron");


        $this->collectionFactory=$collectionFactory;
        $this->scopeConfig=$scopeConfig;
        $this->transportBuilder=$transportBuilder;
        $this->coreDate=$coreDate;
        $this->coreHelper=$coreHelper;
    }

    /**
     * @param \Magento\Cron\Model\Schedule $schedule
     * @throws \Exception
     */
    public function run(\Magento\Cron\Model\Schedule $schedule)
    {
        try {
            $log=[];

            $this->logger->notice("-------------------- CRON PROCESS --------------------");
            $log[]="-------------------- CRON PROCESS --------------------";

            $coll=$this->collectionFactory->create();

            $cnt=0;

            foreach ($coll as $profile) {

                $done=false;
                try {

                    $this->logger->notice("--> Running profile : " . $profile->getName() . " [#" . $profile->getId() . "] <--");
                    $log[]="--> Running profile : " . $profile->getName() . " [#" . $profile->getId() . "] <--";

                    $cron=[];

                    $cron['current']['localDate']=$this->coreDate->date('l Y-m-d H:i:s');
                    $cron['current']['gmtDate']=$this->coreDate->gmtDate('l Y-m-d H:i:s');
                    $cron['current']['localTime']=$this->coreDate->timestamp();
                    $cron['current']['gmtTime']=$this->coreDate->gmtTimestamp();

                    $cron['file']['localDate']=$this->coreDate->date('l Y-m-d H:i:s', $profile->getImportedAt());
                    $cron['file']['gmtDate']=$profile->getImportedAt();
                    $cron['file']['localTime']=$this->coreDate->timestamp($profile->getImportedAt());
                    $cron['file']['gmtTime']=strtotime($profile->getImportedAt());


                    $cron['offset']=$this->coreDate->getGmtOffset("hours");

                    $log[]='   * Last update : ' . $cron['file']['gmtDate'] . " GMT / " . $cron['file']['localDate'] . ' GMT' . $cron['offset'] . "";
                    $log[]='   * Current date : ' . $cron['current']['gmtDate'] . " GMT / " . $cron['current']['localDate'] . ' GMT' . $cron['offset'] . "";
                    $this->logger->notice('   * Last update : ' . $cron['file']['gmtDate'] . " GMT / " . $cron['file']['localDate'] . ' GMT' . $cron['offset']);
                    $this->logger->notice('   * Current date : ' . $cron['current']['gmtDate'] . " GMT / " . $cron['current']['localDate'] . ' GMT' . $cron['offset']);

                    $cronExpr=json_decode($profile->getCronSettings());

                    $i=0;

                    if ($cronExpr != null && isset($cronExpr->days)) {
                        foreach ($cronExpr->days as $d) {
                            foreach ($cronExpr->hours as $h) {
                                $time=explode(':', $h);
                                if (date('l', $cron['current']['gmtTime']) == $d) {
                                    $cron['tasks'][$i]['localTime']=strtotime($this->coreDate->date('Y-m-d')) + ($time[0] * 60 * 60) + ($time[1] * 60);
                                    $cron['tasks'][$i]['localDate']=date('l Y-m-d H:i:s', $cron['tasks'][$i]['localTime']);
                                } else {
                                    $cron['tasks'][$i]['localTime']=strtotime("last " . $d, $cron['current']['localTime']) + ($time[0] * 60 * 60) + ($time[1] * 60);
                                    $cron['tasks'][$i]['localDate']=date('l Y-m-d H:i:s', $cron['tasks'][$i]['localTime']);
                                }

                                if ($cron['tasks'][$i]['localTime'] >= $cron['file']['localTime'] && $cron['tasks'][$i]['localTime'] <= $cron['current']['localTime'] && $done != true) {
                                    $this->logger->notice('   * Scheduled : ' . ($cron['tasks'][$i]['localDate'] . " GMT" . $cron['offset']));
                                    $log[]='   * Scheduled : ' . ($cron['tasks'][$i]['localDate'] . " GMT" . $cron['offset']) . "";
                                    $this->logger->notice("   * Starting generation");

                                    $result=$profile->multipleImport();
                                    if ($result) {
                                        $done=true;

                                        $log[]="   * EXECUTED!";
                                        $log[]=__('The profile %1 [ID:%2] has been processed.', $profile->getName(), $profile->getId());
                                        if (count($result["success"]) > 0) {
                                            $log[]=__('%1', $result["success"]);
                                        }
                                        if (count($result["notice"]) > 0) {
                                            $log[]=__('%1', $result["notice"]);
                                        }
                                        if (count($result["warning"]) > 0) {
                                            $log[]=__('%1', $result["warning"]);
                                        }

                                    }
                                    $cnt++;
                                    break 2;
                                }

                                $i++;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $cnt++;
                    $this->logger->notice("   * ERROR! " . ($e->getMessage()));
                    $log[]="   * ERROR! " . ($e->getMessage()) . "";
                }
                if (!$done) {
                    $this->logger->notice("   * SKIPPED!");
                    $log[]="   * SKIPPED!";
                }
            }


            if ($this->coreHelper->getStoreConfig($this->module . "/settings/enable_reporting")) {


                $emails=explode(',', $this->coreHelper->getStoreConfig($this->module . "/settings/emails"));

                if (count($emails) > 0) {
                    try {
                        if ($cnt) {
                            $template="wyomind_massstockupdate_cron_report";

                            $transport=$this->transportBuilder
                                ->setTemplateIdentifier($template)
                                ->setTemplateOptions(
                                    [
                                        'area'=>\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                                        'store'=>\Magento\Store\Model\Store::DEFAULT_STORE_ID
                                    ]
                                )
                                ->setTemplateVars(
                                    [
                                        'report'=>implode("<br/>", $log),
                                        'subject'=>$this->coreHelper->getStoreConfig($this->module . '/settings/report_title')
                                    ]
                                )
                                ->setFrom(
                                    [
                                        'email'=>$this->coreHelper->getStoreConfig($this->module . '/settings/sender_email'),
                                        'name'=>$this->coreHelper->getStoreConfig($this->module . '/settings/sender_name')
                                    ]
                                )
                                ->addTo($emails[0]);

                            $count=count($emails);
                            for ($i=1; $i < $count; $i++) {
                                $transport->addCc($emails[$i]);
                            }

                            $transport->getTransport()->sendMessage();
                        }
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->logger->notice('   * EMAIL ERROR! ' . $e->getMessage());
                        throw new \Magento\Framework\Exception\LocalizedException(__("Error: %s", $e->getMessage()));
                    }
                }
            }
        } catch (\Exception $e) {

            $schedule->setStatus('failed');
            $schedule->setMessage($e->getMessage());
            $schedule->save();
            $this->logger->notice("CRITICAL ERROR ! ");
            $this->logger->notice($e->getMessage());
        }
    }
}