class Environment {
  String baseUrl;
  String googleMapsApiKey;
  String magentoAdminAccessToken;
  String postmatesBaseUrl;
  String postmatesApiKey;
  String postmatesCustomerId;
  String twilioAccountSid;
  String twilioAuthToken;
  String twilioNumber;
  String stripeApiPublicKey;
  String stripeAndroidPayMode;
  String stripeMerchantId;
  bool isDevelopment;
  Environment(
      {this.baseUrl,
      this.postmatesBaseUrl,
      this.googleMapsApiKey,
      this.magentoAdminAccessToken,
      this.postmatesApiKey,
      this.postmatesCustomerId,
      this.twilioAccountSid,
      this.twilioAuthToken,
      this.twilioNumber,
      this.stripeApiPublicKey,
      this.stripeMerchantId,
      this.stripeAndroidPayMode,
      this.isDevelopment});
}

final isDevelopment = true;
final env = Environment(
    isDevelopment: isDevelopment,
    baseUrl: isDevelopment ? '<your development URL>' : '<your production URL>',
    googleMapsApiKey: '<your google maps API key>',
    magentoAdminAccessToken: '<your Magento access token>',
    postmatesBaseUrl: 'https://api.postmates.com',
    postmatesApiKey: '<your Postmates API key>',
    postmatesCustomerId: '<your Postmates customer ID',
    twilioAccountSid: '<your Twilio account SID>',
    twilioAuthToken: '<your Twilio auth token>',
    twilioNumber: '<your Twilio phone number>',
    stripeApiPublicKey: '<your Stripe public key>',
    stripeAndroidPayMode: 'prod',
    stripeMerchantId: 'Test');
