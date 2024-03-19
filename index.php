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
<script type="text/javascript">
function deepLink(options) {
  var fallback = options.fallback || '';
  var url = options.url || '';
  var iosStoreLink = options.ios_store_link;
  var androidPackageName = options.android_package_name;
  var playStoreLink =
    'https://play.google.com/store/apps/details?id=' + androidPackageName;
  var ua = window.navigator.userAgent;

  // split the first :// from the url string
  var split = url.split(/:\/\/(.+)/);
  var scheme = split[0];
  var path = split[1] || '';

  var urls = {
    deepLink: url,
    iosStoreLink: iosStoreLink,
    android_intent: 'intent://' +
      path +
      '#Intent;scheme=' +
      scheme +
      ';package=' +
      androidPackageName +
      ';end;',
    playStoreLink: playStoreLink,
    fallback: fallback,
  };

  var isMobile = {
    android: function() {
      return /Android/i.test(ua);
    },
    ios: function() {
      return /iPhone|iPad|iPod/i.test(ua);
    },
  };

  // fallback to the application store on mobile devices
  if (isMobile.ios() && urls.deepLink && urls.iosStoreLink) {
    iosLaunch();
  } else if (isMobile.android() && androidPackageName) {
    androidLaunch();
  } else {
    window.location = urls.fallback;
  }

  function launchWekitApproach(url, fallback) {
    document.location = url;
    setTimeout(function() {
      document.location = fallback;
    }, 250);
  }

  function launchIframeApproach(url, fallback) {
    var iframe = document.createElement('iframe');
    iframe.style.border = 'none';
    iframe.style.width = '1px';
    iframe.style.height = '1px';
    iframe.onload = function() {
      document.location = url;
    };
    iframe.src = url;

    window.onload = function() {
      document.body.appendChild(iframe);

      setTimeout(function() {
        window.location = fallback;
      }, 25);
    };
  }

  function iosLaunch() {
    // chrome and safari on ios >= 9 don't allow the iframe approach
    if (
      ua.match(/CriOS/) ||
      (ua.match(/Safari/) && ua.match(/Version\/(9|10|11|12)/))
    ) {
      launchWekitApproach(urls.deepLink, urls.iosStoreLink || urls.fallback);
    } else {
      launchIframeApproach(urls.deepLink, urls.iosStoreLink || urls.fallback);
    }
  }

  function androidLaunch() {
    if (ua.match(/Chrome/) || (ua.match(/Firefox/))) {
      launchWekitApproach(urls.deepLink, urls.playStoreLink || urls.fallback);
    } else {
      launchIframeApproach(url, urls.playStoreLink || urls.fallback);
    }
  }
}

// expose module so it can be required later in tests
if (typeof module !== 'undefined') {
  module.exports = deepLink;
}
</script>
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
window.location.href = 'https://play.google.com/store/apps/details?id=com.victorstein.tcg';
</script>
<?php endif; ?>
