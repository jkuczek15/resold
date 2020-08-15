import 'package:http/http.dart' show Client;
import 'dart:async';
import 'dart:convert';
import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';

/*
* Resold service - Resold specific API client
*/
class Resold {

  static Client client = Client();
  static Config config = Config();

  /*
   * getRegionId - Returns region ID for customer address
   * regionCode - State code
   * countryId - Country code
   */
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
  }// end function getRegionId

  /*
   * getVendorId - Returns vendor ID for a customer
   * customerId - ID of the signed in customer
   */
  static Future<String> getVendorId(int customerId) async {
    if(customerId == null) {
      return 'Please enter a customer id.';
    }

    await config.initialized;

    final response = await client.post(
      '${config.baseUrl}/vendor',
      headers: config.headers,
      body: <String, dynamic> { 'customerId': customerId.toString() }
    );

    if(response.statusCode == 200) {
      // success
      var json = jsonDecode(response.body.toString());
      return json['vendorId'];
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return json['message'];
    }
  }// end function getVendorId

  /*
   * getCustomerIdByProduct - Returns customer ID given a product ID
   * productId - ID of the product
   */
  static Future<String> getCustomerIdByProduct(int productId) async {
    if(productId == null) {
      return 'Please enter a product id.';
    }

    await config.initialized;

    final response = await client.post(
        '${config.baseUrl}/Product/Customer',
        headers: config.headers,
        body: <String, dynamic> { 'productId': productId.toString() }
    );

    if(response.statusCode == 200) {
      // success
      var json = jsonDecode(response.body.toString());
      return json['customerId'];
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return json['message'];
    }
  }// end function getCustomerIdByProduct

  /*
   * getVendor - Returns vendor given a vendor ID
   * vendorId - ID of the vendor
   */
  static Future<Vendor> getVendor(int vendorId) async {

    await config.initialized;

    final response = await client.post(
        '${config.baseUrl}/vendor/details',
        headers: config.headers,
        body: <String, dynamic> { 'vendorId': vendorId.toString() }
    );

    if(response.statusCode == 200) {
      // success
      var json = jsonDecode(response.body.toString());
      return Vendor.fromJson(json);
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return json['message'];
    }
  }// end function getVendor

  /*
   * getProductImages - Returns product images given a product ID
   * productId - ID of product
   */
  static Future<List<String>> getProductImages(int productId) async {

    await config.initialized;

    final response = await client.get(
      '${config.baseUrl}/image/get?product_id=$productId',
      headers: config.headers
    );

    if(response.statusCode == 200) {
      // success
      var json = jsonDecode(response.body.toString()).toList();
      return json.map((value) => value['uuid']).toList().cast<String>();
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return [json['message']];
    }
  }// end function getProductImages

  /*
   * getVendorProducts - Returns products listed by a specific vendor
   * vendorId - ID of the vendor
   * type - Either for-sale or sold products
   */
  static Future<List<Product>> getVendorProducts(int vendorId, String type) async {

    await config.initialized;

    final response = await client.post(
      '${config.baseUrl}/vendor/products',
      headers: config.headers,
      body: <String, dynamic> { 'vendorId': vendorId.toString(), 'type': type }
    );

    if(response.statusCode == 200) {
      // success
      List<dynamic> vendorProducts = jsonDecode(response.body.toString()).toList();
      List<Product> products = new List<Product>();
      vendorProducts.forEach((vendorProduct) {
        products.add(Product.fromJson(vendorProduct));
      });
      return products;
    } else {
      // error
      var json = jsonDecode(response.body.toString());
      return [json['message']];
    }
  }// end function getVendorProducts

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
      'base_url': 'https://resold.us/api'
    };

    baseUrl = config['base_url'];
    headers['User-Agent'] = 'Resold - Mobile Application';
  }// end function init

}// end class Config
