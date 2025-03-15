<?php
/**
 * Cron admin page
 *
 * Admin page for the cron system.
 *
 *
 * @package TFSP_Tools
 * @subpackage ADMIN
 * @since 2.2.0
 */
 ?>
<div id="tfsp_loader"></div>
<div class="wrap">
  <h2>Hosting Tools - Crons</h2>
   <div id="tfsp_response_output" class=""></div>

    <?php settings_errors(); ?>
    <h2 class="nav-tab-wrapper">
      <?php if ( !defined( 'TFSP_STAGING' ) || !TFSP_STAGING ){ ?> <a class="nav-tab" href="<?php echo admin_url() ?>tools.php?page=tfsp_cache">Cache</a><?php } ?>
      <a class="nav-tab" href="<?php echo admin_url() ?>tools.php?page=tfsp_updates">Updates</a>
      <a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>tools.php?page=tfsp_cron">Crons</a>
      <?php if (!is_ssl() && ( !defined( 'TFSP_STAGING' ) || !TFSP_STAGING )){ ?> <a class="nav-tab" href="<?php echo admin_url() ?>tools.php?page=tfsp_setup">Let's Encrypt</a> <?php } ?>
      </h2>



    <div class="content-section">
    <h3>Recent Cron List</h3>

    <h4>Crons that have been recently processed</h4>
    <?php echo $render_recent_crons; ?>
   <hr class="clear sp-divider">
