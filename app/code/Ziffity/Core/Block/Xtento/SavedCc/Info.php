<?php

/**
 *  Overwrite Xtento_SavedCc 
 *  Info Block for email template changes
 */

namespace Ziffity\Core\Block\Xtento\SavedCc;

class Info extends \Xtento\SavedCc\Block\Info
{
    protected $_template = 'Ziffity_Core::savedcc/default.phtml';
}
