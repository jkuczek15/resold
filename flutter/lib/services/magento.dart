import 'package:http/http.dart' show Client;
import 'dart:async';
import 'dart:convert';
import 'package:resold/view_models/network/request/login-request.dart';
import 'package:resold/view_models/network/response/login-response.dart';
import 'package:resold/view_models/network/request/customer-request.dart';
import 'package:resold/view_models/network/response/customer-response.dart';

class Magento {

  static Config config = Config();
  static Client client = Client();

  static Future<LoginResponse> loginCustomer(LoginRequest request) async {
    if(request.username.isEmpty || request.password.isEmpty) {
      return LoginResponse (
        status: 400,
        error: 'Please enter both an email and a password.'
      );
    }

    await config.initialized;

    final response = await client.post(
      '${config.baseUrl}/integration/customer/token',
      headers: config.headers,
      body: jsonEncode(<String, String>{
        'username': request.username,
        'password': request.password
      })
    );

    if(response.statusCode == 200) {
      // login success
      return LoginResponse(
        status: response.statusCode,
        email: request.username,
        token: response.body.toString()
      );
    } else {
      // login error
      var json = jsonDecode(response.body.toString());
      return LoginResponse (
        status: response.statusCode,
        error: json['message']
      );
    }
  }

  static Future<CustomerResponse> createCustomer(CustomerRequest request, String password) async {
    if(request.firstname.isEmpty || request.lastname.isEmpty) {
      return CustomerResponse (
          status: 400,
          error: 'Please enter both a first name and last name.'
      );
    }

    if(request.email.isEmpty || password.isEmpty) {
      return CustomerResponse (
          status: 400,
          error: 'Please enter both an email and a password.'
      );
    }

    await config.initialized;

    var json = jsonEncode(<String, Object>{'customer': request, 'password': password });

    final response = await client.post(
        '${config.baseUrl}/customers',
        headers: config.headers,
        body: json
    );

    if(response.statusCode == 200) {
      // sign up success
      return jsonDecode(response.body.toString());
    } else {
      // login error
      var json = jsonDecode(response.body.toString());
      return CustomerResponse (
          status: response.statusCode,
          error: json['message']
      );
    }
  }
}

class Config {
  String baseUrl;
  String accessToken;
  Map<String, String> headers = Map<String, String>();
  Future initialized;
  Map<String, dynamic> storeConfig;

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
    headers['Authorization'] = 'Bearer ${this.accessToken}';
    headers['User-Agent'] = 'Resold - Mobile Application';
    headers['Content-Type'] = 'application/json';
  }

  setStoreConfig(Map<String, dynamic> storeConfiguration) {
    print('setStoreConfig');
    storeConfig = storeConfiguration;
  }

  String getMediaUrl() {
    return '${storeConfig['base_media_url']}';
  }

  String getProductMediaUrl() {
    return '${storeConfig['base_media_url']}catalog/product';
  }
}

