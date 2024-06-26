<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */


use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

// These are for the Joomla way of doing things. You may not need these if you're doing things
// differently.
HTMLHelper::_('behavior.keepalive');
//HTMLHelper::_('behavior.formvalidator');

$form_id = 'designrequests_form';
$route   = Route::_(Uri::current());
#echo '<pre>'; var_dump($route); echo '</pre>'; exit;
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
$inputs_fieldset       = $this->form->getFieldset('main');
$inputs_fieldset_info  = $fieldsets['main'];
$inputs_fieldset_class = isset($inputs_fieldset_info->class)
                       ? ' class="' . $inputs_fieldset_info->class . '"'
                       : '';
$hidden_inputs = array();
?>
    <fieldset<?php echo $inputs_fieldset_class; ?>>
        <legend><?php echo Text::_($inputs_fieldset_info->label); ?></legend>

        <?php foreach($inputs_fieldset as $field): ?><?php if($field->type == 'Hidden'): ?>
        <?php $hidden_inputs[] = $field; ?>
        <?php elseif($field->type == 'Button'): ?>
        <div class="l-layout  l-row">
            <div class="l-layout__inner"><span class="ff-width-100--30--25  l-box"></span><span class="ff-width-100--30--75  l-box"><?php echo $field->input;?></span></div>
        </div>
        <?php elseif($field->type == 'Checkbox'): ?>
        <div class="l-layout  l-row">
            <div class="l-layout__inner"><span class="ff-width-100--30--25  l-box"></span><span class="ff-width-100--30--75  l-box"><?php echo $field->input;echo Text::_($field->label); ?></span></div>
        </div>
        <?php else: ?>
        <div class="l-layout  l-row">
            <div class="l-layout__inner"><span class="ff-width-100--30--25  l-box"><?php echo Text::_($field->label);?></span><span class="ff-width-100--30--75  l-box"><?php echo $field->input; ?></span></div>
        </div>
        <?php endif; ?><?php endforeach; ?>

        <?php foreach($hidden_inputs as $field): ?>
        <?php echo $field->input;?>
        <?php endforeach; ?>
        <?php echo HTMLHelper::_('form.token'); ?>
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
        <p><?php echo $field->input;echo Text::_($field->label); ?></p>
        <?php else: ?>
        <p><?php echo Text::_($field->label);echo $field->input; ?></p>
        <?php endif; ?><?php endforeach; ?>
        <?php /*
        <button class="btn" type="submit"><?php echo Text::_('COM_DESIGNREQUESTS_SUBMIT_LABEL'); ?></button>
        <a class="btn" href="<?php echo Route::_('index.php?option=com_designrequests'); ?>"><?php echo Text::_('JCANCEL') ?></a>
        */ ?>
        <button class="btn" type="submit" onclick="return Joomla.submitbutton('designrequest.save')"><?php echo Text::_('COM_DESIGNREQUESTS_SUBMIT_LABEL'); ?></button>
        &emsp;
        <a class="btn" href="<?php echo Route::_('index.php?option=com_designrequests'); ?>" onclick="return Joomla.submitbutton('designrequest.cancel')"><?php echo Text::_('JCANCEL') ?></a>
    </fieldset>
</form>
