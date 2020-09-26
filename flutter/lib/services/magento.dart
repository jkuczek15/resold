import 'package:http/http.dart' show Client;
import 'package:resold/models/customer/customer-address.dart';
import 'dart:async';
import 'dart:convert';
import 'package:resold/view-models/request/magento/login-request.dart';
import 'package:resold/view-models/request/magento/customer-request.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/models/order.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/services/resold-rest.dart';

/*
* Resold Magento API service - Magento specific API client
* This service is used to make Magento REST requests
*/
class Magento {

  static Config config = Config();
  static Client client = Client();

  /*
  * loginCustomer - Authenticates a customer against Magento service
  * request - LoginRequest object with information to authenticate the customer
  */
  static Future<CustomerResponse> loginCustomer(LoginRequest request) async {
    if (request.username.isEmpty || request.password.isEmpty) {
      return CustomerResponse(
          status: 400,
          error: 'Please enter both an email and a password.'
      );
    }

    await config.initialized;

    final response = await client.post(
        '${config.baseUrl}/integration/customer/token',
        headers: config.adminHeaders,
        body: jsonEncode(<String, String>{
          'username': request.username,
          'password': request.password
        })
    );

    if (response.statusCode == 200) {
      // login success
      // call another endpoint to get customer information
      var token = response.body.toString().replaceAll("\"", '');

      // get existing user information
      var customer = await getMe(token, request.password);

      // ensure the user is a seller
      if(customer.vendorId == -1) {
        var vendorId = await ResoldRest.createVendor(customer.token);
        customer.vendorId = int.tryParse(vendorId);
      }// end if user is not a seller

      return customer;
    } else {
      // login error
      var json = jsonDecode(response.body.toString());
      return CustomerResponse(
          status: response.statusCode,
          error: json['message']
      );
    }
  }// end function loginCustomer

  /*
  * createCustomer - Creates a customer using Magento service
  * request - CustomerRequest object with information to create a customer
  */
  static Future<CustomerResponse> createCustomer(CustomerRequest request,
      String password, String confirmPassword) async {
    if (request.firstname.isEmpty || request.lastname.isEmpty) {
      return CustomerResponse(
          status: 400,
          error: 'Please enter both a first name and last name.'
      );
    }
    if (request.email.isEmpty || password.isEmpty) {
      return CustomerResponse(
          status: 400,
          error: 'Please enter both an email and a password.'
      );
    }
    if (password != confirmPassword) {
      return CustomerResponse(
          status: 400,
          error: 'Confirmation password should match password.'
      );
    }

    await config.initialized;

    var requestJson = jsonEncode(
        <String, dynamic>{'customer': request, 'password': password});

    final response = await client.post(
        '${config.baseUrl}/customers',
        headers: config.adminHeaders,
        body: requestJson
    );

    var responseJson = jsonDecode(response.body.toString());

    if (response.statusCode == 200) {
      // sign up success
      // make another call to get the token
      return await loginCustomer(LoginRequest(
          username: request.email,
          password: password
      ));
    } else {
      // sign up error
      return CustomerResponse(
          status: response.statusCode,
          error: responseJson['message']
      );
    }
  }// end function createCustomer

  /*
  * getMe - Return information about the currently signed in customer
  * token - Customer API token
  * password - Customer password
  */
  static Future<CustomerResponse> getMe(String token, String password) async {
    if (token.isEmpty) {
      return CustomerResponse(
        status: 400,
        error: 'Please enter both an email and a password.'
      );
    }

    await config.initialized;

    config.customerHeaders['Authorization'] = 'Bearer ${token}';

    final response = await client.get(
      '${config.baseUrl}/customers/me',
      headers: config.customerHeaders
    );

    var responseJson = jsonDecode(response.body.toString());

    if (response.statusCode == 200) {
      // return customer information
      // get vendor id
      var customerId = int.tryParse(responseJson['id'].toString());
      var vendorId = await Resold.getVendorId(customerId);

      return CustomerResponse(
        status: response.statusCode,
        id: customerId,
        email: responseJson['email'].toString(),
        password: password,
        firstName: responseJson['firstname'].toString(),
        lastName: responseJson['lastname'].toString(),
        addresses: [CustomerAddress.fromMap(responseJson['addresses'])],
        token: token,
        vendorId: int.tryParse(vendorId)
      );
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return CustomerResponse(
          status: response.statusCode,
          error: json['message']
      );
    }
  }// end function getMe

  /*
  * getPurchasedOrders - Returns a list of orders for the customer
  * customerId - ID of the customer
  */
  static Future<List<Order>> getPurchasedOrders(int customerId) async {
    await config.initialized;

    final response = await client.get(
        '${config
            .baseUrl}/orders?searchCriteria[filterGroups][0][filters][0][field]=customer_id&searchCriteria[filterGroups][0][filters][0][value]=$customerId&searchCriteria[filterGroups][0][filters][0][condition_type]==',
        headers: config.adminHeaders
    );

    if (response.statusCode == 200) {
      // success
      var json = jsonDecode(response.body.toString());
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
  }// end function getPurchasedOrders

  /*
  * getCustomerById - Get a particular customer by ID
  * customerId - Customer Id
  */
  static Future<CustomerAddress> getCustomerAddressById(int customerId) async {

    await config.initialized;

    final response = await client.get(
        '${config.baseUrl}/customers/$customerId',
        headers: config.adminHeaders,
    );

    var responseJson = jsonDecode(response.body.toString());

    if (response.statusCode == 200) {
      return CustomerAddress.fromMap(responseJson['addresses']);
    } else {
      return CustomerAddress();
    }
  }// end function createCustomer

}// end class Magento

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
      'base_url': 'https://resold.us/rest/V1',
      'access_token': 'frlf1x1o9edlk8q77reqmfdlbk54fycl'
    };

    baseUrl = config['base_url'];
    accessToken = config['access_token'];
    adminHeaders['Authorization'] = 'Bearer ${this.accessToken}';
    adminHeaders['User-Agent'] = customerHeaders['User-Agent'] = 'Resold - Mobile Application';
    adminHeaders['Content-Type'] = customerHeaders['User-Agent'] = 'application/json';
  }// end function init

}// end class Config

