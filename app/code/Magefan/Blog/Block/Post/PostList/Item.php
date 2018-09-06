<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Block\Post\PostList;

/**
 * Post list item
 */
class Item extends \Magefan\Blog\Block\Post\AbstractPost
{
    public function wordLimiter($string, $limit = 20)
    {
        $str = strip_tags($string);
        if (stripos($str, " ")) {
            $str_s = '';
            $ex_str = explode(" ", $str);
            if (count($ex_str) > $limit) {
                for ($i = 0; $i < $limit; $i++) {
                    $str_s.=$ex_str[$i] . " ";
                }
                return $str_s;
            } else {
                return $str;
            }
        } else {
            return $str;
        }
    }
}
