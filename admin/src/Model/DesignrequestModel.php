<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Administrator\Model;

defined('_JEXEC') or die;


#use Joomla\CMS\Form\Form;
#use Joomla\CMS\Helper\TagsHelper;
#use Joomla\CMS\Language\Associations;
#use Joomla\CMS\Language\LanguageHelper;
#use Joomla\CMS\UCM\UCMType;
#use Joomla\CMS\Versioning\VersionableModelTrait;
#use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
#use Joomla\Registry\Registry;
#use Joomla\String\StringHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

use NPEU\Component\Designrequests\Administrator\Helper\DesignrequestsHelper;

/**
 * Designrequest Model
 */
class DesignrequestModel extends AdminModel
{
    protected $trello_client;

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
    }

    /*
        The controller DesignRequestsControllerDesignRequest extends JControllerForm to handle
        form processing, but that controller uses the model AND a table to 'checkin' content, so
        here we're having to spoof the getTable method to fool that controller.
        Ugh! Consider this all 'tainted'. I guess using an API as a model in Joomla just isn't
        possible to to neatly, without replicating Joomla code :-(
    */
    public function getTable($name = '', $prefix = 'Table', $options = array())
    {
        $table = new class {
            public $spoof = false;

            public function getColumnAlias($name) {
                return 'spoof';
            }

            public function getKeyName() {
                return 'id';
            }
        };

        return $table;
    }

    /*
        The FormController needs the 'id' in the $validData array, but I can't figure out how its'
        meant to appear there (probably to do with state which baffles me).
        So I'm forcing it to be in the array to avoid the notice:
    */
    public function validate($form, $data, $group = null)
    {
        // FormController messes up the id by casting it to an (int), so check for that and get
        // the real id from the input:
        if (is_int($data['id'])) {
            $input = Factory::getApplication()->input;
            $data['id'] = $input->get('id');
        }

        $return = parent::validate($form, $data, $group);
        if (is_array($return) && empty($return['id'])) {
            $return['id'] = 0;
        }

        return $return;
    }

    /**
     * Method for getting the form from the model.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  \JForm|boolean  A \JForm object on success, false on failure
     *
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm(
            'com_designrequests.designrequest',
            'designrequest',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  array  The default data is an empty array.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        if ($this->is_in_list_view) {
            return false;
        }

        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_designrequests.edit.designrequest.data',
            array()
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        if ($data) {
            // We need to add the custom field values as properties to the main object so the form can
            // be populated with those values.
            foreach ($data->customFieldItemsKey as $key => $value) {
                $data->$key = $value->realvalue;
            }
        }

        return $data;
    }







    /* Stub out the methods from AdminModel. */


    /**
     * Method to perform batch operations on an item or a set of items.
     *
     * @param   array  $commands  An array of commands to perform.
     * @param   array  $pks       An array of item ids.
     * @param   array  $contexts  An array of item contexts.
     *
     * @return  boolean  Returns true on success, false on failure.
     *
     * @since   1.7
     */
    public function batch($commands, $pks, $contexts)
    {
        // We don't use this.
        return false;
    }

    /**
     * Batch access level changes for a group of rows.
     *
     * @param   integer  $value     The new value matching an Asset Group ID.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     *
     * @since   1.7
     */
    protected function batchAccess($value, $pks, $contexts)
    {
        // This method uses a table so we can't use the parent, but wouldn't anyway.
        return false;
    }

    /**
     * Batch copy items to a new category or current.
     *
     * @param   integer  $value     The new category.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  array|boolean  An array of new IDs on success, boolean false on failure.
     *
     * @since   1.7
     */
    protected function batchCopy($value, $pks, $contexts)
    {
        // This method uses a table so we can't use the parent, but wouldn't anyway.
        return false;
    }

    /**
     * Batch language changes for a group of rows.
     *
     * @param   string  $value     The new value matching a language.
     * @param   array   $pks       An array of row IDs.
     * @param   array   $contexts  An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     *
     * @since   2.5
     */
    protected function batchLanguage($value, $pks, $contexts)
    {
        // This method uses a table so we can't use the parent, but wouldn't anyway.
        return false;
    }

    /**
     * Batch move items to a new category
     *
     * @param   integer  $value     The new category ID.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     *
     * @since   1.7
     */
    protected function batchMove($value, $pks, $contexts)
    {
        // This method uses a table so we can't use the parent, but wouldn't anyway.
        return false;
    }


    /**
     * Batch tag a list of item.
     *
     * @param   integer  $value     The value of the new tag.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     *
     * @since   3.1
     */
    protected function batchTag($value, $pks, $contexts)
    {
        // This method uses a table so we can't use the parent, but wouldn't anyway.
        return false;
    }


    /**
     * Method override to check-in a record or an array of record
     *
     * @param   mixed  $pks  The ID of the primary key or an array of IDs
     *
     * @return  integer|boolean  Boolean false if there is an error, otherwise the count of records checked in.
     *
     * @since   1.6
     */
    public function checkin($pks = array())
    {
        // This method uses a table so we can't use the parent.
        return true;

        // If at some point it becomes possible to 'check in' a Trello card, do that here.
    }

    /**
     * Method override to check-out a record.
     *
     * @param   integer  $pk  The ID of the primary key.
     *
     * @return  boolean  True if successful, false if an error occurs.
     *
     * @since   1.6
     */
    public function checkout($pk = null)
    {
        // This method uses a table so we can't use the parent.
        return false;

        // If at some point it becomes possible to 'check out' a Trello card, do that here.
    }

    /**
     * Method to delete one or more records.
     *
     * @param   array  &$pks  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     *
     * @since   1.6
     */
    public function delete(&$pks)
    {
        // Trello Delete call here.
        // @TODO

        return false;
    }

    /**
     * Method to change the title & alias.
     *
     * @param   integer  $categoryId  The id of the category.
     * @param   string   $alias       The alias.
     * @param   string   $title       The title.
     *
     * @return  array  Contains the modified title and alias.
     *
     * @since   1.7
     */
    protected function generateNewTitle($categoryId, $alias, $title)
    {
        // We don't use this.
        return false;
    }

    /**
     * Method to get a single record.
     *
     * @param   string  $id  The id of the card.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($id = null)
    {
        #$input = Factory::getApplication()->input;
        $id = (!empty($id)) ? $id : $this->getState('designrequest.id');
        #echo 'input<pre>'; var_dump($id); echo '</pre>'; exit;
        if (!empty($id)) {
            return DesignRequestsHelper::$trello_cards[$id];
        }
        return false;
    }

    /**
     * Stock method to auto-populate the model state.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState()
    {
        // This method uses a table so we can't use the parent.
        return false;

        // If we need it:

        // Establish what $key should be:

        // Then this is from the parent method:

        // Get the pk of the record from the request.
        /*$pk = \JFactory::getApplication()->input->getInt($key);
        $this->setState($this->getName() . '.id', $pk);

        // Load the parameters.
        $value = \JComponentHelper::getParams($this->option);
        $this->setState('params', $value);*/
    }

    /**
     * Method to change the published state of one or more records.
     *
     * @param   array    &$pks   A list of the primary keys to change.
     * @param   integer  $value  The value of the published state.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     */
    public function publish(&$pks, $value = 1)
    {
        // This method uses a table so we can't use the parent.
        return false;
    }

    /**
     * Method to adjust the ordering of a row.
     *
     * Returns NULL if the user did not have edit
     * privileges for any of the selected primary keys.
     *
     * @param   integer  $pks    The ID of the primary key to move.
     * @param   integer  $delta  Increment, usually +1 or -1
     *
     * @return  boolean|null  False on failure or error, true on success, null if the $pk is empty (no items selected).
     *
     * @since   1.6
     */
    public function reorder($pks, $delta = 0)
    {
        // This method uses a table so we can't use the parent.
        return false;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   1.6
     */
    public function save($data)
    {
        // This method uses a table so we can't use the parent the parent.

        #echo '<pre>'; var_dump($data); echo '</pre>'; #exit;

        $card_id = DesignRequestsHelper::trelloSaveCard($data);
        if (!empty($card_id)) {
            $this->setState($this->getName() . '.id', $card_id);
            return true;
        }
        return false;
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array    $pks    An array of primary key ids.
     * @param   integer  $order  +1 or -1
     *
     * @return  boolean|\JException  Boolean true on success, false on failure, or \JException if no items are selected
     *
     * @since   1.6
     */
    public function saveorder($pks = array(), $order = null)
    {
        // This method uses a table so we can't use the parent.
        return false;
    }

    /**
     * Method to create a tags helper to ensure proper management of tags
     *
     * @param   \JTableObserverTags  $tagsObserver  The tags observer for this table
     * @param   \JUcmType            $type          The type for the table being processed
     * @param   integer              $pk            Primary key of the item bing processed
     * @param   string               $typeAlias     The type alias for this table
     * @param   \JTable              $table         The \JTable object
     *
     * @return  void
     *
     * @since   3.2
     */
    public function createTagsHelper($tagsObserver, $type, $pk, $typeAlias, $table)
    {
        // This method uses a table so we can't use the parent.
        return false;
    }

    /**
     * Method to check the validity of the category ID for batch copy and move
     *
     * @param   integer  $categoryId  The category ID to check
     *
     * @return  boolean
     *
     * @since   3.2
     */
    protected function checkCategoryId($categoryId)
    {
        // This method uses a table so we can't use the parent, and we're not using categories.
        return false;
    }

    /**
     * A method to preprocess generating a new title in order to allow tables with alternative names
     * for alias and title to use the batch move and copy methods
     *
     * @param   integer  $categoryId  The target category id
     * @param   \JTable  $table       The \JTable within which move or copy is taking place
     *
     * @return  void
     *
     * @since   3.2
     */
    public function generateTitle($categoryId, $table)
    {
        // We're not using this.
        return false;
    }

    /**
     * Method to initialize member variables used by batch methods and other methods like saveorder()
     *
     * @return  void
     *
     * @since   3.8.2
     */
    public function initBatch()
    {
        // This method uses a table so we can't use the parent.
        return false;
    }

}
