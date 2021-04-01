<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

$table_id = 'designrequestsTable';
// If you need specific JS/CSS for this view, add them here.
// Example included for DataTables (https://datatables.net/) delete if you don't want this.
// Make sure jQuery is loaded first:
JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
// Get the doc object:
$doc = JFactory::getDocument();
#echo '<pre>'; var_dump($this->items); echo '</pre>'; exit;

$date_format = 'd M Y';
?>
<p>
<a href="<?php echo JRoute::_('index.php?option=com_designrequests&task=designrequest.add'); ?>">Add new</a>
</p>
<table class="table table-striped table-hover" id="<?php echo $table_id; ?>">
    <thead>
        <tr>
            <th>
                <?php echo JText::_('COM_DESIGNREQUESTS_DEADLINE'); ?>
            </th>
            <th>
                <?php echo JText::_('COM_DESIGNREQUESTS_TITLE'); ?>
            </th>
            <th>
                <?php echo JText::_('COM_DESIGNREQUESTS_PROJECT'); ?>
            </th>
            <th>
                <?php echo JText::_('COM_DESIGNREQUESTS_JOB_TYPE'); ?>
            </th>
            <th>
                <?php echo JText::_('COM_DESIGNREQUESTS_REQUEST_DATE'); ?>
            </th>
            <th>
                <?php echo JText::_('COM_DESIGNREQUESTS_STATUS'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($this->items)) : ?>
        <?php foreach ($this->items as $i => $item) :
            $view_link = JRoute::_('index.php?option=com_designrequests&task=designrequest.view&id=' . $item->id);
            $edit_link = JRoute::_('index.php?option=com_designrequests&task=designrequest.edit&id=' . $item->id);
            $is_own = false;
            if ($this->user->authorise('core.edit.own', 'com_designrequests') && ($this->user->id == $item->customFieldItemsKey['requested_by'])) {
                $is_own = true;
            }
            $authorised = $this->user->authorise('core.edit', 'com_designrequests');
        ?>
        <?php /*<tr>
            <td colspan="6"><pre><?php var_dump($item); ?></pre></td>
        </tr>*/ ?>
        <tr>
            <td>
                <?php echo date($date_format, strtotime($item->due)); ?>
            </td>
            <td> 
                <a href="<?php echo $view_link; ?>" title="<?php echo JText::_('COM_DESIGNREQUESTS_VIEW_RECORD'); ?>">
                    <?php echo $item->name; ?>
                </a>
                <?php if($is_own || $authorised): ?>
                <br>
                <br>
                <a href="<?php echo $edit_link; ?>" title="<?php echo JText::_('COM_DESIGNREQUESTS_EDIT_RECORD'); ?>">
                    <?php echo JText::_('COM_DESIGNREQUESTS_RECORDS_ACTION_EDIT'); ?>
                </a>
                <?php endif; ?>
            </td>
            <td>
                <?php echo $this->fields_projects[$item->customFieldItemsKey['project']->idValue]; ?>
            </td>
            <td>
                <?php echo $this->fields_job_types[$item->customFieldItemsKey['job_type']->idValue]; ?>
            </td>
            <td>
                <?php echo date($date_format, strtotime($item->customFieldItemsKey['requested_on']->value->date)); ?>
            </td>
            <td>
                <?php echo $this->status_list_key_name_map[$item->status]; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No requests to show.</td>
            </tr>   
        <?php endif; ?>
    </tbody>
</table>
