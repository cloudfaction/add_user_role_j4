<div align=center>
  <?php
/**
 * Add current user to a Joomla user group
 *
 * @copyright   Copyright (C) 2022. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt, see LICENSE.php
 * @author      Maarten Blokdijk and Arend-Henk Huzen / www.cloudfaction.nl
 *
 * @version     1.0.0
 */

/*

Het template ontvangt data van de dispatcher. Dit data object wordt echter eerst uitgepakt.
De template ontvangt dus niet $data, maar $form_data, $params etc.

The template receives data from the dispatcher. However, this data object is first unpacked.
So, the template does not receive $data, but $form_data, $params, etc.

 */

// No Direct Access
defined('_JEXEC') or die;

if (!$form_data) {
    echo "No data to display.";
    return;
}

// Echo $form_data
// echo "form_data:<br />";
// print_r($form_data);

// Display the thank you message for 3 seconds.
if (isset($form_data['message'])) {
    echo $form_data['message'];
    ?>
    <script>
        setTimeout(function() {
            document.querySelector(".thankyou_message").style.display = "none";
        }, 3000);
    </script>

<?php
}?>

<form method="post" action="">
    <input type="hidden" name="referer" value="<?php echo $form_data['fullurl']; ?>">
    <input type="hidden" name="subscribe_action" value="<?php echo $form_data['subscribe_action']; ?>">
    <input type="submit" class="<?php echo $form_data['button_class']; ?>" value="<?php echo $form_data['button_text']; ?>">
</form>

<?php
// De onderstaande </div> is noodzakelijk. Anders wordt de code in de module niet goed afgesloten. Het waarom is nog een mysterie...
// The following </div> is necessary. Otherwise, the code in the module will not be properly closed. The reason why is still a mystery...
?>
</div>
