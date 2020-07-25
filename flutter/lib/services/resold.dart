import 'package:http/http.dart' show Client;
import 'dart:async';
import 'dart:convert';

class Resold {

  static Client client = Client();
  static Config config = Config();

  static Future<String> getRegionId(String regionCode, String countryId) async {
    if(regionCode.isEmpty || countryId.isEmpty) {
      return 'Please enter both a region code and a country Id.';
    }

    await config.initialized;

    final response = await client.post(
        '${config.baseUrl}/region',
        headers: config.headers,
        body: <String, dynamic> { 'regionCode': regionCode, 'countryId': countryId }
    );

    if(response.statusCode == 200) {
      // success
      var json = jsonDecode(response.body.toString());
      return json['regionId'];
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return json['message'];
    }
  }
}

class Config {
  String baseUrl;
  Map<String, String> headers = Map<String, String>();
  Future initialized;

  Config() {
    initialized = init();
  }

  init() async {
    final config = {
      'base_url': 'https://resold.us/api'
    };

    baseUrl = config['base_url'];
    headers['User-Agent'] = 'Resold - Mobile Application';
  }
}
