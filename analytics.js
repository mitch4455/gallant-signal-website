// Google Analytics 4
(function (id) {
  var s = document.createElement('script');
  s.async = true;
  s.src = 'https://www.googletagmanager.com/gtag/js?id=' + id;
  document.head.appendChild(s);

  window.dataLayer = window.dataLayer || [];
  window.gtag = function () { dataLayer.push(arguments); };
  gtag('js', new Date());
  gtag('config', id);
})('G-NJLEWKBBFD');

// HubSpot tracking + chat widget (Portal 343279375, NA3)
(function (portalId) {
  var s = document.createElement('script');
  s.id = 'hs-script-loader';
  s.async = true;
  s.defer = true;
  s.src = '//js.hs-scripts.com/' + portalId + '.js';
  document.head.appendChild(s);
})('343279375');
