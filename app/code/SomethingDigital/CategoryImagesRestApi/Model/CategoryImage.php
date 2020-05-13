<?php
namespace SomethingDigital\CategoryImagesRestApi\Model;

use Magento\Framework\Webapi\Rest\Request;
use SomethingDigital\CategoryImagesRestApi\Api\CategoryImageInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class CategoryImage implements CategoryImageInterface
{

  protected $request;
  protected $file;
  protected $directoryList;
  protected $categoryRepository;

  public function __construct(
      Request $request,
      File $file,
      DirectoryList $directoryList,
      CategoryRepositoryInterface $categoryRepository
  ) {
      $this->request = $request;
      $this->file = $file;
      $this->directoryList = $directoryList;
      $this->categoryRepository = $categoryRepository;
  }

  public function setCategoryImage($categoryId) {

      try {
        $params = $this->request->getBodyParams();
        $base64_encoded_data = trim($params['content']['base64_encoded_data']);
        $imageName = strip_tags(trim($params['content']['name']));

        if (!preg_match("/^[a-zA-Z0-9-_.]+$/", $imageName)) {
          return "Image name must match pattern [a-zA-Z0-9-_.].";
        }

        $mediaDirectory = $this->directoryList->getPath(DirectoryList::MEDIA);
        
        $this->file->filePutContents($mediaDirectory . '/catalog/category/' . $imageName, base64_decode($base64_encoded_data));

        $categoryobj = $this->categoryRepository->get($categoryId);
        $categoryobj->setStoreId(0);
        $categoryobj->setImage($imageName);

        $this->categoryRepository->save($categoryobj);

        return true;
      } catch (\Exception $e) {
        return $e->getMessage();
      }
  }
}