<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

// These are for the Joomla way of doing things. You may not need them if you're using other data
// validation libraries:
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

$form_id = 'designrequests_form';
$route   = JRoute::_(JUri::current());
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        var form = document.getElementById('<?php echo $form_id; ?>');
        var $form = jQuery(<?php echo $form_id; ?>);
        if (task == 'designrequest.cancel') {
            Joomla.submitform(task, form);
        } else if (document.formvalidator.isValid(form)) {
            Joomla.submitform(task, form);
            return false;

            // This is how an ajax save may work. It's not ideal as-is because the form will reload
            // but the item won't be checked out any longer. We don't want this.
            // However, if you were loading the form in a modal, closing the modal on success would
            // make this ok.

            var data = $form.serialize();

            jQuery.ajax({
                method: "POST",
                url: "<?php echo $route; ?>",
                data: data + '&task=' + task + '&ajax=1',
                processData: false
            })
            .done(function( return_string ) {
                console.log("Data Saved: " + return_string);
                var return_json = JSON.parse(return_string);
                console.log(return_json);
                Joomla.renderMessages(return_json.messages);
            });

            /*console.dir({
                method: "POST",
                url: "<?php echo $route; ?>",
                data: data + '&ajax=1'
            });*/
        }
        return false;
    }
</script>

<form action="<?php echo $route; ?>" method="post" name="designrequests_form" id="<?php echo $form_id; ?>" class="">
<?php
$fieldsets             = $this->form->getFieldsets();
$inputs_fieldset       = $this->form->getFieldset('inputs');
$inputs_fieldset_info  = $fieldsets['inputs'];
$inputs_fieldset_class = isset($inputs_fieldset_info->class)
                       ? ' class="' . $inputs_fieldset_info->class . '"'
                       : '';
$hidden_inputs = array();
?>
    <fieldset<?php echo $inputs_fieldset_class; ?>>
        <legend><?php echo JText::_($inputs_fieldset_info->label); ?></legend>
        <ol class="ff-width-100--45--66-666  u-space--left--auto  u-space--right--auto">
            <?php foreach($inputs_fieldset as $field): ?><?php if($field->type == 'Hidden'): ?>
            <?php $hidden_inputs[] = $field; ?>
            <?php elseif($field->type == 'Button'): ?>
            <li class="l-col-to-row"><span class="ff-width-100--30--25 l-col-to-row__item"></span><span class="ff-width-100--30--75 l-col-to-row__item"><?php echo $field->input;?><s/pan></li>
            <?php elseif($field->type == 'Checkbox'): ?>
            <li class="l-col-to-row"><span class="ff-width-100--30--25 l-col-to-row__item"></span><span class="ff-width-100--30--75 l-col-to-row__item"><?php echo $field->input;echo JText::_($field->label); ?></span></li>
            <?php else: ?>
            <li class="l-col-to-row"><span class="ff-width-100--30--25 l-col-to-row__item"><?php echo JText::_($field->label);?></span><span class="ff-width-100--30--75 l-col-to-row__item"><?php echo $field->input; ?></li>
            <?php endif; ?><?php endforeach; ?>
        </ol>
        <?php foreach($hidden_inputs as $field): ?>
        <?php echo $field->input;?>
        <?php endforeach; ?>
        <?php echo JHtml::_('form.token'); ?>
        <?php /* You may not need these if you're not using return value or Joomla data validation: */ ?>
        <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
        <input type="hidden" name="task" value="designrequest.save" />

    </fieldset>
<?php
$controls_fieldset = $this->form->getFieldset('controls');
?>
    <fieldset>
        <?php foreach($controls_fieldset as $field): ?><?php if($field->type == 'Button'): ?>
        <p><?php echo $field->input;?></p>
        <?php elseif($field->type == 'Checkbox'): ?>
        <p><?php echo $field->input;echo JText::_($field->label); ?></p>
        <?php else: ?>
        <p><?php echo JText::_($field->label);echo $field->input; ?></p>
        <?php endif; ?><?php endforeach; ?>
        <?php /*
        <button class="btn" type="submit"><?php echo JText::_('COM_DESIGNREQUESTS_SUBMIT_LABEL'); ?></button>
        <a class="btn" href="<?php echo JRoute::_('index.php?option=com_designrequests'); ?>"><?php echo JText::_('JCANCEL') ?></a>
        */ ?>
        <button class="btn" type="submit" onclick="return Joomla.submitbutton('designrequest.save')"><?php echo JText::_('COM_DESIGNREQUESTS_SUBMIT_LABEL'); ?></button>
        <a class="btn" href="<?php echo JRoute::_('index.php?option=com_designrequests'); ?>" onclick="return Joomla.submitbutton('designrequest.cancel')"><?php echo JText::_('JCANCEL') ?></a>
    </fieldset>
</form>
