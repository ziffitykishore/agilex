<?php

namespace Creatuity\Nav\Model\Connection;

class Connection
{
    protected $username;
    protected $password;
    protected $host;
    protected $port;
    protected $serverInstance;
    protected $client;
    protected $companyName;

    public function __construct(
        $username,
        $password,
        $host,
        $port,
        $serverInstance,
        $client,
        $companyName
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->serverInstance = $serverInstance;
        $this->client = $client;
        $this->companyName = $companyName;

        $this->validate();
    }

    public function getWsdlBaseUri()
    {
        return implode('/', [
            "http://{$this->host}:{$this->port}",
            $this->serverInstance,
            $this->client,
            rawurlencode($this->companyName),
            'Page'
        ]);
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    protected function validate()
    {
        $invalidFields = $this->getInvalidFields([
            'Username'        => $this->username,
            'Password'        => $this->password,
            'Host'            => $this->host,
            'Port'            => $this->port,
            'Server Instance' => $this->serverInstance,
            'Client'          => $this->client,
            'Company Name'    => $this->companyName,
        ]);

        if (!empty($invalidFields)) {
            $data = var_export($invalidFields, true);
            throw new \Exception("Failed to create NAV connection using data from the following invalid fields:\n{$data}\n");
        }
    }

    protected function getInvalidFields(array $fields)
    {
        $invalidFields = [];
        foreach ($fields as $name => $value) {
            if ($this->isFieldValueInvalid($value)) {
                $invalidFields[$name] = $value;
            }
        }

        return $invalidFields;
    }

    protected function isFieldValueInvalid($field)
    {
        return $field === null || trim($field) == '';
    }
}
