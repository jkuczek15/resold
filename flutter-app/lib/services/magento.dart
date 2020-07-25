import 'package:http/http.dart' show Client;
import 'package:resold/view_models/network/login-response.dart';
import 'dart:async';
import 'dart:convert';

class Magento {

  static Config config = Config();
  static Client client = Client();

  static Future<String> fetchStoreConfig() async {
    print('fetchStoreConfig');
    await config.initialized;
    final response = await client.get(
      '${config.baseUrl}/V1/store/storeConfigs',
      headers: config.headers,
    );

    return response.body.toString();
  }

  static Future<LoginResponse> loginCustomer(String username, String password) async {
    if(username.isEmpty || password.isEmpty) {
      return LoginResponse (
          status: 400,
          error: 'Please enter both an email and a password.'
      );
    }

    await config.initialized;
    final response = await client.post(
      '${config.baseUrl}/customer/token',
      headers: config.headers,
      body: jsonEncode(<String, String>{
        'username': username,
        'password': password
      })
    );

    if(response.statusCode == 200) {
      // login success
      return LoginResponse(
        status: response.statusCode,
        email: username,
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
      'base_url': 'https://resold.us/rest/V1/integration',
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

