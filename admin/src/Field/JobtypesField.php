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
class JobtypesField extends ListField
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'JobTypes';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $options   = [];
        $job_types = DesignRequestsHelper::$trello_fields_job_types;

        $i = 0;
        foreach ($job_types as $job_type) {
            $options[] = HTMLHelper::_('select.option', $job_type, $job_type);
            $i++;
        }
        if ($i > 0) {
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        } else {
            $options = parent::getOptions();
            $options[0]->text = Text::_('COM_DESIGNREQUESTS_JOBTYPES_EMPTY');
        }
        return $options;
    }
}