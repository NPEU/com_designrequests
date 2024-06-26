<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_designrequests
 *
 * @copyright   Copyright (C) NPEU 2023.
 * @license     MIT License; see LICENSE.md
 */

namespace NPEU\Component\Designrequests\Administrator\Extension;

defined('JPATH_PLATFORM') or die;


#use Joomla\CMS\Association\AssociationServiceInterface;
#use Joomla\CMS\Association\AssociationServiceTrait;
#use Joomla\CMS\Categories\CategoryServiceInterface;
#use Joomla\CMS\Categories\CategoryServiceTrait;
#use Joomla\CMS\Fields\FieldsServiceInterface;
#use NPEU\Component\Designrequests\Site\Service\TraditionalRouter;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Component\Router\RouterInterface;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseAwareTrait;
use NPEU\Component\Designrequests\Administrator\Service\HTML\AdministratorService;
use Psr\Container\ContainerInterface;

class DesignrequestsComponent extends MVCComponent implements
    RouterServiceInterface, BootableExtensionInterface
{
    use RouterServiceTrait;
    use HTMLRegistryAwareTrait;
    #use AssociationServiceTrait;
    use DatabaseAwareTrait;

    /**
     * Booting the extension. This is the function to set up the environment of the extension like
     * registering new class loaders, etc.
     *
     * We use this to register the helper file class which contains the html for displaying associations
     */
    public function boot(ContainerInterface $container)
    {
        $this->getRegistry()->register('designrequestsadministrator', new AdministratorService);
    }


    /**
     * Returns the name of the published state column in the table
     * for use by the count items function
     *
     */
    protected function getStateColumnForSection(string $section = null)
    {
        return 'state';
    }

}
