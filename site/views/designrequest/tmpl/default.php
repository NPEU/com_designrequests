<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2021.
 * @license     MIT License; see LICENSE.md
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/vendor/autoload.php';

use \Michelf\Markdown;

// Set page title
$page_title = $this->item->name;

$skip = array(
    'id',
    'name',
    'desc'
);
?>
<h2><?php echo $page_title ?></h2>

<div class="l-col-to-row--flush-edge-gutters">
    <div class="l-col-to-row  l-col-to-row--gutter">

        <div class="ff-width-100--50--66-666 l-col-to-row__item">
            <div class="d-bands  u-padding">
                <h3>Description</h3>
                <div>
                <?php echo Markdown::defaultTransform($this->item->desc); ?>
                </div>
            </div>
        </div>
        <div class="ff-width-100--50--33-333 l-col-to-row__item">
            <div class="d-background  u-padding">
                <?php foreach ($this->form->getFieldsets() as $name => $fieldset): ?>
                <dl>
                    <?php foreach ($this->form->getFieldset($name) as $field): if(!in_array($field->fieldname, $skip)): ?>
                    <?php
                        $label = JText::_($field->getAttribute('label'));
                        $value = false;
                        $key   = $field->fieldname;
                        #echo $label . ' ' . $key;
                        if (isset($this->item->$key)) {
                            $value = $this->item->$key;
                        } elseif (isset($this->item->customFieldItemsKey[$field->fieldname])) {
                            $value = $this->item->customFieldItemsKey[$field->fieldname]->realvalue;
                        } else {
                            continue;
                        }

                        if ($field->getAttribute('dateformat', false)) {
                            $value = date($field->getAttribute('dateformat'), strtotime($value));
                        }
                        if ($field->getAttribute('stringsearch', false) && $field->getAttribute('stringreplace', false)) {
                            $search  = html_entity_decode($field->getAttribute('stringsearch'));
                            $replace = html_entity_decode($field->getAttribute('stringreplace'));
                            $value = preg_replace('#' . $search . '#', $replace, $value);
                            $value = Markdown::defaultTransform($value);
                            $value = trim(preg_replace(array('#^<p>#', '#</p>$#'), '', $value));
                        }
                    ?>
                    <dt><?php echo $label ?></dt>
                    <dd><?php echo $value ?></dd>
                    <?php endif; endforeach; ?>
                </dl>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<p class="c-utilitext  u-text-size--medium">
    <a href="<?php echo JRoute::_('index.php?option=com_designrequests'); ?>">
        <svg display="none" focusable="false" class="icon" aria-hidden="true"><use xlink:href="#icon-chevron-left"></use></svg>
        <span>Back</span>
    </a>
    
    <a href="<?php echo JRoute::_('index.php?option=com_designrequests&task=designrequest.edit&id=' . $this->item->id); ?>" class="u-space--left--s">
        <span><?php echo JText::_('COM_DESIGNREQUESTS_RECORDS_ACTION_EDIT'); ?></span>
        <svg display="none" focusable="false" class="icon  u-space--left--xs" aria-hidden="true"><use xlink:href="#icon-edit"></use></svg>
    </a>
</p>