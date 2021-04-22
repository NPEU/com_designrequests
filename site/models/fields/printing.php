<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;


/*
    IMPORTANT = this field has an UNPROTECTED dependency on the FirstLastNames plugin.
    This extension will break of that's not installed and enabled.
*/


JFormHelper::loadFieldClass('list');

/**
 * Form field for a list of active research admins.
 */
class JFormFieldPrinting extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Printing';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $options          = array();
        $printing_options = DesignRequestsHelper::$trello_fields_print;

        $i = 0;
        foreach ($printing_options as $printing_option) {
            $options[] = JHtml::_('select.option', $printing_option, $printing_option);
            $i++;
        }
        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = JText::_('COM_DESIGNREQUESTS_PRINTING_EMPTY');
        }
        return $options;
    }
}