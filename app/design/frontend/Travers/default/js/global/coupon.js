let hash = window.location.hash.substr(1);
let result = hash.split('&').reduce(function (result, item) {
    var parts = item.split('=');
    result[parts[0]] = parts[1];
    return result;
}, {});

if (result['coupon']) {
  document.cookie = "coupon="+result['coupon'];
}