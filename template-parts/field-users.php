<?php defined('ABSPATH') || exit; 
$asfUsers = $this->_sanitize->sanitizeIds($asfUsers);
if(!$asfUsers) return;
?>
<?php if($subtitle) { ?>
	<p class="subtitle"><?php echo esc_html($subtitle); ?></p>
<?php } ?>
<ul>
<?php 
foreach ($asfUsers as $asfUser) {
    $user = get_userdata($asfUser);
    if(!is_a($user,'WP_User')) continue;
    $userId = $this->_sanitize->sanitizeId($user->ID);
    $args = array(
        'name'  => 'default_users',
        'label' => sanitize_user($user->display_name),
        'value' => $userId,
        'info-1'  => sprintf( esc_html__('(ID: %s)','seo-file-names'), strval( $userId ) ),
        'info-2'  => sanitize_email($user->user_email),
        'checked' => $value && in_array($userId, $value) ? 'checked' : '',
        'class' => '',
    );
    include realpath(AFG_ASF_PATH.'template-parts/field-checkbox.php');
} ?>
</ul>