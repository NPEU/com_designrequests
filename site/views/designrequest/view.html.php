<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

// Load the Admin language file to avoid repoeating form language strings:
$lang = JFactory::getLanguage();
$extension = 'com_designrequests';
$base_dir = JPATH_COMPONENT_ADMINISTRATOR;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

/**
 * HTML View class for the DesignRequests Component
 */
class DesignRequestsViewDesignRequest extends JViewLegacy
{
    // Overwriting JView display method
    function display($tpl = null)
    {
        $app      = JFactory::getApplication();
        $input    = $app->input;
        $document = JFactory::getDocument();
        $user = JFactory::getUser();
        #echo '<pre>'; var_dump($app); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($input); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($document); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user); echo '</pre>'; exit;

        #echo '<pre>'; var_dump($this->_defaultModel); echo '</pre>'; exit;

        $user_is_root = $user->authorise('core.admin');
        #echo '<pre>'; var_dump($user_is_root); echo '</pre>'; exit;

        $item = $this->get('Item');
        #echo '<pre>'; var_dump($item); echo '</pre>'; exit;
        $trello_fields_data_key_map  = DesignRequestsHelper::$trello_fields_data_key_map;
        $trello_fields_data_name_map = DesignRequestsHelper::$trello_fields_data_name_map;
        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the record on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually declare them, along with any other properties of the form that may be
        // useful:
        $form = $this->get('Form');

        #echo '<pre>'; var_dump($trello_fields_data_key_map); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($trello_fields_data_name_map); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($form); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($this->getLayout()); echo '</pre>'; exit;


        $menus  = $app->getMenu();
        $menu   = $menus->getActive();
        #echo '<pre>'; var_dump($menu); echo '</pre>'; exit;
        #echo '<pre>'; var_dump(JRoute::_($menu->link)); echo '</pre>'; exit;
        #echo '<pre>'; var_dump(JURI::base()); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($item->id); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user, $item); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user->id, $item->created_by); echo '</pre>'; exit;

        $this->return_page = base64_encode(JURI::base() . $menu->route);

        $is_new = empty($item->id);
        $is_own = false;

        $user_string_id = $user->name . ' <' . $user->email . '>';
        #echo '<pre>'; var_dump($item->customFieldItemsKey['requested_by']->realvalue); echo '</pre>'; exit;
        if (!$is_new && ($user_string_id == $item->customFieldItemsKey['requested_by']->realvalue)) {
            $is_own = true;
        }
        #echo '<pre>'; var_dump($is_own); echo '</pre>'; exit;

        if ($user_is_root) {
            $authorised = true;
        } elseif ($is_new) {
            $authorised = $user->authorise('core.create', 'com_designrequests');
        } elseif ($is_own) {
            $authorised = $user->authorise('core.edit.own', 'com_designrequests');
        }
        else {
            $authorised = $user->authorise('core.edit', 'com_designrequests');
        }

        if ($authorised !== true && $this->getLayout() == 'form') {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

            return false;
        }

        /*if (!empty($this->item))
        {
            $this->form->bind($this->item);
        }*/

        // Add to breadcrumbs:
        if (!empty($item->name)) {
            $breadcrumb_title = $item->name;
        }

        if ($is_new) {
            $breadcrumb_title = JText::_('COM_DESIGNREQUESTS_PAGE_TITLE_ADD_NEW');
        }
        #echo '<pre>'; var_dump($breadcrumb_title); echo '</pre>'; exit;
        $app     = JFactory::getApplication();
        $pathway = $app->getPathway();
        $pathway->addItem($breadcrumb_title);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }


        // Assign data to the view
        $this->item = $item;
        // Although we're not actually showing the form, it's useful to use it to be able to show
        // the field names without having to explicitly state them (more DRY):
        $this->form = $form;



        // Display the view
        parent::display($tpl);

        if ($input->get('layout') == 'form') {
            $document->page_heading_additional = ': ' . (
                $is_new
              ? JText::_('COM_DESIGNREQUESTS_REQUEST_CREATING')
              : JText::_('COM_DESIGNREQUESTS_REQUEST_EDITING') . ' ' . $item->name
            );
        }


        // Assign data to the view
        #$this->msg = 'Get from API';

        /*$form = $this->get('Form');
        $item   = $this->get('Item');

        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();

        // Get the parameters
        $this->com_params  = JComponentHelper::getParams('com_designrequests');
        $this->menu_params = $menu->params;

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }

        // Assign data to the view
        $this->form   = $form;
        $this->item   = $item;
        $this->title  = $menu->title;
        // Display the view
        parent::display($tpl);*/
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    /*protected function setDocument()
    {
        $isNew = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $document->setTitle($isNew ? JText::_('COM_RESEARCHPROJECTS_RECORD_CREATING') :
                JText::_('COM_RESEARCHPROJECTS_RECORD_EDITING'));

        if (!empty($this->script)) {
            $document->addScript(JURI::root() . $this->script);
        }

        $document->addScript(JURI::root() . "/administrator/components/com_researchprojects"
                                          . "/views/researchproject/submitbutton.js");
        JText::script('COM_RESEARCHPROJECTS_RECORD_ERROR_UNACCEPTABLE');
    }*/
}
