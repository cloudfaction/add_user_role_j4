<?php
/**
 * Provides the mod_jm_add_user_role_j4 service provider.
 *
 * @copyright   (C) 2024 cloudfaction.nl. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
 * @author      Maarten Blokdijk and Arend-Henk Huzen / www.cloudfaction.nl
 *
 * @since       2.0.0
 */

// No Direct Access
\defined('_JEXEC') || die('Restricted access');

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The mod_jm_add_user_role_j4 service provider.
 *
 * @since  2.0.0
 */
return new class() implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\JModules\\Module\\JmAddUserRoleJ4'));
        $container->registerServiceProvider(new HelperFactory('\\JModules\\Module\\JmAddUserRoleJ4\\Site\\Helper'));

        $container->registerServiceProvider(new Module());
    }
};
