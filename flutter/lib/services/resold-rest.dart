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
  static Future<String> postProduct(
      String token, Product product, List<String> imagePaths) async {
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
      'itemSize': product.itemSize,
      'imagePaths': imagePaths
    });

    dio.options.headers['Authorization'] = 'Bearer $token';
    var response = await dio.post('${config.baseUrl}/product', data: formData);

    if (response.data.length > 1) {
      return response.data[1];
    } else {
      return 'Error: ' + response.data;
    } // end if response data success
  } // end function postProduct

  /*
   * createVendor - Create a seller account
   * token - Customer identification token
   */
  static Future<String> createVendor(String token) async {
    await config.initialized;

    dio.options.headers['Authorization'] = 'Bearer $token';
    var response = await dio.post('${config.baseUrl}/vendor');

    if (response.data.length > 1) {
      return response.data[1];
    } else {
      return 'Error: ' + response.data;
    } // end if response data success
  } // end function createVendor

  /*
   * setDeliveryId - Set the product's delivery ID
   * token - Customer identification token
   * productId - ID of the product to be delivered
   * deliverId - Postmates delivery
   */
  static Future setDeliveryId(
      String token, int productId, String deliveryId) async {
    await config.initialized;

    FormData formData = new FormData.fromMap(
        {'productId': productId, 'deliveryId': deliveryId});
    dio.options.headers['Authorization'] = 'Bearer $token';
    await dio.post('${config.baseUrl}/product/delivery', data: formData);
  } // end function setDeliveryId

} // end class Resold

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
      // 'base_url': 'https://resold.us/rest/V1/resold',
      'base_url': 'https://4c776f9f0de9.ngrok.io/rest/V1/resold'
    };

    baseUrl = config['base_url'];
    headers['User-Agent'] = 'Resold - Mobile Application';
  } // end function init

} // end class Config
