<?php
/**
 * Helper Class for mod_jm_add_user_role_j4.
 *
 * @copyright   (C) 2024 cloudfaction.nl. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
 * @author      Maarten Blokdijk and Arend-Henk Huzen / www.cloudfaction.nl
 *
 * @since       1.0.0
 */

/*
Table of contents
---------------------------------------------------------------------
getData
hasUserRole
addUserToGroup
removeUserFromGroup
---------------------------------------------------------------------
 */

namespace JModules\Module\JmAddUserRoleJ4\Site\Helper;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;

// No Direct Access
defined('_JEXEC') || die('Restricted access');

session_start();

/**
 * Helper for mod_jm_add_user_role_j4
 *
 * @since  1.0.0
 */
class JmAddUserRoleJ4Helper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Retrieve form_data for list
     *
     * @param   Registry        $params  The module parameters.
     * @param   SiteApplication $app     The application.
     *
     * @return  string
     *
     * @since   1.0.0
     */
    public function getData(Registry $params, SiteApplication $app): array
    {
        if (!$app instanceof SiteApplication) {
            return '';
        }

        // Get URL and add to $form_data
        $form_data['fullurl'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        // Get User object
        $user = $app->getIdentity();

        // Check if user already has the role.
        // Get number of roles
        $userid = $user->id;
        // $role_id = $params->get('jm_group_id');
        $role_id = $params['jm_group_id'];
        $number_of_roles = $this->hasUserRole($userid, $role_id);

        $app = Factory::getApplication();
        $post = $app->input->post;
        $form_data['post'] = $post->getArray();

        // Get session variable
        $session = Factory::getApplication()->getSession();

        // Check is session variable is set
        if ($session->get('thankyou_text')) {
            $thankyou_message = $session->get('thankyou_text');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if ($_POST['subscribe_action'] === 'unsubscribe') {
                // Remove user from group
                // $this->removeUserFromGroup($app->getIdentity()->id, $params->get('jm_group_id'));  // This of coourse also works.
                $this->removeUserFromGroup($app->getIdentity()->id, $params['jm_group_id']);

                // $thankyou_message = $params['jm_thankyou_text_unsubscribe'];
                $thankyou_message = 'Testbericht';
                $session->set('thankyou_text', $params['jm_thankyou_text_unsubscribe']);

                // Redirect to the same page or a different one
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;

            } elseif ($_POST['subscribe_action'] === 'subscribe') {
                // } else {
                // Add user to group
                // $this->addUserToGroup($app->getIdentity()->id, $params->get('jm_group_id'));  // This of coourse also works.
                $this->addUserToGroup($app->getIdentity()->id, $params['jm_group_id']);

                $session->set('thankyou_text', $params['jm_thankyou_text_subscribe']);
                $thankyou_message = $params['jm_thankyou_text_subscribe'];

                // Redirect to the same page or a different one
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;

            } else {
                // Error
                $thankyou_message = 'Er is een fout opgetreden bij het verwerken van het formulier<br />Waarschuw de beheerder van de website.';

            }

        } else {

            // SHOW FORM
            // if $count == 0 then show subscribe button
            // if $count == 1 then show unsubscribe button
            // if $count > 1 then show error message

            if ($number_of_roles == 0) {
                // User is not subscribed.
                // Offer to subscribe
                $form_data['button_text'] = $params['jm_button_text_subscribe'];
                // $form_data['thankyou_text'] = $params['jm_thankyou_text_unsubscribe'];
                $form_data['button_class'] = $params['jm_button_class_subscribe'];
                $form_data['subscribe_action'] = 'subscribe';

                // After showing the message, clear the session variable
                $session->clear('thankyou_text');

            } elseif ($number_of_roles == 1) {
                // User is subscribed.
                // Offer to unsubscribe
                $form_data['button_text'] = $params['jm_button_text_unsubscribe'];
                // $form_data['thankyou_text'] = $params['jm_thankyou_text_subscribe'];
                $form_data['button_class'] = $params['jm_button_class_unsubscribe'];
                $form_data['subscribe_action'] = 'unsubscribe';

                // After showing the message, clear the session variable
                $session->clear('thankyou_text');

            } else {
                // Error
                $form_data['thankyou_text'] = 'Er is een fout opgetreden bij het verwerken van het formulier<br />Waarschuw de beheerder van de website.';
            }

        }

        // Set styled message
        if (isset($thankyou_message)) {
            $form_data['message'] = <<<HTML
                <div class="thankyou_message">
                    {$thankyou_message}<br /><br />
                </div>
            HTML;
        }

        return $form_data;

    }

    /**
     * Check is user already has the role
     *
     * @param   int  $userId    The user ID.
     * @param   int  $roleId    The role ID.
     *
     * @return  int
     *
     * @since   1.0.0
     *
     */
    private function hasUserRole($userid, $role_id): int
    {
        // Check is user already has the role: record count is 1.
        // Expect a single result: user_id and group_id

        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->quoteName('#__user_usergroup_map'))
            ->where($db->quoteName('user_id') . ' = :user_id')
            ->where($db->quoteName('group_id') . ' = :group_id');
        $query->bind(':user_id', $userid);
        $query->bind(':group_id', $role_id);
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    /**
     * Add user to group
     *
     * @param   int  $userid    The user ID.
     * @param   int  $role_id   The role ID.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function addUserToGroup($userid, $role_id)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('user_id'),
            $db->quoteName('group_id'),
        );
        $values = array(
            $db->quote($userid),
            $db->quote($role_id),
        );
        $query->insert($db->quoteName('#__user_usergroup_map'))
            ->columns($fields)
            ->values(implode(',', $values));
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Remove user from group
     *
     * @param   int  $userid    The user ID.
     * @param   int  $role_id   The role ID.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function removeUserFromGroup($userid, $role_id)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('user_id') . ' = :userid',
            $db->quoteName('group_id') . ' = :group_id',
        );
        $query->delete($db->quoteName('#__user_usergroup_map'))
            ->where($conditions);
        $query->bind(':userid', $userid);
        $query->bind(':group_id', $role_id);
        $db->setQuery($query);
        $db->execute();
    }

}
