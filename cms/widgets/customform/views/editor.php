<?php

/**
 * This class is the "Plain Text" CMS editor view
 *
 * @package     Nails
 * @subpackage  module-cms
 * @category    Widget
 * @author      Nails Dev Team
 * @link
 */

echo '<textarea name="body">';
    echo isset($body) ? $body : '';
echo '</textarea>';
