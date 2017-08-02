<div class="col-xs-12 col-md-8 col-md-offset-2">
    <div class="panel panel-default">
        
        <div class="panel-heading">
            <?php _trans('saml_sp'); ?>
        </div>
        <div class="panel-body">
        
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[saml_sp_mode]">
                            <?php _trans('saml_sp_mode'); ?>
                        </label>
                        <select name="settings[saml_sp_mode]" class="form-control simple-select"
                                id="saml_sp_mode">
                            <option value="0" <?php check_select(get_setting('saml_sp_mode'), '1'); ?>>
                                <?php _trans('saml_mode_add_btn'); ?>
                            </option>
                            <option value="1">
                                <?php _trans('saml_mode_sso_only'); ?>
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[saml_sp_entity_id]">
                            <?php _trans('saml_sp_entity_id'); ?>
                        </label>
                        <input type="text" name="settings[saml_sp_entity_id]" id="settings[saml_sp_entity_id]"
                               class="form-control" value="<?php echo get_setting('saml_sp_entity_id'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="settings[saml_sp_sso_url]">
                            <?php _trans('saml_sp_sso_url'); ?>
                        </label>
                        <input type="text" name="settings[saml_sp_sso_url]" id="settings[saml_sp_sso_url]"
                               class="form-control" value="<?php echo get_setting('saml_sp_sso_url'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="settings[saml_sp_slo_url]">
                            <?php _trans('saml_sp_slo_url'); ?>
                        </label>
                        <input type="text" name="settings[saml_sp_slo_url]" id="settings[saml_sp_slo_url]"
                               class="form-control" value="<?php echo get_setting('saml_sp_slo_url'); ?>">
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[saml_sp_contact_name]">
                            <?php _trans('saml_sp_contact_name'); ?>
                        </label>
                        <input type="text" name="settings[saml_sp_contact_name]" id="settings[saml_sp_contact_name]"
                               class="form-control" value="<?php echo get_setting('saml_sp_contact_name'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="settings[saml_sp_contact_mail]">
                            <?php _trans('saml_sp_contact_mail'); ?>
                        </label>
                        <input type="text" name="settings[saml_sp_contact_mail]" id="settings[saml_sp_contact_mail]"
                               class="form-control" value="<?php echo get_setting('saml_sp_contact_mail'); ?>">
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[saml_sp_cert_path]">
                            <?php _trans('saml_sp_cert_path'); ?>
                        </label>
                        <input type="text" name="settings[saml_sp_cert_path]" id="settings[saml_sp_cert_path]"
                               class="form-control" value="<?php echo get_setting('saml_sp_cert_path'); ?>">
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[saml_sp_key_path]">
                            <?php _trans('saml_sp_key_path'); ?>
                        </label>
                        <input type="text" name="settings[saml_sp_key_path]" id="settings[saml_sp_key_path]"
                               class="form-control" value="<?php echo get_setting('saml_sp_key_path'); ?>">
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="panel-heading">
            <?php _trans('saml_idp'); ?>
        </div>
        <div class="panel-body">
        
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[saml_idp_entity_id]">
                            <?php _trans('saml_idp_entity_id'); ?>
                        </label>
                        <input type="text" name="settings[saml_idp_entity_id]" id="settings[saml_idp_entity_id]"
                               class="form-control" value="<?php echo get_setting('saml_idp_entity_id'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="settings[saml_idp_cert]">
                            <?php _trans('saml_idp_cert'); ?>
                        </label>
                        <input type="text" name="settings[saml_idp_cert]" id="settings[saml_idp_cert]"
                               class="form-control" value="<?php echo get_setting('saml_idp_cert'); ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[saml_idp_sso_url]">
                            <?php _trans('saml_idp_sso_url'); ?>
                        </label>
                        <input type="text" name="settings[saml_idp_sso_url]" id="settings[saml_idp_sso_url]"
                               class="form-control" value="<?php echo get_setting('saml_idp_sso_url'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="settings[saml_idp_slo_url]">
                            <?php _trans('saml_idp_slo_url'); ?>
                        </label>
                        <input type="text" name="settings[saml_idp_slo_url]" id="settings[saml_idp_slo_url]"
                               class="form-control" value="<?php echo get_setting('saml_idp_slo_url'); ?>">
                    </div>
                </div>
            </div>

        </div>
        
        <div class="panel-heading">
            <?php _trans('saml_map'); ?>
        </div>
        <div class="panel-body">
        
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[saml_map_username]">
                            <?php _trans('saml_map_username'); ?>
                        </label>
                        <input type="text" name="settings[saml_map_username]" id="settings[saml_map_username]"
                               class="form-control" value="<?php echo get_setting('saml_map_username'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="settings[saml_map_mail]">
                            <?php _trans('saml_map_mail'); ?>
                        </label>
                        <input type="text" name="settings[saml_map_mail]" id="settings[saml_map_mail]"
                               class="form-control" value="<?php echo get_setting('saml_map_mail'); ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="settings[saml_map_nameid_format]">
                            <?php _trans('saml_map_nameid_format'); ?>
                        </label>
                        <input type="text" name="settings[saml_map_nameid_format]" id="settings[saml_map_nameid_format]"
                               class="form-control" value="<?php echo get_setting('saml_map_nameid_format'); ?>">
                    </div>
                </div>
            </div>

        </div>  
                
    </div>
</div>
