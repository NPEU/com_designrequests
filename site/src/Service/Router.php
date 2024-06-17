<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Factory;
/**/use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

use NPEU\Component\Designrequests\Site\Service\CustomRouterRules;
use NPEU\Component\Designrequests\Administrator\Helper\DesignrequestsHelper;


class Router extends RouterView
{
    use MVCFactoryAwareTrait;

    private $categoryFactory;

    private $categoryCache = [];

    private $db;

    /**
     * Component router constructor
     *
     * @param   SiteApplication           $app              The application object
     * @param   AbstractMenu              $menu             The menu object to work with
     * @param   CategoryFactoryInterface  $categoryFactory  The category object
     * @param   DatabaseInterface         $db               The database object
     */
    public function __construct(SiteApplication $app, AbstractMenu $menu)
    {
        //$this->categoryFactory = $categoryFactory;
        //$this->db              = $db;
        //$this->db = \Joomla\CMS\Factory::getContainer()->get('DatabaseDriver');
        $trello_client = DesignRequestsHelper::getTrelloClient();

        //$this->attachRule(new CustomRouterRules($this));

        #$category = new RouterViewConfiguration('category');
        #$category->setKey('id')->setNestable();
        #$this->registerView($category);
        $designrequests = new RouterViewConfiguration('designrequests');
        #$designrequests->addLayout('other');
        $this->registerView($designrequests);


        $add = new RouterViewConfiguration('add');
        $add->setParent($designrequests);
        $this->registerView($add);


        $designrequest = new RouterViewConfiguration('designrequest');
        $designrequest->setKey('id')->setParent($designrequests);
        $this->registerView($designrequest);

        $edit = new RouterViewConfiguration('edit');
        $edit->setParent($designrequest);
        $this->registerView($edit);

        /*$alt = new RouterViewConfiguration('alt');
        $alt->setParent($designrequests);
        $this->registerView($alt);

        $other = new RouterViewConfiguration('other');
        $other->setParent($designrequests);
        $this->registerView($other);*/


        //$this->attachRule(new CustomRouterRules($this));

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));

        $this->attachRule(new CustomRouterRules($this));
    }

    /**
     * Method to get the id for an designrequests item from the segment
     *
     * @param   string  $segment  Segment of the designrequests to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getDesignrequestId(string $segment, array $query): bool|int
    {
        $record = $this->record_exists($segment);
        if (!empty($record) && isset($record->closed) && $record->closed != true) {
            // Default router operations don't like string-based ID's (as is the case with Trello
            // card ID's, so forcing it into the state here)
            $app   = Factory::getApplication();
            $user_state_ids = $app->setUserState('com_designrequests.edit.designrequest.id', [$segment]);

            return $segment;
        }
        return false;
    }

    /**
     * Method to get the segment(s) for a designrequests item
     *
     * @param   string  $id     ID of the designrequests to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getDesignrequestSegment(string $id, array $query): array
    {
        #echo 'getDesignrequestSegment<pre>'; var_dump($id); echo '</pre>';# exit;
        return [$id];
    }



    /**
     * Method to get the id for edit view
     *
     * @param   string  $segment  Segment of the designrequests to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getEditId(string $segment, array $query): bool|int
    {
        #echo 'getEditIdsegemnt<pre>'; var_dump($query); echo '</pre>';# exit;
        return true;
    }

    /**
     * Method to get the segment(s) for edit view
     *
     * @param   string  $id     ID of the designrequest to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     *
     * @since   4.0.0
     */
    public function getEditSegment($id, $query)
    {
        return 'edit';
    }

    /**
     * Method to get the id for add view
     *
     * @param   string  $segment  Segment of the designrequests to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getAddId(string $segment, array $query): bool|int
    {
        return true;
    }

    /**
     * Method to get the segment(s) for add view
     *
     * @param   string  $id     ID of the designrequest to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     *
     * @since   4.0.0
     */
    public function getAddSegment($id, $query)
    {
        return 'add';
    }

    /**
     * Method to check a record exists.
     *
     * @param   string     $id   The record ID
     *
     * @return  bool    Record does/does not exist.
     */
    protected function record_exists($id)
    {
        $trello_client = DesignRequestsHelper::getTrelloClient();
        return $trello_client->getCard($id);
    }
}
