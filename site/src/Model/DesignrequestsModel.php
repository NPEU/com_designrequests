<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Site\Model;

use NPEU\Component\Designrequests\Administrator\Helper\DesignrequestsHelper;

defined('_JEXEC') or die;

/**
 * Designrequest Component Model
 */
class DesignrequestsModel extends \Joomla\CMS\MVC\Model\ListModel {

    public function __construct() {
        DesignRequestsHelper::getTrelloClient();
    }

    /**
     * Method to cache the last query constructed.
     *
     * This method ensures that the query is constructed only once for a given state of the model.
     *
     * @return  \JDatabaseQuery  A \JDatabaseQuery object
     *
     * @since   1.6
     */
    protected function _getListQuery()
    {
        return false;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   1.6
     */
    public function getItems()
    {
        #echo 'here'; exit;
        return DesignRequestsHelper::$trello_cards_by_due_date;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     */
    public function getStatusListKeyNameMap()
    {
        return DesignRequestsHelper::$trello_status_list_key_name_map;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     */
    public function getFieldsJobTypes()
    {
        return DesignRequestsHelper::$trello_fields_job_types;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     */
    public function getFieldsPrint()
    {
        return DesignRequestsHelper::$trello_fields_print;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     */
    public function getFieldsPrintSizes()
    {
        return DesignRequestsHelper::$trello_fields_print_sizes;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     */
    public function getFieldsProjects()
    {
        return DesignRequestsHelper::$trello_fields_projects;
    }



    /**
     * Method to get a \JDatabaseQuery object for retrieving the data set from a database.
     *
     * @return  \JDatabaseQuery  A \JDatabaseQuery object to retrieve the data set.
     *
     * @since   1.6
     */
    /*protected function getListQuery()
    {

    }*/

}