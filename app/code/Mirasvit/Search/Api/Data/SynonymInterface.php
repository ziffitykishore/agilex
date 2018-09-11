<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.78
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Api\Data;

interface SynonymInterface
{
    const TABLE_NAME = 'mst_search_synonym';

    const ID       = 'synonym_id';
    const TERM     = 'term';
    const SYNONYMS = 'synonyms';
    const STORE_ID = 'store_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTerm();

    /**
     * @param string $input
     * @return $this
     */
    public function setTerm($input);

    /**
     * @return string
     */
    public function getSynonyms();

    /**
     * @param string $input
     * @return $this
     */
    public function setSynonyms($input);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $input
     * @return $this
     */
    public function setStoreId($input);
}
