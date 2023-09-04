<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;


/**
 * Designrequests Component Controller
 */
class DisplayController extends BaseController {
    protected $default_view = 'designrequests';

    public function display($cachable = false, $urlparams = []) {
        return parent::display($cachable, $urlparams);
    }
}