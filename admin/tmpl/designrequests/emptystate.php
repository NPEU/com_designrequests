<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
    'textPrefix' => 'COM_DESIGNREQUESTS',
    'formURL'    => 'index.php?option=com_designrequests',
];

/*
$displayData = [
    'textPrefix' => 'COM_DESIGNREQUESTS',
    'formURL'    => 'index.php?option=com_designrequests',
    'helpURL'    => '',
    'icon'       => 'icon-globe designrequests',
];
*/

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_designrequests') || count($user->getAuthorisedCategories('com_designrequests', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_designrequests&task=designrequest.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);