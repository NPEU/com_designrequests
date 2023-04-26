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
<div class="l-box  l-box--space--edge">
    <h2><?php echo $page_title ?></h2>

    <div class="l-layout  l-row  l-gutter  l-flush-edge-gutter">
        <div class="l-layout__inner">

            <div class="l-box  ff-width-100--50--66-666">
                <div>
                    <h3>Description</h3>
                    <div>
                    <?php echo Markdown::defaultTransform($this->item->desc); ?>
                    </div>
                </div>
            </div>
            <div class="l-box  ff-width-100--50--33-333">
                <div class="d-background  l-box  l-box--space--edge">
                    <?php foreach ($this->form->getFieldsets() as $name => $fieldset): ?>
                    <div class="l-layout  l-row">
                        <dl class="l-layout__inner">
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
                            <dt class="l-box  ff-width-100--30--33-333"><?php echo $label ?></dt>
                            <dd class="l-box  l-box--space--block-end  ff-width-100--30--66-666"><?php echo $value ?></dd>
                            <?php endif; endforeach; ?>
                        </dl>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <p class="l-box l-layout  l-row  l-gutter  l-flush-edge-gutter">
                <span class="l-layout__inner  c-utilitext">
                    <span class="l-box">
                        <a href="<?php echo JRoute::_('index.php?option=com_designrequests'); ?>">
                            <svg focusable="false" aria-hidden="true" width="1.25em" height="1.25em" display="none"><use xlink:href="#icon-chevron-left"></use></svg>
                            <span>Back</span>
                        </a>
                    </span>
                    <span class="l-box u-text-align--right">
                        <a href="<?php echo JRoute::_('index.php?option=com_designrequests&task=designrequest.edit&id=' . $this->item->id); ?>" class="u-space--left--s">
                            <span><?php echo JText::_('COM_DESIGNREQUESTS_RECORDS_ACTION_EDIT'); ?></span>
                            <svg focusable="false" aria-hidden="true" width="1.25em" height="1.25em" display="none"><use xlink:href="#icon-edit"></use></svg>
                        </a>
                    </span>
                </span>
            </p>
        </div>
    </div>
</div>