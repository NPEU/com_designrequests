<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

// Force-load the Admin language file to avoid repeating form language strings:
// (this model is used in the front-end too, and the Admin lang isn't auto-loaded there.)
/*$lang = JFactory::getLanguage();
$extension = 'com_designrequests';
$base_dir = JPATH_COMPONENT_ADMINISTRATOR;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);*/

// Require helper file
JLoader::register('DesignRequestsHelper', JPATH_COMPONENT . '/helpers/designrequests.php');

if (!JFactory::getUser()->authorise('core.manage', 'com_designrequests'))
{
    throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller = JControllerLegacy::getInstance('DesignRequests');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
