<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/designrequest.php';

/**
 * DesignRequests DesignRequest Form Model
 */
class DesignRequestsModelDesignRequestform extends DesignRequestsModelDesignRequest
{
    /**
     * Model typeAlias string. Used for version history.
     *
     * @var    string
     * @since  3.2
     */
    public $typeAlias = 'com_designrequests.designrequest';

    /**
     * Get the return URL.
     *
     * @return  string  The return URL.
     */
    public function getReturnPage()
    {
        return base64_encode($this->getState('return_page'));
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState()
    {
        $app = JFactory::getApplication();

        // Load state from the request.
        $pk = $app->input->getInt('id');
        $this->setState('designrequest.id', $pk);

        // Add compatibility variable for default naming conventions.
        $this->setState('form.id', $pk);

        //$categoryId = $app->input->getInt('catid');
        //$this->setState('designrequest.catid', $categoryId);

        $return = $app->input->get('return', null, 'base64');

        if (!JUri::isInternal(base64_decode($return)))
        {
            $return = null;
        }

        $this->setState('return_page', base64_decode($return));

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        $this->setState('layout', $app->input->getString('layout'));
    }
}
