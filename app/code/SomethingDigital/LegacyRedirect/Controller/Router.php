<?php

namespace SomethingDigital\LegacyRedirect\Controller;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ResponseFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ActionFactory;

class Router implements \Magento\Framework\App\RouterInterface
{
    const HTTP_CODE_301 = 301;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        ResponseFactory $responseFactory,
        ProductRepository $productRepository,
        CategoryFactory $categoryFactory,
        ResponseInterface $response,
        ActionFactory $actionFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->productRepository = $productRepository;
        $this->categoryFactory = $categoryFactory;
        $this->response = $response;
        $this->actionFactory = $actionFactory;
    }

    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $uri = $this->getRedirectUrl($request->getRequestUri(), $request);

        if ($uri) {
            $this->response->setRedirect($uri, self::HTTP_CODE_301);
            $request->setDispatched(true);
            return $this->actionFactory->create(Redirect::class);
        }
        return null;
    }

    protected function getRedirectUrl($uri, $request)
    {
        $path = trim($request->getPathInfo(), '/');
        $parts = explode("/", $path);

        return $this->handleGenericUrl($parts);
    }

    protected function handleGenericUrl($parts)
    {
        if ($parts[0] == 'c') {
            return $this->redirectToCategoryByLegacyId($parts[1]);
        } elseif ($parts[0] == 'p') {
            if (preg_match('/^[0-9]*$/' , $parts[1])) {
                return $this->redirectToCategoryByLegacyItemGroups($parts[1]);
            } else {
                return $this->redirectByProductSku($parts[1]);
            }
        }
        return false;
    }

    /**
     * Redirects to pdp by sku
     * @param $sku
     */
    protected function redirectByProductSku($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
            if ($product->getId()) {
                return $product->getProductUrl();
            }
            return false;
        } catch (NoSuchEntityException $e) {
            // That's okay.
            return false;
        }
    }

    /**
     * Redirects to category by legacy id
     * @param $urlKey
     */
    protected function redirectToCategoryByLegacyId($id)
    {
        $categoryCollection = $this->categoryFactory->create()->getCollection()
            ->addAttributeToFilter('legacy_id', $id);
        $category = $categoryCollection->getFirstItem();
        if ($category->getId()) {
            return $category->getUrl();
        }
        return false;
    }

    /**
     * Redirects to category by legacy item group id
     * @param $urlKey
     */
    protected function redirectToCategoryByLegacyItemGroups($id)
    {
        $categoryCollection = $this->categoryFactory->create()->getCollection()
            ->addAttributeToFilter('legacy_item_groups', ['finset' => [$id]]);

        $category = $categoryCollection->getFirstItem();
        if ($category->getId()) {
            return $category->getUrl();
        }
        return false;
    }
    
}