<?php

namespace SomethingDigital\BrandCategoryCreation\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\CategoryListInterface;
use Magento\Eav\Model\Config;

class ReSaveBrandCategory extends Command
{

    protected $categoryRepository;
    protected $categoryListRepository;
    protected $eavConfig;
    protected $filterGroup;
    protected $filterBuilder;
    protected $searchCriteria;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryListInterface $categoryListRepository,
        Config $eavConfig,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryListRepository = $categoryListRepository;
        $this->eavConfig = $eavConfig;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteria = $criteria;
        parent::__construct(null);
    }
    protected function configure()
    {
        $this->setName('sd:re-save-brand-categories');
        $this->setDescription('Resave brand categories');
       
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'brand_123');
        $options = $attribute->getSource()->getAllOptions();

        foreach ($options as $key => $option) {
            if (trim($option['label']) == '') {
                continue;
            }

            try {
                $this->filterGroup->setFilters([
                    $this->filterBuilder
                        ->setField(\Magento\Catalog\Model\Category::KEY_NAME)
                        ->setConditionType('like')
                        ->setValue($option['label'])
                        ->create()
                ]);

                $this->searchCriteria->setFilterGroups([$this->filterGroup]);
                $categories = $this->categoryListRepository->getList($this->searchCriteria);
                if ($categories) {
                    $catItems = $categories->getItems();
                    foreach ($catItems as $category) {
                        $this->categoryRepository->save($category);
                        $output->writeln('Re-saved: ' . $category->getName());
                    }
                }
            } catch (\Exception $e) {
                $output->writeln('Can not resave: '. $e->getMessage());
                continue;
            }
        }
    }
}
