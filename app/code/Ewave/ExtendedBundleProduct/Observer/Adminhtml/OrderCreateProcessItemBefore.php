<?php
namespace Ewave\ExtendedBundleProduct\Observer\Adminhtml;

use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;

class OrderCreateProcessItemBefore implements ObserverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * OrderCreateProcessItemBefore constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MetadataPool $metadataPool
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param Observer $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Framework\App\RequestInterface $request
         */
        $request = $observer->getEvent()->getData('request_model');
        $items = $request->getPost('item', []);
        if ($productId = (int)$request->getPost('add_product')) {
            $items[$productId] = [];
        }

        if (!empty($items)) {
            $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getIdentifierField();
            $this->searchCriteriaBuilder
                ->addFilter($linkField, array_keys($items), 'in')
                ->addFilter(ProductInterface::TYPE_ID, Bundle::TYPE_CODE);

            $products = $this->productRepository->getList($this->searchCriteriaBuilder->create());
            foreach ($products->getItems() as $product) {
                /**
                 * @var Product $product
                 * @var Bundle $bundleProduct
                 */
                $bundleProduct = $product->getTypeInstance();
                $selections = $bundleProduct->getSelectionsCollection(
                    $bundleProduct->getOptionsIds($product),
                    $product
                )->addFieldToFilter(
                    ProductInterface::TYPE_ID,
                    Configurable::TYPE_CODE
                );

                if ($selections->getSize() > 0) {
                    throw new LocalizedException(__(implode(' ', [
                        'You\'re trying to add a bundle that has configurable product in its options.',
                        'This could be done only on storefront.',
                    ])));
                }
            }
        }

        return $this;
    }
}
