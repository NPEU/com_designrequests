<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

/**
 * DesignRequests List Model
 */
class DesignRequestsModelDesignRequests extends JModelList
{
    /**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelLegacy
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

        #$this->setup();
    }


    /*public function setup() {
        $trello_client = DesignRequestsHelper::$getTrelloClient();
        $this->trello_client = $trello_client;

        if (!$this->trello_client) {
            // @TODO - throw some error
        }
    }*/

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
	protected function getListQuery()
	{

    }



    //protected $published = 1;

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    /*protected function getListQuery()
    {
        // Initialize variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Create the select statement.
        $query->select('*')
              ->from($db->quoteName('#__designrequests'));

        if (is_numeric($this->published))
        {
            $query->where('published = ' . (int) $this->published);
        }
        elseif ($this->published === '')
        {
            $query->where('(published IN (0, 1))');
        }

        #$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }*/

    /**
     * Method to get an array of data items (published and unpublished).
     *
     * @return  mixed  An array of data items on success, false on failure.
     */
    /*public function getAllItems()
    {
        $this->published = '';
        return parent::getItems();
    }*/

    /**
     * Method to get an array of data items (unpublished only).
     *
     * @return  mixed  An array of data items on success, false on failure.
     */
    /*public function getUnpublishedItems()
    {
        $this->published = 0;
        return parent::getItems();
    }*/
}
