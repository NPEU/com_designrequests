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



        /*
        $app = JFactory::getApplication();

        // Load state from the request.
        $pk = $app->input->getInt('id');
        $this->setState('designrequest.id', $pk);

        // Add compatibility variable for default naming conventions.
        $this->setState('form.id', $pk);

        $return = $app->input->get('return', null, 'base64');

        if (!JUri::isInternal(base64_decode($return))) {
            $return = null;
        }

        $this->setState('return_page', base64_decode($return));

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        $this->setState('layout', $app->input->getString('layout'));

        ---

        $app = Factory::getApplication();

        // Load the object state.
        $pk = $app->input->getInt('id');
        $this->setState('weblink.id', $pk);

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        $user = $app->getIdentity();

        if (!$user->authorise('core.edit.state', 'com_weblinks') && !$user->authorise('core.edit', 'com_weblinks')) {
            $this->setState('filter.state', 1);
            $this->setState('filter.archived', 2);
        }

        $this->setState('filter.language', Multilanguage::isEnabled());
        */
    }

    /**
     * Get the designrequest
     * @return object The designrequest to be displayed to the user
     */
    /*public function getItem($pk = NULL)
    {
        echo 'State<pre>'; var_dump($this->getState('designrequest.id')); echo '</pre>'; exit;
    }*/

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed    A JForm object on success, false on failure
     *
     * @since   1.6
     */
    /*public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        // To DRY use the admin form use:
        // JPATH_COMPONENT_ADMINISTRATOR . '/forms/designrequest.xml',
        // or if you need a separate site form, use:
        // JPATH_COMPONENT_SITE . '/forms/designrequest.xml',
        $form = $this->loadForm(
            'com_designrequests.form',
            JPATH_COMPONENT_SITE . '/forms/designrequest.xml',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form)) {
            $errors = $this->getErrors();
            throw new \Exception(implode("\n", $errors), 500);
        }

        return $form;
    }*/

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     *
     * We need to override this - otherwise it would take 'Form' as the $name
     */
    /*public function getTable($name = 'Designrequest', $prefix = 'administrator', $options = array())
    {
        return parent::getTable($name, $prefix, $options);
    }*/
}