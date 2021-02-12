<?php

namespace Travers\Feedback\Block;
use Magento\Framework\App\Config\ScopeConfigInterface;

class FeedbackForm extends \Magento\Framework\View\Element\Template
{   
    const XML_PATH_API_URL = 'feedbackformsetting/general/url';
    const XML_PATH_API_USERNAME = 'feedbackformsetting/general/username';
    const XML_PATH_API_PASSWORD = 'feedbackformsetting/general/password';
    const XML_PATH_JIRA_PROJECT_KEY = 'feedbackformsetting/jiradetails/project';
    const XML_PATH_JIRA_ISSUETYPE = 'feedbackformsetting/jiradetails/issuetype';
    const XML_PATH_JIRA_LABELS = 'feedbackformsetting/jiradetails/labellist';

     /**
    * @var ScopeConfigInterface
    */
    protected $_scopeConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    )
    {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function createIssue($postData){
        $url = $this->getJiraUrl().'3/issue/';

        $auth_username = $this->getAuthUsername();
        $auth_password = $this->getAuthPassword();

        $resultData = $this->getJsonEncodeData($postData);
        
        if ($curl = curl_init()) {
            $result = false;
            $header = array('Accept: application/json','Content-Type: application/json', "cache-control: no-cache");
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_ENCODING,"");
            curl_setopt($curl, CURLOPT_HTTP_VERSION, "CURL_HTTP_VERSION_1_1");
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $resultData);
            curl_setopt($curl, CURLOPT_USERPWD, $auth_username.":".$auth_password);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

            try {
                $response = curl_exec($curl);
                $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $err = curl_error($curl);
                curl_close($curl);

                if($err){
                    $result['message'] = $err;
                    $result['code'] = $code;
                    $this->logData($err);
                } else{
                    $result['message'] = $err;
                    $result['code'] = $code;
                }
             
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->logData($e->getMessage());
            } catch (\Exception $e) {
                $this->logData($e->getMessage());
            }
            return $result;
        }
    }

    public function logData($message = null)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/create_jira_issue.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($message, true));
    }

    /**
     * Get form action URL for POST feedback request
     *
     * @return string
     */
    public function getFormAction()
    {
      return $this->getBaseUrl().'feedback/feedbackform/index';
    }

    /**
     * Get action URL for Jira feedback request
     *
     * @return string
     */
    public function getJiraUrl()
    {   
      $url = $this->getConfig(static::XML_PATH_API_URL);
      return $url;
    }

    /**
     * Get auth username for feedback request
     *
     * @return string
     */
    public function getAuthUsername()
    {   
      $username = $this->getConfig(static::XML_PATH_API_USERNAME);
      return $username;
    }

     /**
     * Get auth password for feedback request
     *
     * @return string
     */
    public function getAuthPassword()
    {   
      $password =  $this->getConfig(static::XML_PATH_API_PASSWORD);
      return $password;
    }

    /**
     * Get Project Key for feedback request
     *
     * @return string
     */
    public function getProjectKey()
    {   
        $projectKey =  $this->getConfig(static::XML_PATH_JIRA_PROJECT_KEY);
        return $projectKey;
    }

    /**
     * Get Issue Type for feedback request
     *
     * @return string
     */
    public function getIssueType()
    {   
        $issueType =  $this->getConfig(static::XML_PATH_JIRA_ISSUETYPE);
        return $issueType;
    }

    /**
     * Get Label List for feedback request
     *
     * @return string
     */
    public function getLabelList()
    {   
        $allLabels =  $this->getConfig(static::XML_PATH_JIRA_LABELS);
        $labels = explode(',',$allLabels);
        return $labels;
    }

     /**
     * Get config value by path
     *
     * @param string $path
     * @return string
     */
    protected function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
     /**
     * Get JSON data for feedback request
     *
     * @param string $data
     * @return json
     */
    public function getJsonEncodeData($data){
                
        $resultData = [
            'fields' => [
              'summary' => $data['summary'],
              'issuetype' => [
                'id' => $this->getIssueType(),
              ],
              'project' => [
                'key' => $this->getProjectKey(),
              ],
              'labels'=> [
                $data['option']
              ],
              'description' => [
                'type' => 'doc',
                'version' => 1,
                'content' => [
                  0 => [
                    'type' => 'paragraph',
                    'content' => [
                      0 => [
                        'text' => 'Customer Name: '.$data['firstname'] ? $data['firstname'] : "-",
                        'type' => 'text',
                      ],
                    ],
                  ],
                  1 => [
                    'type' => 'paragraph',
                    'content' => [
                      0 => [
                        'text' => 'Customer Email: '.$data['emailaddress'] ? $data['emailaddress'] : '-',
                        'type' => 'text',
                      ],
                    ],
                  ],
                  2 => [
                    'type' => 'paragraph',
                    'content' => [
                      0 => [
                        'text' => $data['summary'],
                        'type' => 'text',
                      ],
                    ],
                  ],
                  3 => [
                    'type' => 'paragraph',
                    'content' => [
                      0 => [
                        'text' => $data['description'] ? $data['description'] :" ",
                        'type' => 'text',
                      ],
                    ],
                  ],
                  4 => [
                    'type' => 'paragraph',
                    'content' => [
                      0 => [
                        'text' => $data['current_url'],
                        'type' => 'text',
                      ],
                    ],
                  ],
                ],
              ],
            ],
        ];
        $result = json_encode($resultData);
        return $result;
    }
}