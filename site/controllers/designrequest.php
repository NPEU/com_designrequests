<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

#use Joomla\Utilities\ArrayHelper;


/**
 * DesignRequests Controller
 */
class DesignRequestsControllerDesignRequest extends JControllerForm
{
    /**
     * The URL view item variable.
     *
     * @var    string
     * @since  1.6
     */
    #protected $view_item = 'form';

    /**
     * The URL view list variable.
     *
     * @var    string
     * @since  1.6
     */
    #protected $view_list = 'categories';

    /**
     * The URL edit variable.
     *
     * @var    string
     * @since  3.2
     */
    #protected $urlVar = 'a.id';



    protected function releaseEditId($context, $id)
    {
        // FormController messes up the id by casting it to an (int), so check for that and get
        // the real id from the input:
        if (is_int($id)) {
            $input = JFactory::getApplication()->input;
            $id = $input->get('id');
        }
        parent::releaseEditId($context, $id);
    }


    /**
     * Method to add a new record.
     *
     * @return  boolean  True if the article can be added, false if not.
     */
    public function add()
    {
        if (!parent::add())
        {
            // Redirect to the return page.
            $this->setRedirect($this->getReturnPage());
        }
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param   array  $data  An array of input data.
     *
     * @return  boolean
     */
    protected function allowAdd($data = array())
    {
        /*$categoryId   = ArrayHelper::getValue($data, 'catid', $this->input->getInt('id'), 'int');
        $allow      = null;

        if ($categoryId)
        {
            // If the category has been passed in the URL check it.
            $allow = JFactory::getUser()->authorise('core.create', $this->option . '.category.' . $categoryId);
        }

        if ($allow !== null)
        {
            return $allow;
        }*/

        // In the absense of better information, revert to the component permissions.
        return parent::allowAdd($data);
    }

    /**
     * Method to check if you can add a new record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $user    = JFactory::getUser();
        $user_id = $user->id;
        $user_is_root = $user->authorise('core.admin');

        // FormController messes up the id by casting it to an (int), so check for that and get
        // the real id from the input:
        if (is_int($data[$key])) {
            $input = JFactory::getApplication()->input;
            $data[$key] = $input->get('id');
        }

        $card = DesignRequestsHelper::$trello_cards[$data[$key]];
        $user_string_id = $user->name . ' <' . $user->email . '>';

        $is_own = false;
        if ($user_string_id == $card->customFieldItemsKey['requested_by']->realvalue) {
            $is_own = true;
        }

        if ($user_is_root) {
            $authorised = true;
        } elseif ($is_own) {
            $authorised = $user->authorise('core.edit.own', 'com_designrequests');
        }
        else {
            $authorised = $user->authorise('core.edit', 'com_designrequests');
        }

        return $authorised;
    }

    /**
     * Method to cancel an edit.
     *
     * @param   string  $key  The name of the primary key of the URL variable.
     *
     * @return  boolean  True if access level checks pass, false otherwise.
     */
    public function cancel($key = 'id')
    {
        $return = parent::cancel($key);

        // Redirect to the return page.
        $this->setRedirect($this->getReturnPage());

        return $return;
    }

    /**
     * Method to edit an existing record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if access level check and checkout passes, false otherwise.
     */
    public function edit($key = null, $urlVar = 'id')
    {
        // we can't use the parent::edit because it forces the id to be an (int), so unfortunately
        // I've had to copy and paste the code for that method here. I can't see another way
        // round it:

        // Do not cache the response to this, its a redirect, and mod_expires and google chrome browser bugs cache it forever!
        \JFactory::getApplication()->allowCache(false);

        $model = $this->getModel();
        $table = $model->getTable();
        $cid   = $this->input->post->get('cid', array(), 'array');
        $context = "$this->option.edit.$this->context";

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        // !!! ANDY: This is the problematic line:
        #$recordId = (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));
        $recordId = (count($cid) ? $cid[0] : $this->input->get($urlVar));
        $checkin = property_exists($table, $table->getColumnAlias('checked_out'));

        // Access check.
        if (!$this->allowEdit(array($key => $recordId), $key))
        {
            $this->setError(\JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                \JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }
        /* We can't check out Trello cards so might as well skip this:
        // Attempt to check-out the new record for editing and redirect.
        if ($checkin && !$model->checkout($recordId))
        {
            // Check-out failed, display a notice but allow the user to see the record.
            $this->setError(\JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                \JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }
        else
        {*/
            // Check-out succeeded, push the new record id into the session.
            $this->holdEditId($context, $recordId);
            \JFactory::getApplication()->setUserState($context . '.data', null);

            $this->setRedirect(
                \JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return true;
        #}
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     */
    public function getModel($name = 'designrequest', $prefix = '', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Gets the URL arguments to append to an item redirect.
     *
     * @param   integer  $recordId  The primary key id for the item.
     * @param   string   $urlVar    The name of the URL variable for the id.
     *
     * @return  string  The arguments to append to the redirect URL.
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = null)
    {
        // FormController messes up the id by casting it to an (int), so check for that and get
        // the real id from the input:
        if (is_int($recordId)) {
            $input = JFactory::getApplication()->input;
            $recordId = $input->get('id');
        }

        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
        /*$itemId = $this->input->getInt('Itemid');
        $return = $this->getReturnPage();

        if ($itemId)
        {
            $append .= '&Itemid=' . $itemId;
        }

        if ($return)
        {
            $append .= '&return=' . base64_encode($return);
        }*/

        return $append;
    }

    /**
     * Get the return URL if a "return" variable has been passed in the request
     *
     * @return  string  The return URL.
     */
    protected function getReturnPage()
    {
        $return = $this->input->get('return', null, 'base64');

        if (empty($return) || !JUri::isInternal(base64_decode($return)))
        {
            return JUri::base();
        }

        return base64_decode($return);
    }

    /**
     * Method to save a record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     */
    public function save($key = null, $urlVar = 'id')
    {
        $is_ajax =  JFactory::getApplication()->input->get('ajax', '', 'bool');

        $result = parent::save($key, $urlVar);
        #echo 'result<pre>'; var_dump($result); echo '</pre>'; exit;
        if ($is_ajax) {
            $app = JFactory::getApplication();
            try {
                $record_id = false;

                if ($result) {
                    $is_new = (bool) !$this->input->getInt($urlVar, false);
                    $message_type = 'success';
                    $message = $is_new ? JText::_('JLIB_APPLICATION_SUBMIT_SAVE_SUCCESS')
                                       : JText::_('JLIB_APPLICATION_SAVE_SUCCESS');
                    // Get the latest id:
                    $db = JFactory::getDbo();
                    $record_id = $db->insertid();
                } else {
                    $message_type = 'error';
                    $message = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', 'Undetermined error.');
                }
                // Pass the new id back - may be useful:
                $data = array('id'=>$record_id);

                $app->enqueueMessage($message, $message_type);

                echo new JResponseJson($data, $message, $result);

            } catch(Exception $e) {
                echo new JResponseJson($e);
            }
            $app->close();
            exit;
        }

        // If ok, redirect to the return page.
        /*if ($result)
        {
            $this->setRedirect($this->getReturnPage());
        }*/
        return $result;
    }
}
