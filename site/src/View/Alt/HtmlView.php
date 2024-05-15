<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Site\View\Alt;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
#use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Router\Route;
#use Joomla\CMS\Plugin\PluginHelper;
#use Joomla\Event\Event;

use NPEU\Component\Designrequests\Site\View;

//require_once(dirname(__DIR__) . '/Designrequest/HtmlView.php');

/**
 * Designrequest Component HTML View
 */
class HtmlView extends \NPEU\Component\Designrequests\Site\View\Designrequests\HtmlView {

    protected $page_title = 'Alt View';


    protected function getTitle($title = '') {
        return $this->page_title;
    }

    public function display($template = null) {

        $app = Factory::getApplication();
        $menu = $app->getMenu()->getActive();
        $menu_title = $menu->title;

        $pathway = $app->getPathway();
        $pathway->addItem($this->page_title);

        $menu->title = $this->page_title;
        parent::display($template);
    }

}