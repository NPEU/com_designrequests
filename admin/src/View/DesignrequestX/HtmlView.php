<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Administrator\View\Designrequest;

defined('_JEXEC') or die;


use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Component\ComponentHelper;


class HtmlView extends BaseHtmlView {

    protected $form;
    protected $item;
    protected $canDo;

    /**
     * Display the "Hello World" edit view
     */
    function display($tpl = null) {

        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');

        // What Access Permissions does this user have? What can (s)he do?
        $this->canDo = ContentHelper::getActions('com_designrequests', 'designrequest', $this->item->id);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolBar();

        $this->setDocument();

        parent::display($tpl);
    }

    protected function addToolBar() {

        $input = Factory::getApplication()->input;

        // Hide Joomla Administrator Main menu
        $input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        ToolBarHelper::title($isNew ? Text::_('COM_DESIGNREQUESTS_MANAGER_RECORD_ADD')
                                    : Text::_('COM_DESIGNREQUESTS_MANAGER_RECORD_EDIT'), 'designrequest');
        // Build the actions for new and existing records.
        if ($isNew) {
            // For new records, check the create permission.
            if ($this->canDo->get('core.create')) {
                ToolbarHelper::apply('designrequest.apply', 'JTOOLBAR_APPLY');
                ToolbarHelper::save('designrequest.save', 'JTOOLBAR_SAVE');
                ToolbarHelper::custom('designrequest.save2new', 'save-new.png', 'save-new_f2.png',
                                       'JTOOLBAR_SAVE_AND_NEW', false);
            }
            ToolbarHelper::cancel('designrequest.cancel', 'JTOOLBAR_CANCEL');
        } else {
            if ($this->canDo->get('core.edit')) {
                // We can save the new record
                ToolbarHelper::apply('designrequest.apply', 'JTOOLBAR_APPLY');
                ToolbarHelper::save('designrequest.save', 'JTOOLBAR_SAVE');

                // We can save this record, but check the create permission to see
                // if we can return to make a new one.
                if ($this->canDo->get('core.create')) {
                    ToolbarHelper::custom('designrequest.save2new', 'save-new.png', 'save-new_f2.png',
                                           'JTOOLBAR_SAVE_AND_NEW', false);
                }
                /*$save_history = Factory::getApplication()->get('save_history', true);
                if ($save_history) {
                    ToolbarHelper::versions('com_designrequest.designrequest', $this->item->id);
                }*/
            }

            if ($this->canDo->get('core.create')) {
                ToolbarHelper::custom('designrequest.save2copy', 'save-copy.png', 'save-copy_f2.png',
                                       'JTOOLBAR_SAVE_AS_COPY', false);
            }
            ToolbarHelper::cancel('designrequest.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    protected function setDocument() {
        //HtmlHelper::_('behavior.framework');
        //HtmlHelper::_('behavior.formvalidator');

        $isNew = ($this->item->id < 1);
        $this->document->setTitle($isNew
          ? Text::_('COM_DESIGNREQUESTS_RECORD_CREATING')
          : Text::_('COM_DESIGNREQUESTS_RECORD_EDITING')
        );
    }
}