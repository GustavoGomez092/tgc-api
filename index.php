<?php

/**
 * This is blank theme for using Wordpress as a Rest API and database only
 * If visited it should redirect to the statically-generated site that uses this API's content
 * Paste the URL of the static site below
 */
// get info from url params
$url = $_GET['url'];
$android_package_name = $_GET['android_package_name'];
$fallback = $_GET['fallback'];

?>

<?php if ($url && $android_package_name && $fallback): ?>
<script type="text/javascript" src="script.js"></script>
<script type="text/javascript">
var options = {
  fallback: '<?php echo $fallback; ?>',
  url: '<?php echo $url; ?>',
  android_package_name: '<?php echo $android_package_name; ?>',
};
deepLink(options);
</script>
<?php else: ?>
<script>
window.location.href = 'https://www.google.com';
</script>
<?php endif; ?>