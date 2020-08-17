import 'dart:async';
import 'package:resold/models/product.dart';
import 'package:dio/dio.dart';

/*
* Resold service - Resold Rest specific API client
* This service is used to make authenticated resold requests
*/
class ResoldRest {

  static Config config = Config();
  static Dio dio = Dio();

  /*
   * postProduct - Post a product
   * token - Customer identification token
   * product - Product to post
   * imagePaths - List of image paths
   */
  static Future postProduct(String token, Product product, List<String> imagePaths) async {

    await config.initialized;

    FormData formData = new FormData.fromMap({
      'name': product.name,
      'price': product.price,
      'condition': product.condition,
      'topCategory': product.categoryIds.first,
      'details': product.description,
      'localGlobal': product.localGlobal,
      'latitude': product.latitude,
      'longitude': product.longitude,
      'imagePaths': imagePaths
    });

    dio.options.headers['Authorization'] = 'Bearer ${token}';
    var response = await dio.post('${config.baseUrl}/product', data: formData);

    var x = response;

    if(response.data.length > 1) {
      return response.data[1];
    } else {
      return 'Error: ' + response.data;
    }// end if response data success
  }// end function postProduct

}// end class Resold

/*
 * Config - Configuration class for Resold specific API calls
 */
class Config {
  String baseUrl;
  Map<String, String> headers = Map<String, String>();
  Future initialized;

  Config() {
    initialized = init();
  }

  init() async {
    final config = {
      'base_url': 'https://resold.us/rest/V1/resold'
    };

    baseUrl = config['base_url'];
    headers['User-Agent'] = 'Resold - Mobile Application';
  }// end function init

}// end class Config
