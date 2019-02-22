$(function() {
    var url = `${window.location.protocol}//${window.location.host}/hardware/bytag?assetTag={CODE}&topsearch=true`;
    var callbackUrl = encodeURIComponent(url);
    var barcodeScannerUrl = `http://zxing.appspot.com/scan?ret=${callbackUrl}`;
    $('#barcodescanner').attr('href', barcodeScannerUrl);
});