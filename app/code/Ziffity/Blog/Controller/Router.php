<?php
namespace Ziffity\Blog\Controller;

use \Magefan\Blog\Model\Url;

/**
 * Blog Controller Router
 */
class Router extends \Magefan\Blog\Controller\Router
{

    /**
     * Validate and Match Blog Pages and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $_identifier = trim($request->getPathInfo(), '/');
        $_identifier = urldecode($_identifier);

        $pathInfo = explode('/', $_identifier);
        $blogRoute = $this->_url->getRoute();

        if ($pathInfo[0] != $blogRoute) {
            return;
        }
        unset($pathInfo[0]);

        if (!count($pathInfo)) {
            $request
                ->setRouteName('blog')
                ->setControllerName('index')
                ->setActionName('index');
        } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_RSS)) {
            $request
                ->setRouteName('blog')
                ->setControllerName(Url::CONTROLLER_RSS)
                ->setActionName(isset($pathInfo[2]) ? $pathInfo[2] : 'index');
        } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_SEARCH)
            && !empty($pathInfo[2])
        ) {
            $request
                ->setRouteName('blog')
                ->setControllerName(Url::CONTROLLER_SEARCH)
                ->setActionName('index')
                ->setParam('q', $pathInfo[2]);
        } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_AUTHOR)
            && !empty($pathInfo[2])
            && ($authorId = $this->_getAuthorId($pathInfo[2]))
        ) {
            $request
                ->setRouteName('blog')
                ->setControllerName(Url::CONTROLLER_AUTHOR)
                ->setActionName('view')
                ->setParam('id', $authorId);
        } elseif ($pathInfo[1] == $this->_url->getRoute(Url::CONTROLLER_TAG)
            && !empty($pathInfo[2])
            && $tagId = $this->_getTagId($pathInfo[2])
        ) {
            $request
                ->setRouteName('blog')
                ->setControllerName(Url::CONTROLLER_TAG)
                ->setActionName('view')
                ->setParam('id', $tagId);
        } else {
            $controllerName = null;
            /*seo purpose updated the urlpath*/
            if (Url::PERMALINK_TYPE_DEFAULT == $this->_url->getPermalinkType()) {
                if(isset($pathInfo[1]) && $pathInfo[1] == 'archive'){
                    $controllerName = $this->_url->getControllerName($pathInfo[1]);
                    unset($pathInfo[1]);
                }
            }

            if(isset($pathInfo[1]) && $pathInfo[1] == 'category'){
                $controllerName = $this->_url->getControllerName($pathInfo[1]);
                unset($pathInfo[1]);
            }

            $pathInfo = array_values($pathInfo);
            $pathInfoCount = count($pathInfo);

            if ($pathInfoCount == 1) {
                if ((!$controllerName || $controllerName == Url::CONTROLLER_ARCHIVE)
                    && $this->_isArchiveIdentifier($pathInfo[0])
                ) {
                    $request
                        ->setRouteName('blog')
                        ->setControllerName(Url::CONTROLLER_ARCHIVE)
                        ->setActionName('view')
                        ->setParam('date', $pathInfo[0]);
                } elseif ((!$controllerName || $controllerName == Url::CONTROLLER_POST)
                    && $postId = $this->_getPostId($pathInfo[0])
                ) {
                    $request
                        ->setRouteName('blog')
                        ->setControllerName(Url::CONTROLLER_POST)
                        ->setActionName('view')
                        ->setParam('id', $postId);
                } elseif ((!$controllerName || $controllerName == Url::CONTROLLER_CATEGORY)
                    && $categoryId = $this->_getCategoryId($pathInfo[0])
                ) {
                    $request
                        ->setRouteName('blog')
                        ->setControllerName(Url::CONTROLLER_CATEGORY)
                        ->setActionName('view')
                        ->setParam('id', $categoryId);
                }
            } elseif ($pathInfoCount > 1) {
                $postId = 0;
                $categoryId = 0;
                $first = true;
                $pathExist = true;

                for ($i = $pathInfoCount - 1; $i >= 0; $i--) {
                    if ((!$controllerName || $controllerName == Url::CONTROLLER_POST)
                        && $first
                        && ($postId = $this->_getPostId($pathInfo[$i]))
                    ) {
                        //we have postId
                    } elseif ((!$controllerName || !$first || $controllerName == Url::CONTROLLER_CATEGORY)
                        && ($cid = $this->_getCategoryId($pathInfo[$i], $first))
                    ) {
                        if (!$categoryId) {
                            $categoryId = $cid;
                        }
                    } else {
                        $pathExist = false;
                        break;
                    }

                    if ($first) {
                        $first = false;
                    }
                }


                if ($pathExist) {
                    if ($postId) {
                        $request
                            ->setRouteName('blog')
                            ->setControllerName(Url::CONTROLLER_POST)
                            ->setActionName('view')
                            ->setParam('id', $postId);
                        if ($categoryId) {
                            $request->setParam('category_id', $categoryId);
                        }
                    } elseif ($categoryId) {
                        $request
                            ->setRouteName('blog')
                            ->setControllerName(Url::CONTROLLER_CATEGORY)
                            ->setActionName('view')
                            ->setParam('id', $categoryId);
                    }
                } elseif ((!$controllerName || $controllerName == Url::CONTROLLER_POST)
                    && $postId = $this->_getPostId(implode('/', $pathInfo))
                ) {
                    $request
                        ->setRouteName('blog')
                        ->setControllerName(Url::CONTROLLER_POST)
                        ->setActionName('view')
                        ->setParam('id', $postId);
                }
            }
        }

        $condition = new \Magento\Framework\DataObject(
            [
                'identifier' => $_identifier,
                'request' => $request,
                'continue' => true
            ]
        );
        $this->_eventManager->dispatch(
            'magefan_blog_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );

        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Redirect',
                ['request' => $request]
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        if (!$request->getModuleName()) {
            return null;
        }

        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $_identifier);

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }

}
