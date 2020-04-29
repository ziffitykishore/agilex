<?php
namespace SomethingDigital\BrandCategoryCreation\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\VisualMerchandiser\Model\Rules;


class CreateBrandCategory extends Command
{

  protected $categoryFactory;
  protected $categoryRepository;
  protected $eavConfig;
  protected $rules;

  public function __construct(
    CategoryFactory $categoryFactory,
    CategoryRepositoryInterface $categoryRepository,
    Config $eavConfig,
    Rules $rules
) {
    $this->categoryFactory = $categoryFactory;
    $this->categoryRepository = $categoryRepository;
    $this->eavConfig = $eavConfig;
    $this->rules = $rules;
    parent::__construct(null);
}
   protected function configure()
   {
       $this->setName('sd:create-brand-categories');
       $this->setDescription('Generate brand categories');
       
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
              $category = $this->categoryFactory->create();
              $category->setName($option['label']);
              $category->setParentId(8879);
              $category->setIsActive(true);
              $categoryObj = $this->categoryRepository->save($category);

              $ruleArray = [[
                  'attribute' => 'brand_123',
                  'operator' => 'eq',
                  'value' => $option['label'],
                  'logic' => 'OR'
              ]];

              $rule = $this->rules->loadByCategory($categoryObj);
              $rule->setData([
                  'rule_id' => $rule->getId(),
                  'category_id' => $categoryObj->getId(),
                  'is_active' => '1',
                  'conditions_serialized' => json_encode($ruleArray)
              ]);
              $rule->save();

              $this->categoryRepository->save($categoryObj);
              $output->writeln('Created: '.$option['label']);
          } catch (\Exception $e) {
              $output->writeln('Can not create: '.$option['label']);
              continue;
          }
      }
   }
}