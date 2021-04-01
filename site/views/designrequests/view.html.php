<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

// Load the Admin language file to avoid repeating form language strings:
$lang = JFactory::getLanguage();
$extension = 'com_designrequests';
$base_dir = JPATH_COMPONENT_ADMINISTRATOR;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

/**
 * HTML View class for the DesignRequests Component
 */
class DesignRequestsViewDesignRequests extends JViewLegacy
{
    // Overwriting JView display method
    function display($tpl = null)
    {
        $user = JFactory::getUser();
        #echo '<pre>'; var_dump($user); echo '</pre>'; exit;

        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the list on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually redeclare them, along with any other properties of the form that may be
        // useful:
        //$this->setModel($this->getModel('designrequests'));
        #jimport('joomla.application.component.model');
        #JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_designrequests/models');
        require JPATH_SITE . '/components/com_designrequests/models/designrequest.php';
        $designrequests_model = JModelLegacy::getInstance('DesignRequestform', 'DesignRequestsModel');
        #echo '<pre>'; var_dump($designrequests_model); echo '</pre>'; exit;
        $form = $designrequests_model->getForm();
        #echo '<pre>'; var_dump($form); echo '</pre>'; exit;


        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();

        // Get the parameters
        $this->com_params  = JComponentHelper::getParams('com_designrequests');
        $this->menu_params = $menu->params;

        $layout = $this->getLayout();
        if ($layout != 'default') {
            $breadcrumb_title = $breadcrumb_title  = JText::_('COM_DESIGNREQUESTS_PAGE_TITLE_' . strtoupper($layout));

            #echo '<pre>'; var_dump($breadcrumb_title); echo '</pre>'; exit;

            $app     = JFactory::getApplication();
            $pathway = $app->getPathway();
            $pathway->addItem($breadcrumb_title);
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }

        // Assign data to the view:
        $this->items = $this->get('Items');
        $this->status_list_key_name_map = $this->get('StatusListKeyNameMap');
        $this->fields_job_types = $this->get('FieldsJobTypes');
        $this->fields_print = $this->get('FieldsPrint');
        $this->fields_print_sizes = $this->get('FieldsPrintSizes');
        $this->fields_projects = $this->get('FieldsProjects');
        #$this->items = $this->get('AllItems');
        #$this->items = $this->get('UnpublishedItems');
        
        #echo '<pre>'; var_dump($this->items); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($this->status_list_key_name_map); echo '</pre>'; exit;

        $this->user  = $user;
        $this->title = $menu->title;
        $this->form  = $form;

        // Display the view
        parent::display($tpl);
    }
}
