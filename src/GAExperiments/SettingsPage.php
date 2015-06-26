<?php
namespace GAExperiments;

class SettingsPage extends WpSubPage {
    
    public function render_settings_page() {
        $option_name = $this->settings_page_properties['option_name'];
        $option_group = $this->settings_page_properties['option_group'];
        $settings_data = $this->get_settings_data();
        ?>
        <div class="wrap">
            <h2>Was It Read Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields( $option_group );
                ?>
                <table class="form-table">
                    <tr>
                        <th><label for="minHeight">Google Analytics Profile ID:</label></th>
                        <td>
                            <input type="text" id="ga_profile" name="<?php echo esc_attr( $option_name."[ga_profile]" ); ?>" value="<?php echo esc_attr( $settings_data['ga_profile'] ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="elements">Elements ID to track when scrolled into view:</label></th>
                        <td>
                            <input type="text" id="elements" name="<?php echo esc_attr( $option_name."[elements]" ); ?>" value="<?php echo esc_attr( $settings_data['elements'] ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="minHeight">Minimum height of the document to activate tracking:</label></th>
                        <td>
                            <input type="text" id="minHeight" name="<?php echo esc_attr( $option_name."[minHeight]" ); ?>" value="<?php echo esc_attr( $settings_data['minHeight'] ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>Enable scroll percentage tracking:</th>

                        <td>
                            <input type="radio" name="<?php echo esc_attr( $option_name."[percentage]" ); ?>" value="1" id="percentageYes"
                                <?php
                                if ($settings_data['percentage']) echo "checked";
                                ?>
                                ><label for="percentageYes">Yes</label>
                            <input type="radio" name="<?php echo esc_attr( $option_name."[percentage]" ); ?>" value="0" id="percentageNo"
                                <?php
                                if (0 == $settings_data['percentage']) echo "checked";
                                ?>
                                ><label for="percentageNo">No</label>

                        </td>
                    </tr>
                    <tr>
                        <th>Enable user timing:</th>

                        <td>
                            <input type="radio" name="<?php echo esc_attr( $option_name."[usertiming]" ); ?>" value="1" id="usertimingYes"
                                <?php
                                if (1 == $settings_data['usertiming']) echo "checked";
                                ?>
                                ><label for="usertimingYes">Yes</label>
                            <input type="radio" name="<?php echo esc_attr( $option_name."[usertiming]" ); ?>" value="0" id="usertimingNo"
                                <?php
                                if (0 == $settings_data['usertiming']) echo "checked";
                                ?>
                                ><label for="usertimingNo">No</label>

                        </td>
                    </tr>
                    <tr>
                        <th>Enable Scroll Depth measurement in Pixels:</th>

                        <td>
                            <input type="radio" name="<?php echo esc_attr( $option_name."[scrolldepth]" ); ?>" value="1" id="scrolldepthYes"
                                <?php
                                if (1 == $settings_data['scrolldepth']) echo "checked";
                                ?>
                                ><label for="scrolldepthYes">Yes</label>
                            <input type="radio" name="<?php echo esc_attr( $option_name."[scrolldepth]" ); ?>" value="0" id="scrolldepthNo"
                                <?php
                                if (0 == $settings_data['scrolldepth']) echo "checked";
                                ?>
                                ><label for="scrolldepthNo">No</label>
                        </td>
                    </tr>
                    <tr>
                        <th>Set events as non-interaction (GA):</th>

                        <td>
                            <input type="radio" name="<?php echo esc_attr( $option_name."[noninteraction]" ); ?>" value="1" id="noninteractionYes"
                                <?php
                                if (1 == $settings_data['noninteraction']) echo "checked";
                                ?>
                                ><label for="noninteractionYes">Yes</label>
                            <input type="radio" name="<?php echo esc_attr( $option_name."[noninteraction]" ); ?>" value="0" id="noninteractionNo"
                                <?php
                                if (0 == $settings_data['noninteraction']) echo "checked";
                                ?>
                                ><label for="noninteractionNo">No</label>
                        </td>
                    </tr>
                </table>
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Options">
            </form>
        </div>
        <?php
    }
	
	public function get_default_settings_data() {
		$defaults = array();
		$defaults['minHeight'] = '0';
        $defaults['elements'] = '';
        $defaults['percentage'] = '1';
        $defaults['usertiming'] = '1' ;
        $defaults['scrolldepth'] ='0';
        $defaults['noninteraction'] = '0';
        $defaults['ga_profile'] = '';

		return $defaults;
	}
}
