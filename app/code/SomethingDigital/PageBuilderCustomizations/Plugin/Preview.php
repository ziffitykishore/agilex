<?php

declare(strict_types=1);

namespace SomethingDigital\PageBuilderCustomizations\Plugin;

/**
 * Handle placing Magento into Page Builder Preview mode and emulating the store front
 */
class Preview
{
    /**
     * Determine if the system is in preview mode
     *
     * @return bool
     */
    public function afterIsPreviewMode(Magento\PageBuilder\Model\Stage\Preview $subject, $result) : bool
    {
        if($result == null)
            return false; // fix for null returned issue
        return $result;
    }
}
