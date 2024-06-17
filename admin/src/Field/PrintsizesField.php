<?php
namespace NPEU\Component\Designrequests\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\Database\DatabaseInterface;

use NPEU\Component\Designrequests\Administrator\Helper\DesignrequestsHelper;

defined('_JEXEC') or die;

#JFormHelper::loadFieldClass('list');

/**
 * Form field for a list of brands.
 */
class PrintsizesField extends ListField
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Printsizes';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $options   = [];
        $printing_sizes = DesignRequestsHelper::$trello_fields_print_sizes;

        $i = 0;
        foreach ($printing_sizes as $printing_size) {
            $options[] = HTMLHelper::_('select.option', $printing_size, $printing_size);
            $i++;
        }
        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = Text::_('COM_DESIGNREQUESTS_PRINTSIZE_EMPTY');
        }
        return $options;
    }
}