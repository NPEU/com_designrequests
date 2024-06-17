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
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
#use NPEU\Component\Designrequests\Site\Helper\DesignrequestHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;

/**
 * Designrequest Component Model
 */
class DesignrequestModel extends \NPEU\Component\Designrequests\Administrator\Model\DesignrequestModel {

    /**
     * @var object item
     */
    protected $item;

    protected $item_state;

    public $is_in_list_view = false;

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return    void
     * @since    2.5
     */
    protected function populateState()
    {
        #echo '<pre>'; var_dump('AlertModel'); echo '</pre>'; exit;
        $app = Factory::getApplication();

        // Somewhere the Router is casting non-numeriv strings to `1` and since Trello uses string
        // ID's we can get the correct ID from the input, so hacking around this to use the URI.
        $uri = explode('/', Uri::getInstance());
        $id = array_pop($uri);

        // The front-end also uses this model but the URL is structured differently, so hacking
        // around that problem here.
        if ($id =='edit') {
            $id = array_pop($uri);
        }
        #echo '<pre>'; var_dump($id); echo '</pre>'; exit;

        $this->setState('designrequest.id', $id);

        // Load the parameters.
        $this->setState('params', Factory::getApplication()->getParams());
        parent::populateState();
    }
}