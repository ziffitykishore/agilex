<?php
namespace SomethingDigital\CategoryImagesRestApi\Model;

use Magento\Framework\Webapi\Rest\Request;
use SomethingDigital\CategoryImagesRestApi\Api\CategoryImageInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\Category;

class CategoryImage implements CategoryImageInterface
{

  protected $request;
  protected $file;
  protected $directoryList;
  protected $category;

  public function __construct(
      Request $request,
      File $file,
      DirectoryList $directoryList,
      Category $category
  ) {
      $this->request = $request;
      $this->file = $file;
      $this->directoryList = $directoryList;
      $this->category = $category;
  }

  public function setCategoryImage($categoryId) {

      try {
        $params = $this->request->getBodyParams();
        $base64_encoded_data = trim($params['content']['base64_encoded_data']);
        $imageName = trim($params['content']['name']);

        $mediaDirectory = $this->directoryList->getPath(DirectoryList::MEDIA);
        
        $this->file->filePutContents($mediaDirectory . '/catalog/category/' . $imageName, base64_decode($base64_encoded_data));

        $categoryobj = $this->category->load($categoryId);
        $categoryobj->setStoreId(0);
        $categoryobj->setImage($imageName)->save();
        return true;
      } catch (\Exception $e) {
        return false;
      }
  }
}