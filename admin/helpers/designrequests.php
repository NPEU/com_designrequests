<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

/**
 * DesignRequestsHelper component helper.
 */
class DesignRequestsHelper extends JHelperContent
{

    protected static $trello_client = null;

    /**
     * Set up the Trello client
     *
     */
    public static function getTrelloClient()
    {
        if (is_null(self::$trello_client)) {

            $token = false;
            $key = false;
            $params = clone JComponentHelper::getParams('com_designrequests');

            if (!empty($params->get('trello_token'))) {
                $token = $params->get('trello_token');
            }

            if (!empty($params->get('trello_key'))) {
                $key = $params->get('trello_key');
            }

            // we need both key and token in order to continue.
            if (!$token && !$key) {
                // @TODO - should probably throw an error here.
                return false;
            }

            $vendor = dirname(__DIR__) . '/vendor/';
            require $vendor . 'autoload.php';

            // Do we have a proxy we need to use?
            $config = JFactory::getConfig();

            if ($config->get('proxy_enable')) {
                $proxy_host   = $config->get('proxy_host');
                $proxy_port   = $config->get('proxy_port');
                $proxy_user   = $config->get('proxy_user',false);
                $proxy_pass   = $config->get('proxy_pass', false);

                $proxy_credentials = '';
                if ($proxy_user && $proxy_pass) {
                    $proxy_credentials = $proxy_user . ':' . $proxy_pass . '@';
                }

                self::$trello_client = new Stevenmaguire\Services\Trello\Client(array(
                    'key'   => $key,
                    'token' => $token,
                    'proxy' => $proxy_credentials . $proxy_host . ':' . $proxy_port
                ));
            } else {
                self::$trello_client = new Stevenmaguire\Services\Trello\Client(array(
                    'key'   => $key,
                    'token' => $token
                ));
            }
        }

        return self::$trello_client;
    }

    /**
     * Configure the Submenu. Delete if component has only one view.
     *
     * @param   string  The name of the active view.
     */
    /*public static function addSubmenu($vName = 'designrequests')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_DESIGNREQUESTS_MANAGER_SUBMENU_RECORDS'),
            'index.php?option=com_designrequests&view=designrequests',
            $vName == 'designrequests'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_DESIGNREQUESTS_MANAGER_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&view=categories&extension=com_designrequests',
            $vName == 'categories'
        );
    }*/

    /**
     * Get the actions
     */
    /*public static function getActions($itemId = 0, $model = null)
    {
        jimport('joomla.access.access');
        $user   = JFactory::getUser();
        $result = new JObject;

        if (empty($itemId)) {
            $assetName = 'comdesignrequests';
        }
        else {
            $assetName = 'com_designrequests.designrequest.'.(int) $itemId;
        }

        $actions = JAccess::getActions('com_designrequests', 'component');

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        // Check if user belongs to assigned category and permit edit if so:
        if ($model) {
            $item  = $model->getItem($itemId);

            if (!!($user->authorise('core.edit', 'com_designrequests')
            || $user->authorise('core.edit', 'com_content.category.' . $item->catid))) {
                $result->set('core.edit', true);
            }
        }

        return $result;
    }*/

}
