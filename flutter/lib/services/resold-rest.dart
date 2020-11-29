import 'dart:async';
import 'package:resold/models/order.dart';
import 'package:resold/models/product.dart';
import 'package:dio/dio.dart';
import 'package:resold/environment.dart';

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
  static Future<String> postProduct(String token, Product product, List<String> imagePaths) async {
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

    if (response.data[0].length > 1) {
      return response.data[0]['productId'];
    } else {
      return 'Error: ' + response.data;
    } // end if response data success
  } // end function postProduct

  /*
   * getProduct - Get a product
   * token - Customer token
   * productId - Product ID
   */
  static Future<Product> getProduct(String token, int productId) async {
    await config.initialized;

    dio.options.headers['Authorization'] = 'Bearer $token';
    var response = await dio.get('${config.baseUrl}/product/$productId');

    if (response.data != null) {
      return Product.fromJson(response.data[0]);
    } else {
      return Product();
    } // end if response data success
  } // end function postProduct

  /*
   * isProductMine - Check if a product is mine
   * token - Customer token
   * productId - Product ID
   */
  static Future<bool> isProductMine(String token, int productId) async {
    await config.initialized;

    dio.options.headers['Authorization'] = 'Bearer $token';
    var response = await dio.get('${config.baseUrl}/product/mine/$productId');

    return response.data == true;
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
   * getVendorOrders - Retrieve the vendor orders
   * token - Customer identification token
   */
  static Future<List<Order>> getVendorOrders(String token) async {
    await config.initialized;

    dio.options.headers['Authorization'] = 'Bearer $token';
    var response = await dio.get('${config.baseUrl}/vendor/orders');

    List<Order> orders = new List<Order>();

    response.data.forEach((order) {
      orders.add(Order.fromJson(order));
    });

    return orders;
  } // end function getVendorOrders

  /*
   * setDeliveryId - Set the product's delivery ID
   * token - Customer identification token
   * productId - ID of the product to be delivered
   * deliveryId - Postmates delivery ID
   */
  static Future setDeliveryId(String token, int productId, String deliveryId) async {
    await config.initialized;
    FormData formData = new FormData.fromMap({'productId': productId, 'deliveryId': deliveryId});
    dio.options.headers['Authorization'] = 'Bearer $token';
    await dio.post('${config.baseUrl}/product/delivery', data: formData);
  } // end function setDeliveryId

  /*
   * setPrice - Set the product's price
   * token - Customer identification token
   * productId - ID of the product to be delivered
   * newPrice - New price to be set from the offer
   */
  static Future setPrice(String token, int productId, int newPrice) async {
    await config.initialized;
    FormData formData = new FormData.fromMap({'productId': productId, 'newPrice': newPrice});
    dio.options.headers['Authorization'] = 'Bearer $token';
    await dio.post('${config.baseUrl}/product/offer', data: formData);
  } // end function setDeliveryId

  /*
   * sendNotificationMessage - Set the product's price
   * token - Customer identification token
   * deviceToken - Device identification token
   */
  static Future sendNotificationMessage(
      String token, String deviceToken, String title, String body, String imageUrl) async {
    await config.initialized;
    FormData formData =
        new FormData.fromMap({'deviceToken': deviceToken, 'title': title, 'body': body, 'imageUrl': imageUrl});
    dio.options.headers['Authorization'] = 'Bearer $token';
    await dio.post('${config.baseUrl}/notifications/send', data: formData);
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
    final config = {'base_url': '${env.baseUrl}/rest/V1/resold'};

    baseUrl = config['base_url'];
    headers['User-Agent'] = 'Resold - Mobile Application';
  } // end function init

} // end class Config
