<?php

namespace SomethingDigital\CategoryImagesRestApi\Api;
/**
* @api
*/
interface CategoryImageInterface
{
  /**
   * Set categoryImage
   *
   * @param int $categoryId
   * @return boolean
   */
  public function setCategoryImage($categoryId);
}