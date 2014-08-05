<?php

/**
 * PLugin Options Page
 *
 * @package deploy
 * @since 0.1
 *         
 */

// Helper Functions
checked( $checked, $current = true, $echo = true );
selected( $selected, $current = true, $echo = true );
disabled( $disabled, $current = true, $echo = true );

?>
<div class="wrap">
    
    <h2 id="deploy-title"><?php _e('Deploy', 'deploy'); ?></h2>
        
	<?php $options = get_option('deploy_options'); ?>

    <div class="postbox-container" style="width: 100%;">
        <div id="post-body">

            <h2 class="nav-tab-wrapper" id="deploy-nav-tab">
                <a href="#synchronise" class="nav-tab nav-tab-active">Synchronise</a>
                <a href="#settings" class="nav-tab">Settings</a>
            </h2>

            <div class="tabwrapper">

                <div id="synchronise" class="deploy-tab" style="display: block;">

                    <h3 class="title">Synchronise</h3>

                    <form action="" method="POST">

                        <?php if ($diff = deploy_get_difs()) : ?>

                        <?php $has_diff = false; ?>

                            <?php 
                                foreach ($diff as $label => $section) :
                                    if ($section) : 
                                        $has_diff = true;
                            ?>
                                <h4><?php echo $label; ?></h4>
                                <table class="widefat wp-list-table">
                                    <thead>
                                        <tr>
                                            <th class="check-column">
                                                <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                                                <input id="ch-select-all-1" class="cb-select-all" type="checkbox" checked>
                                            </th>
                                            <th>Option</th>  
                                            <th>New</th>       
                                            <th>Current</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($section as $field => $change) : ?>
                                            <tr>
                                                <td>
                                                    <label class="screen-reader-text" for="cb-select-<?php echo $field; ?>">Select <?php echo $field; ?></label>
                                                    <input id="cb-select-<?php echo $field; ?>" type="checkbox" name="deploy[]" class="cb-select" value="<?php echo $field; ?>" checked>
                                                </td>
                                                <td class="label"><?php echo $field; ?></td>
                                                <td class="new"><span><?php echo $change['file']; ?></span></td>
                                                <td class="current"><span><?php echo $change['database']; ?></span></td>
                                           </tr>
                                       <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else : ?>
                                <p><strong><?php echo $label; ?></strong> is up to date!</p>
                            <?php endif; ?>

                            <hr />

                            <?php endforeach; ?>

                        <?php endif; ?>

                        <p class="submit">
                            <?php if ($has_diff) : ?>
                                <input type="submit" id="save" class="button-primary" value="<?php _e('Synchronise','deploy'); ?>" />&nbsp;
                            <?php endif; ?>
                            <a id="reset" class="button-secondary" href="options-general.php?page=deploy&amp;reset=true"><?php _e('Reset Data from Database','deploy'); ?></a>
                        </p>

                    </form>

                </div><!--/synchronise-->

                <div id="settings" class="deploy-tab">

                    <h3 class="title">Settings</h3>

                    <form action="options.php" method="POST">

                        <?php settings_fields('deploy_plugin_options'); ?>

                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">
                                    <label for="deploy_config_dir">Configuration File Directory</label>
                                </td>
                                <td>
                                    <input name="deploy_options[config_dir]" id="deploy_config_dir" type="text" value="<?php echo ($options['config_dir']) ? $options['config_dir'] : '/wp-content/deploy'; ?>" class="regular-text code" />
                                    <p class="description">ie. <code>/wp-content/deploy</code></p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <input type="submit" class="button-primary" value="Save Changes" />
                        </p>

                    </form>


                </div><!--/settings-->

            </div><!--/tabwrapper-->

        </div>
    </div>

</div><!--/wrap-->