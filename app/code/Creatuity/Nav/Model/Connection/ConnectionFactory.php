<?php

namespace Creatuity\Nav\Model\Connection;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class ConnectionFactory
{
    protected $usernameConfigPath;
    protected $passwordConfigPath;
    protected $hostConfigPath;
    protected $portConfigPath;
    protected $serverInstanceConfigPath;
    protected $clientConfigPath;
    protected $companyNameConfigPath;

    protected $scopeConfig;
    protected $encryptor;

    public function __construct(
        $usernameConfigPath,
        $passwordConfigPath,
        $hostConfigPath,
        $portConfigPath,
        $serverInstanceConfigPath,
        $clientConfigPath,
        $companyNameConfigPath,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->usernameConfigPath = $usernameConfigPath;
        $this->passwordConfigPath = $passwordConfigPath;
        $this->hostConfigPath = $hostConfigPath;
        $this->portConfigPath = $portConfigPath;
        $this->serverInstanceConfigPath = $serverInstanceConfigPath;
        $this->clientConfigPath = $clientConfigPath;
        $this->companyNameConfigPath = $companyNameConfigPath;

        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    public function create()
    {
        return new Connection(
            $this->scopeConfig->getValue($this->usernameConfigPath),
            $this->encryptor->decrypt($this->scopeConfig->getValue($this->passwordConfigPath)),
            $this->scopeConfig->getValue($this->hostConfigPath),
            $this->scopeConfig->getValue($this->portConfigPath),
            $this->scopeConfig->getValue($this->serverInstanceConfigPath),
            $this->scopeConfig->getValue($this->clientConfigPath),
            $this->scopeConfig->getValue($this->companyNameConfigPath)
        );
    }
}
