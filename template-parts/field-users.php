<?php defined('ABSPATH') || exit; 
$asfUsers = $this->_sanitize->sanitizeIds($asfUsers);
if(!$asfUsers) return;

$info2 = $args['info-2'];

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
    $userName = sanitize_user($user->display_name);
    $args['id'] = $i;
    $args['label'] = $userName;
    $args['value'] = $userId;
    $args['info-2'] = sprintf($info2, '<a href="'.esc_url( get_edit_user_link( $userId ) ).'" title="'.esc_attr($userName).'">', $userId, '</a>');
    $args['info-3'] = '<a href="mailto:'.sanitize_email($user->user_email).'" title="'.esc_attr($userName).'">'.sanitize_email($user->user_email).'</a>';
    $args['checked'] = $value && in_array($userId, $value) ? 'checked' : '';
    include realpath(AFG_ASF_PATH.'template-parts/field-checkbox.php');
    $i++;
} ?>
</ul>