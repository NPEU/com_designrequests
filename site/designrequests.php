<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

#require_once JPATH_COMPONENT . '/helpers/route.php';

// Require helper file
JLoader::register('DesignRequestsHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/designrequests.php');

// Get an instance of the controller prefixed by DesignRequests
$controller = JControllerLegacy::getInstance('DesignRequests');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();