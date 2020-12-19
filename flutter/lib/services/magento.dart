import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:http/http.dart' show Client;
import 'package:money2/money2.dart';
import 'package:resold/models/customer/customer-address-region.dart';
import 'package:resold/models/customer/customer-address.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/resold-firebase.dart';
import 'dart:async';
import 'dart:convert';
import 'package:resold/view-models/request/magento/login-request.dart';
import 'package:resold/view-models/request/magento/customer-request.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/models/order.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:stripe_payment/stripe_payment.dart';
import 'package:resold/environment.dart';

/*
* Resold Magento API service - Magento specific API client
* This service is used to make Magento REST requests
*/
class Magento {
  static Config config = Config();
  static Client client = Client();
  static final FirebaseMessaging firebaseMessaging = FirebaseMessaging();

  /*
  * loginCustomer - Authenticates a customer against Magento service
  * request - LoginRequest object with information to authenticate the customer
  */
  static Future<CustomerResponse> loginCustomer(LoginRequest request) async {
    if (request.username.isEmpty || request.password.isEmpty) {
      return CustomerResponse(
          statusCode: 400, error: 'Please enter both an email and a password.');
    }

    await config.initialized;

    final response = await client.post(
        '${config.baseUrl}/integration/customer/token',
        headers: config.adminHeaders,
        body: jsonEncode(<String, String>{
          'username': request.username,
          'password': request.password
        }));

    if (response.statusCode == 200) {
      // login success
      // call another endpoint to get customer information
      var token = response.body.toString().replaceAll("\"", '');

      // get existing user information
      var customer = await getMe(token, request.password);

      // ensure the user is a seller
      if (customer.vendorId == -1) {
        var vendorId = await ResoldRest.createVendor(customer.token);
        customer.vendorId = int.tryParse(vendorId);
      } // end if user is not a seller

      return customer;
    } else {
      // login error
      var json = jsonDecode(response.body.toString());
      return CustomerResponse(
          statusCode: response.statusCode, error: json['message']);
    }
  } // end function loginCustomer

  /*
  * createCustomer - Creates a customer using Magento service
  * request - CustomerRequest object with information to create a customer
  */
  static Future<CustomerResponse> createCustomer(
      CustomerRequest request, String password, String confirmPassword) async {
    if (request.firstname.isEmpty || request.lastname.isEmpty) {
      return CustomerResponse(
          statusCode: 400,
          error: 'Please enter both a first name and last name.');
    }
    if (request.email.isEmpty || password.isEmpty) {
      return CustomerResponse(
          statusCode: 400, error: 'Please enter both an email and a password.');
    }
    if (password != confirmPassword) {
      return CustomerResponse(
          statusCode: 400,
          error: 'Confirmation password should match password.');
    }

    await config.initialized;

    var requestJson = jsonEncode(
        <String, dynamic>{'customer': request, 'password': password});

    final response = await client.post('${config.baseUrl}/customers',
        headers: config.adminHeaders, body: requestJson);

    var responseJson = jsonDecode(response.body.toString());

    if (response.statusCode == 200) {
      // sign up success
      // make another call to get the token
      return await loginCustomer(
          LoginRequest(username: request.email, password: password));
    } else {
      // sign up error
      return CustomerResponse(
          statusCode: response.statusCode, error: responseJson['message']);
    }
  } // end function createCustomer

  /*
  * updateCustomer - Creates a customer using Magento service
  * customerId - Customer Id
  * customer - Customer request object
  * existingPassword - Existing customer password
  */
  static Future<bool> updateCustomer(String token, int customerId,
      CustomerRequest customer, String existingPassword) async {
    await config.initialized;

    config.customerHeaders['Authorization'] = 'Bearer $token';

    var requestJson = jsonEncode(<String, dynamic>{
      'customer': {
        'id': customerId,
        'firstname': customer.firstname,
        'lastname': customer.lastname,
        'email': customer.email,
        'addresses': customer.addresses,
        'website_id': 1
      },
      'password': existingPassword
    });

    final response = await client.put('${config.baseUrl}/customers/$customerId',
        headers: config.adminHeaders, body: requestJson);

    if (response.statusCode == 200) {
      // success
      return true;
    } else {
      // error
      return false;
    }
  } // end function updateCustomer

  /*
  * updatePassword - Update the customer's password
  * customer - CustomerRequest object with information to create a customer
  */
  static Future<bool> updatePassword(
      String token, String existingPassword, String newPassword) async {
    await config.initialized;

    config.customerHeaders['Authorization'] = 'Bearer $token';

    var requestJson = jsonEncode(<String, dynamic>{
      'currentPassword': existingPassword,
      'newPassword': newPassword
    });

    final response = await client.put('${config.baseUrl}/customers/me/password',
        headers: config.adminHeaders, body: requestJson);

    if (response.statusCode == 200) {
      // password changed
      return true;
    } else {
      // password change failed
      return false;
    }
  } // end function updatePassword

  /*
  * getMe - Return information about the currently signed in customer
  * token - Customer API token
  * password - Customer password
  */
  static Future<CustomerResponse> getMe(String token, String password) async {
    if (token.isEmpty) {
      return CustomerResponse(
          statusCode: 400, error: 'Please enter both an email and a password.');
    }

    await config.initialized;

    config.customerHeaders['Authorization'] = 'Bearer $token';

    final response = await client.get('${config.baseUrl}/customers/me',
        headers: config.customerHeaders);

    var responseJson = jsonDecode(response.body.toString());

    if (response.statusCode == 200) {
      // return customer information
      // get vendor id
      var customerId = int.tryParse(responseJson['id'].toString());
      var vendorId = await Resold.getVendorId(customerId);
      var deviceToken = await firebaseMessaging.getToken();

      return CustomerResponse(
          statusCode: response.statusCode,
          id: customerId,
          email: responseJson['email'].toString(),
          password: password,
          firstName: responseJson['firstname'].toString(),
          lastName: responseJson['lastname'].toString(),
          addresses: [CustomerAddress.fromMap(responseJson['addresses'])],
          token: token,
          deviceToken: deviceToken,
          vendorId: int.tryParse(vendorId));
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return CustomerResponse(
          statusCode: response.statusCode, error: json['message']);
    }
  } // end function getMe

  /*
  * createOrder - Return the order ID for the customer
  * token - Customer API token
  * shippingAddress - Customer shipping address
  * product - Product to be added to the cart
  * stripeToken - Stripe payment token
  * deliveryFee - Delivery fee
  */
  static Future<String> createOrder(
      String token,
      CustomerAddress shippingAddress,
      Product product,
      Token stripeToken,
      Money deliveryFee) async {
    await config.initialized;

    config.customerHeaders['Authorization'] = 'Bearer $token';

    var response = await client.post('${config.baseUrl}/carts/mine',
        headers: config.customerHeaders);

    if (response.statusCode != 200) {
      return response.body;
    }

    String responseText = response.body.toString().replaceAll("\"", "");

    // get the cart ID
    int cartId = int.tryParse(responseText);

    // setup the cart item request
    String requestJson = jsonEncode(<String, dynamic>{
      'cartItem': {'sku': product.sku, 'qty': 1, 'quote_id': cartId}
    });
    // add the product to the cart
    response = await client.post('${config.baseUrl}/carts/mine/items',
        headers: config.customerHeaders, body: requestJson);

    if (response.statusCode != 200) {
      return response.body;
    }

    // setup shipping estimate request
    Map<String, dynamic> address = shippingAddress.toJson();
    CustomerAddressRegion region = address['region'];
    address.remove('region');
    address.remove('defaultBilling');
    address.remove('defaultShipping');
    address['region_id'] = region.regionId;
    address['region_code'] = region.regionCode;
    address['same_as_billing'] = 1;
    requestJson = jsonEncode(<String, dynamic>{'address': address});

    // estimate shipping methods
    response = await client.post(
        '${config.baseUrl}/carts/mine/estimate-shipping-methods',
        headers: config.customerHeaders,
        body: requestJson);

    if (response.statusCode != 200) {
      return response.body;
    }

    responseText = response.body.toString();

    // setup shipping information request
    requestJson = jsonEncode(<String, dynamic>{
      'addressInformation': {
        'shipping_address': address,
        'billing_address': address,
        'shipping_carrier_code': 'flatrate',
        'shipping_method_code': 'flatrate'
      }
    });

    // set shipping information
    response = await client.post(
        '${config.baseUrl}/carts/mine/shipping-information',
        headers: config.customerHeaders,
        body: requestJson);

    if (response.statusCode != 200) {
      return response.body;
    }

    // setup payment information request
    requestJson = jsonEncode(<String, dynamic>{
      'paymentMethod': {
        'method': 'stripe',
        'additional_data': {
          'token': stripeToken.tokenId,
          'cc_saved': stripeToken.tokenId
        }
      },
      'delivery_fee':
          deliveryFee.toString().replaceAll('\$', '').replaceAll('.', ''),
      'billing_address': address
    });

    // set payment information and create order
    response = await client.post(
        '${config.baseUrl}/carts/mine/payment-information',
        headers: config.customerHeaders,
        body: requestJson);

    if (response.statusCode != 200) {
      return response.body;
    }

    responseText = response.body.toString();

    return responseText.replaceAll("\"", "");
  } // end function getMe

  /*
  * getPurchasedOrders - Returns a list of purchased orders for the customer
  * customerId - ID of the customer
  */
  static Future<List<Order>> getPurchasedOrders(int customerId) async {
    await config.initialized;

    final response = await client.get(
        '${config.baseUrl}/orders?searchCriteria[filterGroups][0][filters][0][field]=customer_id&searchCriteria[filterGroups][0][filters][0][value]=$customerId&searchCriteria[filterGroups][0][filters][0][condition_type]==',
        headers: config.adminHeaders);

    if (response.statusCode == 200) {
      // success
      dynamic json = jsonDecode(response.body.toString());
      List<dynamic> items = json['items'].toList();

      List<Order> orders = new List<Order>();
      items.forEach((item) {
        orders.add(Order.fromJson(item));
      });
      return orders;
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return [json['message']];
    }
  } // end function getPurchasedOrders

  /*
  * getCustomerAddressById - Get a particular customer by ID
  * customerId - Customer Id
  */
  static Future<CustomerResponse> getCustomerById(int customerId) async {
    await config.initialized;

    final response = await client.get(
      '${config.baseUrl}/customers/$customerId',
      headers: config.adminHeaders,
    );

    dynamic responseJson = jsonDecode(response.body.toString());
    if (response.statusCode == 200) {
      // get the vendor ID for this customer
      String vendorId = await Resold.getVendorId(customerId);

      // get the device token for this customer (for push notifications)
      String deviceToken = await ResoldFirebase.getDeviceToken(customerId);

      return CustomerResponse(
          statusCode: response.statusCode,
          id: customerId,
          email: responseJson['email'].toString(),
          firstName: responseJson['firstname'].toString(),
          lastName: responseJson['lastname'].toString(),
          addresses: [CustomerAddress.fromMap(responseJson['addresses'])],
          vendorId: int.tryParse(vendorId),
          deviceToken: deviceToken);
    } else {
      return CustomerResponse(
          statusCode: response.statusCode, error: responseJson['message']);
    }
  } // end function getCustomerAddressById

  /*
  * getOrderById - Get an order by ID
  * orderId - Order ID
  */
  static Future<Order> getOrderById(int orderId) async {
    await config.initialized;

    final response = await client.get(
      '${config.baseUrl}/orders/$orderId',
      headers: config.adminHeaders,
    );

    dynamic responseJson = jsonDecode(response.body.toString());
    if (response.statusCode == 200) {
      return Order.fromJson(responseJson);
    } else {
      return Order();
    }
  } // end function getOrderById

  /*
  * deleteCustomer - deletes a customer using Magento service
  * request - CustomerRequest object with information to delete a customer
  */
  static Future<bool> deleteCustomer(int customerId) async {
    await config.initialized;

    final response = await client.delete(
      '${config.baseUrl}/customers/$customerId',
      headers: config.adminHeaders,
    );

    if (response.statusCode == 200) {
      return Future<bool>.value(true);
    } else {
      return Future<bool>.value(false);
    }
  } // end function deleteCustomer

  /*
  * deleteProduct - deletes a product using Magento service
  * productSku - SKU of the product
  */
  static Future<bool> deleteProduct(String productSku) async {
    await config.initialized;

    final response = await client.delete(
      '${config.baseUrl}/products/$productSku',
      headers: config.adminHeaders,
    );

    if (response.statusCode == 200) {
      return Future<bool>.value(true);
    } else {
      return Future<bool>.value(false);
    }
  } // end function product

  /*
  * forgotPassword - Send a forgot password email
  * email - Customer email
  */
  static Future<bool> forgotPassword(String email) async {
    await config.initialized;

    var requestJson = jsonEncode(<String, dynamic>{
      'email': email,
      'template': 'email_reset',
      'websiteId': 1
    });

    final response = await client.put('${config.baseUrl}/customers/password',
        headers: config.adminHeaders, body: requestJson);

    if (response.statusCode == 200) {
      return Future<bool>.value(true);
    } else {
      return Future<bool>.value(false);
    }
  } // end function product

} // end class Magento

/*
 * Config - Configuration class for Resold Magento specific API calls
 */
class Config {
  String baseUrl;
  String accessToken;
  Map<String, String> adminHeaders = Map<String, String>();
  Map<String, String> customerHeaders = Map<String, String>();
  Future initialized;

  Config() {
    initialized = init();
  }

  init() async {
    final config = {
      'base_url': '${env.baseUrl}/rest/V1',
      'access_token': env.magentoAdminAccessToken
    };

    baseUrl = config['base_url'];
    accessToken = config['access_token'];
    adminHeaders['Authorization'] = 'Bearer ${this.accessToken}';
    adminHeaders['User-Agent'] =
        customerHeaders['User-Agent'] = 'Resold - Mobile Application';
    adminHeaders['Content-Type'] =
        customerHeaders['Content-Type'] = 'application/json';
  } // end function init

} // end class Config
