<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Site\View\Designrequests;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
#use Joomla\CMS\Helper\TagsHelper;
#use Joomla\CMS\Router\Route;
#use Joomla\CMS\Plugin\PluginHelper;
#use Joomla\Event\Event;

use NPEU\Component\Designrequests\Site\Model\DesignrequestModel;

/**
 * Designrequests Component HTML View
 */
class HtmlView extends BaseHtmlView {


    /**
     * The page parameters
     *
     * @var    \Joomla\Registry\Registry|null
     */
    protected $params;

    /**
     * The item model state
     *
     * @var    \Joomla\Registry\Registry
     */
    protected $state;

    // This allows alternate views to overide this and supply a different title:
    protected function getTitle($title = '') {
        return $title;
    }

    public function display($template = null)
    {
        $app    = Factory::getApplication();
        $uri    = Uri::getInstance();

        $this->params = $app->getParams();

        $menus  = $app->getMenu();
        $menu   = $menus->getActive();
        #echo 'menu<pre>'; var_dump($menu); echo '</pre>'; exit;
        // Get the parameters
        #$this->com_params  = JComponentHelper::getParams('com_designrequests');
        $this->menu_params = $menu->getParams();

        #echo 'menu_params<pre>'; var_dump($this->menu_params); echo '</pre>'; exit;

        #$this->state  = $this->get('State');
        #$this->params = $this->state->get('params');
        #$this->items  = $this->get('Items');

        $user = $app->getIdentity();
        $user_is_root = $user->authorise('core.admin');
        $this->user  = $user;

        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the list on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually redeclare them, along with any other properties of the form that may be
        // useful:
        $designrequests_model = new DesignrequestModel();
        $designrequests_model->is_in_list_view = true;;
        $form = $designrequests_model->getForm();
        #$form = '';

        // Load admin lang file for use in the form:
        $app->getLanguage()->load('com_designrequests', JPATH_COMPONENT_ADMINISTRATOR);


        $uri    = Uri::getInstance();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();

        $this->title = $this->getTitle($menu->title);
        #echo '<pre>'; var_dump($this->title); echo '</pre>'; exit;
        #$this->menu_params = $menu->getParams();

        // Add to breadcrumbs:
        $pathway = $app->getPathway();

        $layout = $this->getLayout();
        if ($layout != 'default') {

            $page_title = Text::_('COM_DESIGNREQUESTS_PAGE_TITLE_' . strtoupper($layout));
            $pathway->addItem($page_title);
            $menu->title = $page_title;
        }

        // Check for errors.
        $errors = $this->get('Errors', false);

        if (!empty($errors)) {
            Log::add(implode('<br />', $errors), Log::WARNING, 'jerror');

            return false;
        }


        // Assign data to the view:
        $this->items = $this->get('Items');
        $this->status_list_key_name_map = $this->get('StatusListKeyNameMap');
        $this->fields_job_types = $this->get('FieldsJobTypes');
        $this->fields_print = $this->get('FieldsPrint');
        $this->fields_print_sizes = $this->get('FieldsPrintSizes');
        $this->fields_projects = $this->get('FieldsProjects');


        $this->user  = $user;
        $this->title = $menu->title;
        $this->form  = $form;

        #echo '<pre>'; var_dump($this->items); echo '</pre>'; exit;
        // Call the parent display to display the layout file
        parent::display($template);

        /*

        $user = JFactory::getUser();
        $user_is_root = $user->authorise('core.admin');

        $item = $this->get('Item');
        // We may not actually want to show the form at this point (though we could if we wanted to
        // include the form AND the record on the same page - especially if it's displayed via a
        // modal), but it's useful to have the form so we can retrieve language strings without
        // having to manually reclare them, along with any other properties of the form that may be
        // useful:
        $form = $this->get('Form');
        #echo '<pre>'; var_dump($item); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($form); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($this->getLayout()); echo '</pre>'; exit;

        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $menu   = $menus->getActive();
        #echo '<pre>'; var_dump($menu); echo '</pre>'; exit;
        #echo '<pre>'; var_dump(JRoute::_($menu->link)); echo '</pre>'; exit;
        #echo '<pre>'; var_dump(URI::base()); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($item->id); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user, $item); echo '</pre>'; exit;
        #echo '<pre>'; var_dump($user->id, $item->created_by); echo '</pre>'; exit;

        $this->return_page = base64_encode(URI::base() . $menu->route);


        $is_new = empty($item->id);
        $is_own = false;
        if (!$is_new && ($user->id == $item->created_by)) {
            $is_own = true;
        }


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


        // Assign data to the view
        $this->item = $item;
        // Although we're not actually showing the form, it's useful to use it to be able to show
        // the field names without having to explicitly state them (more DRY):
        $this->form = $form;

        */




        /*
        $app = Factory::getApplication();

        $this->item   = $this->get('Item');
        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        // Create a shortcut for $item.
        $item = $this->item;

        $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

        $temp         = $item->params;
        $item->params = clone $app->getParams();
        $item->params->merge($temp);

        $offset = $this->state->get('list.offset');

        $app->triggerEvent('onContentPrepare', array('com_weblinks.weblink', &$item, &$item->params, $offset));

        $item->event = new \stdClass;

        $results = $app->triggerEvent('onContentAfterTitle', array('com_weblinks.weblink', &$item, &$item->params, $offset));
        $item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $app->triggerEvent('onContentBeforeDisplay', array('com_weblinks.weblink', &$item, &$item->params, $offset));
        $item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = $app->triggerEvent('onContentAfterDisplay', array('com_weblinks.weblink', &$item, &$item->params, $offset));
        $item->event->afterDisplayContent = trim(implode("\n", $results));

        parent::display($tpl);
        */







        /*// Assign data to the view
        $this->msg = 'Get from API';
        */

    }

}