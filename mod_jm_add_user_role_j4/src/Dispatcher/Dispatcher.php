<?php

/**
 * Provides the mod_jm_add_user_role_j4 dispatcher.
 *
 * @copyright   (C) 2024 cloudfaction.nl. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
 * @author      Maarten Blokdijk and Arend-Henk Huzen / www.cloudfaction.nl
 *
 * @since       1.0.0
 */

namespace JModules\Module\JmAddUserRoleJ4\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

// No Direct Access
\defined('_JEXEC') || die('Restricted access');

/**
 * Dispatcher class for mod_jm_add_user_role_j4
 *
 * @since  1.0.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * Three step process:
     * 1. The Dispatcher get the settings of the modules (params), pushes these to the Helper.
     * 2. In return the Dispatcher gets data from the Helper.
     * 3. The Dispather pushes this data to the selected template in de module: Advanced Tab: Layout. default or text
     *
     * @return  array
     *
     * @since   J4.2.0
     */
    protected function getLayoutData()
    {
        // Get default object from parent
        $data = parent::getLayoutData();

        // Get data from the Helper, add it to $data and push $data to the template
        $data['form_data'] = $this->getHelperFactory()->getHelper('JmAddUserRoleJ4Helper')->getData($data['params'], $this->getApplication());

        return $data;
    }
}
