<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Site\Controller;

defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;

use NPEU\Component\Designrequests\Administrator\Helper\DesignrequestsHelper;

/**
 * Designrequest Component Controller
 *
 * Used to handle the http POST from the front-end form which allows users to enter a new designrequest
 */
class DesignrequestController extends FormController
{
    protected $view_item;  // default view within JControllerForm for reload function

    public function __construct($config = array())
    {
        $input = Factory::getApplication()->input;
        $this->view_item = $input->get("view", "designrequest", "string");
        parent::__construct($config);
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
        $user    = Factory::getUser();
        $user_id = $user->id;
        $user_is_root = $user->authorise('core.admin');

        // FormController messes up the id by casting it to an (int), so check for that and get
        // the real id from the input:
        #if (is_int($data[$key])) {
            #$input = Factory::getApplication()->input;
            #$data[$key] = $input->get('id');
        #}

        // Somewhere the Router is casting non-numeric strings to `1` and since Trello uses string
        // ID's we can get the correct ID from the input, so hacking around this to use the URI.
        $uri = explode('/', Uri::getInstance());
        $id = array_slice($uri, -2, 1)[0];

        $card = DesignRequestsHelper::$trello_cards[$id];

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

    public function cancel($key = null)
    {
        parent::cancel($key);

        // set up the redirect back to the same form
        /*$this->setRedirect(
            (string) Uri::getInstance(),
            Text::_('COM_DESIGNREQUESTS_ADD_CANCELLED')
        );*/
        $recordId = $this->input->getInt('id', false);

        if ($recordId) {
            $url = 'index.php?option=' . $this->option . '&view=' . $this->view_item . '&id=' . $recordId . $this->getRedirectToListAppend();
            $message = Text::_('COM_DESIGNREQUESTS_EDIT_CANCELLED');
        } else {
            $url = 'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend();
            $message = Text::_('COM_DESIGNREQUESTS_ADD_CANCELLED');
        }

        $route = Route::_($url);

        // Redirect to the list screen.
        $this->setRedirect($route, $message);
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
        Factory::getApplication()->allowCache(false);

        $model = $this->getModel();
        $table = $model->getTable();
        $cid   = $this->input->post->get('cid', array(), 'array');
        $context = "$this->option.edit.$this->context";

        // Determine the name of the primary key for the data.
        if (empty($key)) {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar)) {
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
            $this->setError(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }
    }

    public function abort()
    {
        // Using unconventional 'abort' task so that we can show the message. Using 'cancel'
        // results in an invalid token error so we can't use that (unless I figure out how)
        // Plus we don't need to 'check-in' an item so this may be better anyway?
        $url = 'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend();
        $message = Text::_('COM_DESIGNREQUESTS_ADD_CANCELLED');

        $route = Route::_($url);
        // Redirect to the list screen.
        $this->setRedirect($route, $message);
    }


    /*
     * Function handing the save for adding a new designrequests record
     * Based on the save() function in the JControllerForm class
     */
    public function save($key = null, $urlVar = 'id')
    {
        // Check for request forgeries.
        $this->checkToken();

        $app     = $this->app;
        $model   = $this->getModel();
        #$table   = $model->getTable();
        $data    = $this->input->post->get('jform', [], 'array');
        #$checkin = $table->hasField('checked_out');
        $context = "$this->option.edit.$this->context";
        $task    = $this->getTask();

        // Determine the name of the primary key for the data.
        #if (empty($key)) {
        #    $key = $table->getKeyName();
        #}

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar)) {
            $urlVar = $key;
        }

        #$recordId = $this->input->getInt($urlVar);
        $recordId = $this->input->get($urlVar);

        // Populate the row id from the session.
        $data[$key] = $recordId;

        // Access check.
        if (!$this->allowSave($data, $key)) {
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                        . $this->getRedirectToListAppend(),
                    false
                )
            );

            return false;
        }

        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);

        if (!$form) {
            $app->enqueueMessage($model->getError(), 'error');

            return false;
        }

        // Send an object which can be modified through the plugin event
        /*$objData = (object) $data;
        $app->triggerEvent(
            'onContentNormaliseRequestData',
            [$this->option . '.' . $this->context, $objData, $form]
        );
        $data = (array) $objData;*/

        // Test whether the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false) {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = \count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof \Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            /**
             * We need the filtered value of calendar fields because the UTC normalisation is
             * done in the filter and on output. This would apply the Timezone offset on
             * reload. We set the calendar values we save to the processed date.
             */
            $filteredData = $form->filter($data);

            foreach ($form->getFieldset() as $field) {
                if ($field->type === 'Calendar') {
                    $fieldName = $field->fieldname;

                    if (isset($filteredData[$fieldName])) {
                        $data[$fieldName] = $filteredData[$fieldName];
                    }
                }
            }

            // Save the data in the session.
            $app->setUserState($context . '.data', $data);

            // Redirect back to the edit screen.
            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar),
                    false
                )
            );

            return false;
        }

        /*if (!isset($validData['tags'])) {
            $validData['tags'] = [];
        }*/

        // Attempt to save the data.
        if (!$model->save($validData)) {
            // Save the data in the session.
            $app->setUserState($context . '.data', $validData);

            // Redirect back to the edit screen.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar),
                    false
                )
            );

            return false;
        }

        $langKey = $this->text_prefix . ($recordId === 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS';
        $prefix  = $this->app->getLanguage()->hasKey($langKey) ? $this->text_prefix : 'JLIB_APPLICATION';

        $this->setMessage(Text::_($prefix . ($recordId === 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task) {
            case 'apply':
                // Set the record data in the session.
                $recordId = $model->getState($model->getName() . '.id');
                $this->holdEditId($context, $recordId);
                $app->setUserState($context . '.data', null);
                #$model->checkout($recordId);

                // Redirect back to the edit screen.
                $this->setRedirect(
                    Route::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                            . $this->getRedirectToItemAppend($recordId, $urlVar),
                        false
                    )
                );
                break;

            default:
                // Clear the record id and data from the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState($context . '.data', null);

                $url = 'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend();

                // Check if there is a return value
                $return = $this->input->get('return', null, 'base64');

                if (!\is_null($return) && Uri::isInternal(base64_decode($return))) {
                    $url = base64_decode($return);
                }

                // Redirect to the list screen.
                $this->setRedirect(Route::_($url, false));
                break;
        }

        // Invoke the postSave method to allow for the child class to access the model.
        $this->postSaveHook($model, $validData);

        return true;
    }
    /*{

        // Get the application
        $app = $this->app;

        $context = "$this->option.edit.$this->context";

        // Get the data from POST
        $data = $this->input->post->get('jform', array(), 'array');

        // Save the data in the session.
        $app->setUserState($context . '.data', $data);
        $result = parent::save($key, $urlVar);

        // If ok, redirect to the return page.
        if ($result) {
            // Flush the data from the session
            $app->setUserState($context . '.data', null);
            $this->setRedirect($this->getReturnPage());
        }

        return $result;
    }*/
    /*public function save($key = null, $urlVar = 'id')
    {
        $is_ajax =  Factory::getApplication()->input->get('ajax', '', 'bool');

        $result = parent::save($key, $urlVar);
        #echo 'result<pre>'; var_dump($result); echo '</pre>'; exit;
        if ($is_ajax) {
            $app = Factory::getApplication();
            try {
                $record_id = false;

                if ($result) {
                    $is_new = (bool) !$this->input->getInt($urlVar, false);
                    $message_type = 'success';
                    $message = $is_new ? Text::_('JLIB_APPLICATION_SUBMIT_SAVE_SUCCESS')
                                       : Text::_('JLIB_APPLICATION_SAVE_SUCCESS');
                    // Get the latest id:
                    $db = Factory::getDbo();
                    $record_id = $db->insertid();
                } else {
                    $message_type = 'error';
                    $message = Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', 'Undetermined error.');
                }
                // Pass the new id back - may be useful:
                $data = array('id'=>$record_id);

                $app->enqueueMessage($message, $message_type);

                echo new JsonResponse($data, $message, $result);

            } catch(Exception $e) {
                echo new JsonResponse($e);
            }
            $app->close();
            exit;
        }
    }*/

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.z
     *
     * @return  object  The model.
     *
     * @since   1.5
     */
    public function getModel($name = 'Form', $prefix = 'Designrequests', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }


    /**
     * Get the return URL if a "return" variable has been passed in the request
     *
     * @return  string  The return URL.
     *
     * @since   1.6
     */
    protected function getReturnPage()
    {
        $return = $this->input->get('return', null, 'base64');

        if (empty($return) || !Uri::isInternal(base64_decode($return))) {
            return Uri::base();
        }

        // We need to check if the alias is being used in the return URL and update it if so
        // (it may have changed and would then result in a 404). This seems unreliable - if form
        // input names change this will break. I guess thats' why it's usual to redirect to the
        // listing page to avoud this issue. If it becomes a problem, do that.
        $r = base64_decode($return);
        $alias = $this->input->post->get('jform', array(), 'array')['alias'];
        $original_alias = $this->input->post->get('original_alias');

        $r = str_replace('/' . $original_alias . '/', '/' . $alias . '/', $r);
        return $r;
    }

}