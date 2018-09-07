<?php

/**
 * Product:       Xtento_XtCore (2.3.0)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-08-15T13:47:06+00:00
 * Last Modified: 2017-08-16T08:52:13+00:00
 * File:          app/code/Xtento/XtCore/Block/View/Element/Message/Renderer/NoescapeRenderer.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Block\View\Element\Message\Renderer;

use Magento\Framework\Message\MessageInterface;

class NoescapeRenderer implements \Magento\Framework\View\Element\Message\Renderer\RendererInterface
{
    /**
     * complex_renderer
     */
    const CODE = 'noescape_renderer';

    /**
     * Renders complex message, no escaping
     *
     * @param MessageInterface $message
     * @param array $initializationData
     * @return string
     */
    public function render(MessageInterface $message, array $initializationData)
    {
        return $message->getText();
    }
}
