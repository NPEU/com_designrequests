<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
#use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
#use NPEU\Component\Designrequests\Site\Helper\DesignrequestHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;

/**
 * Alt Component Model
 */
class AltModel extends \NPEU\Component\Designrequests\Administrator\Model\DesignrequestsModel {

    public function getTable($name = '', $prefix = '', $options = [])
    {
        return 'designrequests';
    }

}