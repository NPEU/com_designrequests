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

$today_stamp = mktime(0, 0, 0, date('m'), date('j'), date('Y'));
$close_diff  = 60 * 60 * 24 * 7; // 7 Days
//$high_diff   = 60 * 60 * 24 * 3; // 3 Days
//$med_diff    = 60 * 60 * 24 * 7; // 7 Days

?>
<svg xmlns="http://www.w3.org/2000/svg" display="none">
    <symbol id="icon-new" viewBox="0 0 24 24"
        fill="none" fill-opacity="0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"        
    >
        <circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
    </symbol>
    <symbol id="icon-in_progress" viewBox="0 0 24 24"
        fill="none" fill-opacity="0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"        
    >
        <polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
    </symbol>
    <symbol id="icon-awaiting_content" viewBox="0 0 24 24"
        fill="none" fill-opacity="0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"        
    >
        <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
    </symbol>
    <symbol id="icon-awaiting_feedback" viewBox="0 0 24 24"
        fill="none" fill-opacity="0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"        
    >
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
    </symbol>
    <symbol id="icon-done" viewBox="0 0 24 24"
        fill="none" fill-opacity="0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"        
    >
        <polyline points="20 6 9 17 4 12"></polyline>
    </symbol>
</svg>
<p>
<a href="<?php echo JRoute::_('index.php?option=com_designrequests&task=designrequest.add'); ?>" class="c-cta">Add new</a>
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
            
            $class = '';
            
            $deadline = strtotime($item->due);
            /*if ($today_stamp + $high_diff >= $deadline) {
                $class = 'c-system-message  t-error';
            } elseif ($today_stamp + $med_diff >= $deadline) {
                $class = 'c-system-message  t-warning';
            }*/
            if ($today_stamp >= $deadline) {
                $class = 'c-system-message  t-error';
            } elseif ($today_stamp + $close_diff >= $deadline) {
                $class = 'c-system-message  t-warning';
            }
            
        ?>
        <?php /*<tr>
            <td colspan="6"><pre><?php var_dump($item); ?></pre></td>
        </tr>*/ ?>
        <tr>
            <td class="<?php echo $class; ?>">
                <?php echo date($date_format, strtotime($item->due)); ?>
            </td>
            <td class="<?php echo $class; ?>"> 
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
            <td class="<?php echo $class; ?>">
                <?php echo $this->fields_projects[$item->customFieldItemsKey['project']->idValue]; ?>
            </td>
            <td class="<?php echo $class; ?>">
                <?php echo $this->fields_job_types[$item->customFieldItemsKey['job_type']->idValue]; ?>
            </td>
            <td class="<?php echo $class; ?>">
                <?php echo date($date_format, strtotime($item->customFieldItemsKey['requested_on']->value->date)); ?>
            </td>
            <td class="<?php echo $class; ?>">
                <?php echo '<svg display="none" focusable="false" class="icon  u-space--left--xs" aria-hidden="true"><use xlink:href="#icon-' . $item->status_key . '"></use></svg> &ensp;' . $item->status; ?>
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
