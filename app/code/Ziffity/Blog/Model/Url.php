<?php

namespace Ziffity\Blog\Model;

/**
 * Blog url model
 */
class Url extends \Magefan\Blog\Model\Url
{

    /**
     * Retrieve blog url path
     * @param  string $identifier
     * @param  string $controllerName
     * @return string
     */
    public function getUrlPath($identifier, $controllerName)
    {
        $identifier = $this->getExpandedItentifier($identifier);
        switch ($this->getPermalinkType()) {
            case self::PERMALINK_TYPE_DEFAULT:
                /*seo purpose updated the urlpath*/
                $conNam = $this->getRoute($controllerName). '/' ;
                if($conNam == 'post_/'){
                    $conNam = '';
                }
                $path = $this->getRoute() . '/' . $conNam . $identifier . ( $identifier ? '/' : '');
                break;
            case self::PERMALINK_TYPE_SHORT:
                if ($controllerName == self::CONTROLLER_SEARCH
                    || $controllerName == self::CONTROLLER_AUTHOR
                    || $controllerName == self::CONTROLLER_TAG
                ) {
                    $path = $this->getRoute() . '/' . $this->getRoute($controllerName) . '/' . $identifier . ( $identifier ? '/' : '');
                } else {
                    $path = $this->getRoute() . '/' . $identifier . ( $identifier ? '/' : '');
                }
                break;
        }

        $path = $this->addUrlSufix($path, $controllerName);

        return $path;
    }
}
