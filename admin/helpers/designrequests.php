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
#class DesignRequestsHelper extends JHelperContent
class DesignRequestsHelper
{
    protected static $trello_board_id;
    protected static $trello_client = null;

    public static $trello_board_data;
    public static $trello_board_lists;
    public static $trello_status_list_id_name_map;
    public static $trello_status_list_id_key_map;
    public static $trello_status_list_key_name_map;
    public static $trello_board_fields;
    public static $trello_fields_data_name_map;
    public static $trello_fields_data_key_map;
    public static $trello_fields_job_types = [];
    public static $trello_fields_print = [];
    public static $trello_fields_print_sizes = [];
    public static $trello_fields_projects = [];
    public static $trello_fields_id_values_map = [];
    public static $trello_cards = [];
    public static $trello_cards_by_due_date = [];
    public static $trello_cards_by_status = [];

    /**
     * Set up the Trello client
     *
     */
    public static function getTrelloClient()
    {
        $params = clone JComponentHelper::getParams('com_designrequests');

        if (is_null(self::$trello_client)) {

            $token  = false;
            $key    = false;


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

            $trello_board_id = $params->get('trello_board_id');

            switch ($_SERVER['SERVER_NAME']) {
                case 'dev.npeu.ox.ac.uk' :
                    $env = 'development';
                    break;
                case 'test.npeu.ox.ac.uk':
                    $env = 'testing';
                    break;
                default:
                    $env = 'production';
            }

            if ($env != 'production') {
                $trello_board_id = $params->get('trello_dev_board_id');
            }

            if (!empty($trello_board_id)) {
                #self::$trello_board_id = $params->get('trello_board_id');
                self::$trello_board_id = $trello_board_id;
                #echo '<pre>'; var_dump(self::$trello_board_id); echo '</pre>'; exit;

                self::$trello_board_data = self::$trello_client->getBoard(self::$trello_board_id);
                #echo '<pre>'; var_dump(self::$trello_board_data); echo '</pre>'; exit;

                // Lists (Status)
                self::$trello_board_lists = self::$trello_client->getBoardLists(self::$trello_board_id);
                #echo '<pre>'; var_dump(self::$trello_board_lists); echo '</pre>'; exit;

                // We have the lists, we need to make a map between the lists and the status.
                // Note that if the list names change on the Trello board, then the name needs to be
                // changed in the Config too (or visa versa), or things will break.
                foreach (self::$trello_board_lists as $list) {
                    if (in_array($list->name, $param_status_lists)) {
                        $key = array_search($list->name, $param_status_lists);
                        self::$trello_status_list_id_name_map[$list->id] = $list->name;
                        self::$trello_status_list_id_key_map[$list->id] = $key;
                        self::$trello_status_list_key_name_map[$key] = $list->name;
                    }
                }
                #echo '<pre>'; var_dump(self::$trello_status_list_id_name_map); echo '</pre>'; #exit;
                #echo '<pre>'; var_dump(self::$trello_status_list_id_key_map); echo '</pre>'; exit;

                // Fields
                self::$trello_board_fields = self::$trello_client->getBoardCustomFields(self::$trello_board_id);
                #echo '<pre>'; var_dump(self::$trello_board_fields); echo '</pre>'; exit;

                // We have the list of fields, now we need associate their ID's with the Joomla fields /
                // data key names.
                // Note that if the field names change on the Trello board, then the name needs to be
                // changed in the Config too (or visa versa), or things will break.
                foreach (self::$trello_board_fields as $field) {
                    if (in_array($field->name, $param_data_fields)) {
                        self::$trello_fields_data_name_map[$field->id] = $field->name;

                        $key_name = array_search($field->name, $param_data_fields);

                        self::$trello_fields_data_key_map[$field->id] = $key_name;

                        // Job Types:
                        if ($key_name == 'job_type') {
                            self::$trello_fields_id_values_map[$field->id] = [];
                            foreach ($field->options as $option) {
                                self::$trello_fields_job_types[$option->id] = $option->value->text;
                                self::$trello_fields_id_values_map[$field->id][$option->id] = $option->value->text;
                            }
                        }

                        // Print:
                        if ($key_name == 'print') {
                            self::$trello_fields_id_values_map[$field->id] = [];
                            foreach ($field->options as $option) {
                                self::$trello_fields_print[$option->id] = $option->value->text;
                                self::$trello_fields_id_values_map[$field->id][$option->id] = $option->value->text;
                            }
                        }

                        // Print sizes:
                        if ($key_name == 'print_size') {
                            self::$trello_fields_id_values_map[$field->id] = [];
                            foreach ($field->options as $option) {
                                self::$trello_fields_print_sizes[$option->id] = $option->value->text;
                                self::$trello_fields_id_values_map[$field->id][$option->id] = $option->value->text;
                            }
                        }

                        // Projects:
                        if ($key_name == 'project') {
                            self::$trello_fields_id_values_map[$field->id] = [];
                            foreach ($field->options as $option) {
                                self::$trello_fields_projects[$option->id] = $option->value->text;
                                self::$trello_fields_id_values_map[$field->id][$option->id] = $option->value->text;
                            }
                            asort(self::$trello_fields_projects);
                        }
                    }
                }

                #echo '<pre>'; var_dump(self::$trello_fields_data_name_map); echo '</pre>'; #exit;
                #echo '<pre>'; var_dump(self::$trello_fields_data_key_map); echo '</pre>'; #exit;
                #echo '<pre>'; var_dump(self::$trello_fields_job_types); echo '</pre>'; #exit;
                #echo '<pre>'; var_dump(self::$trello_fields_print); echo '</pre>'; #exit;
                #echo '<pre>'; var_dump(self::$trello_fields_print_sizes); echo '</pre>'; #exit;
                #echo '<pre>'; var_dump(self::$trello_fields_projects); echo '</pre>'; #exit;
                #echo '<pre>'; var_dump(self::$trello_fields_id_values_map); echo '</pre>'; #exit;

                // Cards
                #self::$trello_cards = self::$trello_client->getBoardCards(self::$trello_board_id, array('customFieldItems'=>'true'));
                $trello_cards = self::$trello_client->getBoardCards(self::$trello_board_id, array('customFieldItems'=>'true'));
                foreach ($trello_cards as $card) {

                    // Add status based on list:
                    $card->status_key = self::$trello_status_list_id_key_map[$card->idList];
                    $card->status = self::$trello_status_list_key_name_map[self::$trello_status_list_id_key_map[$card->idList]];

                    // Create a key-based custom fields array:
                    $card->customFieldItemsKey = [];

                    foreach ($card->customFieldItems as $field) {
                        if (array_key_exists($field->idCustomField, self::$trello_fields_data_key_map)) {

                            if (isset($field->idValue)) {
                                $field->realvalue = self::$trello_fields_id_values_map[$field->idCustomField][$field->idValue];
                            } else {
                                #echo '<pre>'; var_dump($field->value); echo '</pre>'; #exit;
                                $type = array_keys(get_object_vars($field->value))[0];
                                $field->realvalue = $field->value->$type;
                            }

                            $card->customFieldItemsKey[self::$trello_fields_data_key_map[$field->idCustomField]] = $field;
                        }
                    }

                    $due = $card->due;
                    self::$trello_cards_by_due_date[$due . '--' . $card->id] = $card;

                    $list = self::$trello_status_list_id_key_map[$card->idList];

                    if (!array_key_exists($list, self::$trello_cards_by_status)) {
                        self::$trello_cards_by_status[$list] = [];
                    }
                    self::$trello_cards_by_status[$list][$due . '--' . $card->id] = $card;



                    self::$trello_cards[$card->id] = $card;
                }


                #echo '<pre>'; var_dump(self::$trello_cards); echo '</pre>'; exit;

                /*foreach (self::$trello_cards as $card) {
                    // Add status based on list:
                    $card->status = self::$trello_status_list_id_key_map[$card->idList];

                    // Create a key-based custom fields array:
                    $card->customFieldItemsKey = [];

                    foreach ($card->customFieldItems as $field) {
                        if (array_key_exists($field->idCustomField, self::$trello_fields_data_key_map)) {
                            $card->customFieldItemsKey[self::$trello_fields_data_key_map[$field->idCustomField]] = $field;
                        }
                    }

                    $due = $card->due;
                    self::$trello_cards_by_due_date[$due . '--' . $card->id] = $card;

                    $list = self::$trello_status_list_id_key_map[$card->idList];

                    if (!array_key_exists($list, self::$trello_cards_by_status)) {
                        self::$trello_cards_by_status[$list] = [];
                    }
                    self::$trello_cards_by_status[$list][$due . '--' . $card->id] = $card;
                }*/
                ksort(self::$trello_cards_by_due_date);

                foreach (self::$trello_cards_by_status as $k => &$v) {
                    ksort($v);
                }

                #echo '<pre>'; var_dump(self::$trello_cards_by_due_date); echo '</pre>'; exit;
                #echo '<pre>'; var_dump(self::$trello_cards_by_status); echo '</pre>'; exit;
                #exit;
            } else {
                // @TODO - throw some error
            }
        }

        return self::$trello_client;
    }
    
    /**
     * Update a Trello card
     *
     */
    public static function trelloSaveCard($data = [])
    {
        #echo '<pre>'; var_dump($data); echo '</pre>'; #exit;
        /*$trello_data = [
            'idBoard'  => $this->trello_board_id,
            'idList'   => $this->trello_new_requests_list,
            'name'     => $data['title'],
            'desc'     => $data['info'],
            'due'      => date('c', $data['deadline'])
        ];*/
        
        $trello_data = [
            'id'      => '',
            'idBoard' => self::$trello_board_id,
            'name'    => $data['name'],
            'due'     => $data['due'],
            'desc'    => $data['desc']
        ];
        
        
        if (empty($data['id'])) {
            $trello_data['idList'] = array_search('new', self::$trello_status_list_id_key_map);
            $result = self::$trello_client->addCard($trello_data);
            $card_id = $result->id;
            $is_new = true;
        } else {
            $card_id = $data['id'];
            $trello_data['id'] = $card_id;
            $result = self::$trello_client->updateCard($card_id, $trello_data);
            
            $is_new = false;
        }
        
        
        /* Not sure if I should allow new projects to be created via the web form.
        Leave this out for now (NOTE THIS IS NOT COMPLETE - IT NEEDS TO BE ADAPTED
        if (!in_array($project_name, $this->trello_projects)) {
            #echo '<pre>'; var_dump($this->trello_projects); echo '</pre>'; exit;
            $attributes = array("value" => array("text" => $project_name));
            try {
                $add_project_result = $client->addCustomFieldOption($this->trello_projects_field_id, $attributes);
                #echo '<pre>'; var_dump($add_project_result); echo '</pre>';
                $project_option_id = $add_project_result->id;
            } catch (Exception $e) {
                #echo '<pre>'; var_dump($e->getResponseBody()); echo '</pre>';
                #echo 'Caught exception: ',  $e->getMessage(), "\n";
                #echo '<pre>'; var_dump(get_class_methods($e)); echo '</pre>';
                #echo '<pre>'; var_dump(get_class_methods($e->getTrace()[0]['args'][0])); echo '</pre>';
                echo '<pre>'; var_dump($e->getTrace()[0]['args'][0]->getMessage()); echo '</pre>';
                #echo '<pre>'; var_dump(get_class_methods($e->getResponseBody())); echo '</pre>';
                #echo '<pre>'; var_dump($e); echo '</pre>';
                exit;
            }
        } else {
            $project_option_id = array_search($project_name, $this->trello_projects);
        }
        
        $attributes = array("idValue" => $project_option_id);
        try {
            $project_result = $client->updateCardCustomField($card_id, $this->trello_projects_field_id, $attributes);
            #echo '<pre>'; var_dump($project_result); echo '</pre>';
        } catch (Exception $e) {
            echo '<pre>'; var_dump($e->getTrace()[0]['args'][0]->getMessage()); echo '</pre>';
            exit;
        }
        */
        
        $user = JFactory::getUser();
        $fields_data = [];
        
        if ($is_new) {
            // Created date:
            $created_date = date('c');
            $attributes = array("value" => array("date" => $created_date));
            $field_id = array_search('requested_on', self::$trello_fields_data_key_map);
            $fields_data[$field_id] = $attributes;
 

            // Created by:
            $created_by = $user->name . ' <' . $user->email . '>';
            $attributes = array("value" => array("text" => $created_by));
            $field_id = array_search('requested_by', self::$trello_fields_data_key_map);
            $fields_data[$field_id] = $attributes;
        }
        
        // Project:
        $project_option = array_search($data['project'], self::$trello_fields_projects);
        $attributes = array("idValue" => $project_option);
        $field_id = array_search('project', self::$trello_fields_data_key_map);
        $fields_data[$field_id] = $attributes;
        
        // Job Type:
        $job_type_option = array_search($data['job_type'], self::$trello_fields_job_types);
        $attributes = array("idValue" => $job_type_option);
        $field_id = array_search('job_type', self::$trello_fields_data_key_map);
        $fields_data[$field_id] = $attributes;
        
        // In/out house printing:
        $print_option = array_search($data['print'], self::$trello_fields_print);
        $attributes = array("idValue" => $print_option);
        $field_id = array_search('print', self::$trello_fields_data_key_map);
        $fields_data[$field_id] = $attributes;

        // Print size:
        $print_size_option = array_search($data['print_size'], self::$trello_fields_print_sizes);
        $attributes = array("idValue" => $print_size_option);
        $field_id = array_search('print_size', self::$trello_fields_data_key_map);
        $fields_data[$field_id] = $attributes;


        
        
        #echo '<pre>'; var_dump($trello_data); echo '</pre>'; #exit;
        #echo '<pre>'; var_dump($fields_data); echo '</pre>'; exit;
        
        
        foreach ($fields_data as $field_id => $attributes) {
            try {
                $result = self::$trello_client->updateCardCustomField($card_id, $field_id, $attributes);
                #echo '<pre>'; var_dump($created_result); echo '</pre>';
            } catch (Exception $e) {
                echo '<pre>'; var_dump($e->getTrace()[0]['args'][0]->getMessage()); echo '</pre>';
                exit;
            }
        }
        
        return $card_id;
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
