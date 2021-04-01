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
    protected $trello_board_id;
    protected $trello_client;
    protected $trello_board_data;
    protected $trello_board_lists;
    protected $trello_status_list_id_name_map;
    protected $trello_status_list_id_key_map;
    protected $trello_status_list_key_name_map;
    protected $trello_board_fields;
    protected $trello_fields_data_name_map;
    protected $trello_fields_data_key_map;
    protected $trello_fields_job_types = array();
    protected $trello_fields_print = array();
    protected $trello_fields_print_sizes = array();
    protected $trello_fields_projects = array();
    protected $trello_cards = array();
    protected $trello_cards_by_due_date = array();
    protected $trello_cards_by_status = array();

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

        $this->setup();
    }


    public function setup() {
        $trello_client = DesignRequestsHelper::getTrelloClient();
        $this->trello_client = $trello_client;

        if (!$this->trello_client) {
            // @TODO - throw some error
        }

        $params = clone JComponentHelper::getParams('com_designrequests');

        $param_status_lists = array(
            'new'               => $params['trello_listname_new'],
            'awaiting_content'  => $params['trello_listname_awaiting_content'],
            'in_progress'       => $params['trello_listname_in_progress'],
            'awaiting_feedback' => $params['trello_listname_awaiting_feedback'],
            'done'              => $params['trello_listname_done']
        );

        $param_data_fields = array(
            'project'      => $params['trello_fieldname_project'],
            'job_type'     => $params['trello_fieldname_job_type'],
            'print'        => $params['trello_fieldname_print'],
            'print_size'   => $params['trello_fieldname_print_size'],
            'requested_on' => $params['trello_fieldname_requested_on'],
            'requested_by' => $params['trello_fieldname_requested_by']
        );
        // Do I need to pop these up to the class properties?

        if (!empty($params->get('trello_board_id'))) {
            $this->trello_board_id = $params->get('trello_board_id');
            #echo '<pre>'; var_dump($this->trello_board_id); echo '</pre>'; exit;

            $this->trello_board_data = $trello_client->getBoard($this->trello_board_id);
            #echo '<pre>'; var_dump($this->trello_board_data); echo '</pre>'; exit;

            // Lists (Status)
            $this->trello_board_lists = $trello_client->getBoardLists($this->trello_board_id);
            #echo '<pre>'; var_dump($this->trello_board_lists); echo '</pre>'; exit;

            // We have the lists, we need to make a map between the lists and the status.
            // Note that if the list names change on the Trello board, then the name needs to be
            // changed in the Config too (or visa versa), or things will break.
            foreach ($this->trello_board_lists as $list) {
                if (in_array($list->name, $param_status_lists)) {
                    $key = array_search($list->name, $param_status_lists);
                    $this->trello_status_list_id_name_map[$list->id] = $list->name;
                    $this->trello_status_list_id_key_map[$list->id] = $key;
                    $this->trello_status_list_key_name_map[$key] = $list->name;
                }
            }
            #echo '<pre>'; var_dump($this->trello_status_list_id_name_map); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump($this->trello_status_list_id_key_map); echo '</pre>'; exit;

            // Fields
            $this->trello_board_fields = $trello_client->getBoardCustomFields($this->trello_board_id);
            #echo '<pre>'; var_dump($this->trello_board_fields); echo '</pre>'; exit;

            // We have the list of fields, now we need associate their ID's with the Joomla fields /
            // data key names.
            // Note that if the field names change on the Trello board, then the name needs to be
            // changed in the Config too (or visa versa), or things will break.
            foreach ($this->trello_board_fields as $field) {
                if (in_array($field->name, $param_data_fields)) {
                    $this->trello_fields_data_name_map[$field->id] = $field->name;

                    $key_name = array_search($field->name, $param_data_fields);

                    $this->trello_fields_data_key_map[$field->id] = $key_name;

                    // Job Types:
                    if ($key_name == 'job_type') {
                        foreach ($field->options as $option) {
                            $this->trello_fields_job_types[$option->id] = $option->value->text;
                        }
                    }

                    // Print:
                    if ($key_name == 'print') {
                        foreach ($field->options as $option) {
                            $this->trello_fields_print[$option->id] = $option->value->text;
                        }
                    }

                    // Print sizes:
                    if ($key_name == 'print_size') {
                        foreach ($field->options as $option) {
                            $this->trello_fields_print_sizes[$option->id] = $option->value->text;
                        }
                    }

                    // Projects:
                    if ($key_name == 'project') {
                        foreach ($field->options as $option) {
                            $this->trello_fields_projects[$option->id] = $option->value->text;
                        }
                        asort($this->trello_fields_projects);
                    }
                }
            }

            #echo '<pre>'; var_dump($this->trello_fields_data_name_map); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump($this->trello_fields_data_key_map); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump($this->trello_fields_job_types); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump($this->trello_fields_print); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump($this->trello_fields_print_sizes); echo '</pre>'; #exit;
            #echo '<pre>'; var_dump($this->trello_fields_projects); echo '</pre>'; exit;

            // Cards
            $this->trello_cards = $trello_client->getBoardCards($this->trello_board_id, array('customFieldItems'=>'true'));
            #echo '<pre>'; var_dump($this->trello_cards); echo '</pre>'; exit;

            foreach ($this->trello_cards as $card) {
                // Add status based on list:
                $card->status = $this->trello_status_list_id_key_map[$card->idList];

                // Create a key-based custom fields array:
                $card->customFieldItemsKey = array();

                foreach ($card->customFieldItems as $field) {
                    if (array_key_exists($field->idCustomField, $this->trello_fields_data_key_map)) {
                        $card->customFieldItemsKey[$this->trello_fields_data_key_map[$field->idCustomField]] = $field;
                    }
                }

                $due = $card->due;
                $this->trello_cards_by_due_date[$due . '--' . $card->id] = $card;

                $list = $this->trello_status_list_id_key_map[$card->idList];

                if (!array_key_exists($list, $this->trello_cards_by_status)) {
                    $this->trello_cards_by_status[$list] = array();
                }
                $this->trello_cards_by_status[$list][$due . '--' . $card->id] = $card;
            }
            ksort($this->trello_cards_by_due_date);

            foreach ($this->trello_cards_by_status as $k => &$v) {
                ksort($v);
            }

            #echo '<pre>'; var_dump($this->trello_cards_by_due_date); echo '</pre>'; exit;
            #echo '<pre>'; var_dump($this->trello_cards_by_status); echo '</pre>'; exit;
            #exit;
        } else {
            // @TODO - throw some error
        }
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
        return $this->trello_cards_by_due_date;
    }

    /**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 */
	public function getStatusListKeyNameMap()
	{
        return $this->trello_status_list_key_name_map;
    }

    /**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 */
	public function getFieldsJobTypes()
	{
        return $this->trello_fields_job_types;
    }

    /**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 */
	public function getFieldsPrint()
	{
        return $this->trello_fields_print;
    }

    /**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 */
	public function getFieldsPrintSizes()
	{
        return $this->trello_fields_print_sizes;
    }

    /**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 */
	public function getFieldsProjects()
	{
        return $this->trello_fields_projects;
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
