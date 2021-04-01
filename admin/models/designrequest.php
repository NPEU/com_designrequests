<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

use Joomla\String\StringHelper;

/**
 * DesignRequests DesignRequest Model
 */
class DesignRequestsModelDesignRequest extends JModelAdmin
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

        if (empty($form))
        {
            return false;
        }

        // Determine correct permissions to check.
        /*if ($this->getState('designrequest.id'))
        {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');
        }
        else
        {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }*/

        // Modify the form based on access controls.
        /*if (!$this->canEditState((object) $data))
        {
            // Disable fields for display.
            $form->setFieldAttribute('state', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('state', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
        }*/

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
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState(
            'com_designrequests.edit.designrequest.data',
            array()
        );

        if (empty($data)) {
            $data = $this->getItem();
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
	 * @since	1.7
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
	 * @since	1.7
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
        return false;

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
	 * @return	array  Contains the modified title and alias.
	 *
	 * @since	1.7
	 */
	protected function generateNewTitle($categoryId, $alias, $title)
	{
        // We don't use this.
        return false;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        // Admin Trello getIem call here.
        // @TODO

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

        // Trello Save call here.
        // @TODO

        return false;

        /*
        $is_new = empty($data['id']);
        $input  = JFactory::getApplication()->input;
        $app    = JFactory::getApplication();

        // Get parameters:
        $params = JComponentHelper::getParams(JRequest::getVar('option'));

        // For reference if needed:
        // By default we're only looking for and acting upon the 'email admins' setting.
        // If any other settings are related to this save method, add them here.
        /*$email_admins_string = $params->get('email_admins');
        if (!empty($email_admins_string) && $is_new) {
            $email_admins = explode(PHP_EOL, trim($email_admins_string));
            foreach ($email_admins as $email) {
                // Sending email as an array to make it easier to expand; it's quite likely that a
                // real app would need more info here.
                $email_data = array('email' => $email);
                $this->_sendEmail($email_data);
            }
        }*/

        // Alter the title for save as copy
        /*if ($app->input->get('task') == 'save2copy')
        {
            list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
            $data['title']    = $title;
            $data['alias']    = $alias;
            $data['state']    = 0;
        }*/

        /*if ($input->get('task') == 'save2copy') {
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));

            // Note is using custom category field title, you need to change 'catid':
            if ($data['title'] == $origTable->title) {
                list($title, $alias) = $this->generateNewBrandTitle($data['catid'], $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            } else {
                if ($data['alias'] == $origTable->alias) {
                    $data['alias'] = '';
                }
            }

            $data['state'] = 0;
        }*/

        // Automatic handling of alias for empty fields
        // Taken from com_content/models/article.php
        /*if (in_array($input->get('task'), array('apply', 'save', 'save2new'))) {
            if (empty($data['alias'])) {
                if (JFactory::getConfig()->get('unicodeslugs') == 1) {
                    $data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
                } else {
                    $data['alias'] = JFilterOutput::stringURLSafe($data['title']);
                }

                $table = JTable::getInstance('DesignRequests', 'DesignRequestsTable');

                if ($table->load(array('alias' => $data['alias']))) {
                    $msg = JText::_('COM_CONTENT_SAVE_WARNING');
                }

                #list($title, $alias) = $this->generateNewDesignRequestsTitle($data['alias'], $data['title']);
                list($title, $alias) = $this->generateNewTitle($data['alias'], $data['title']);
                $data['alias'] = $alias;

                if (isset($msg)) {
                    JFactory::getApplication()->enqueueMessage($msg, 'warning');
                }
            }
        }

        return parent::save($data);
        */
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
