import 'dart:async';
import 'package:resold/environment.dart';
import 'package:resold/view-models/request/postmates/delivery-quote-request.dart';
import 'package:resold/view-models/request/postmates/delivery-request.dart';
import 'package:resold/view-models/response/postmates/delivery-quote-response.dart';
import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:resold/view-models/response/postmates/delivery-response.dart';

/*
* Resold Postmates API service - Postmates specific API client
* This service is used to make real-time requests
*/
class Postmates {
  static Config config = Config();
  static Dio dio = Dio();

  /*
  * createDeliveryQuote - Create a delivery quote
  * response - Delivery quote response data
  */
  static Future<DeliveryQuoteResponse> createDeliveryQuote(DeliveryQuoteRequest request) async {
    if (request.dropoff_address.isEmpty || request.pickup_address.isEmpty) {
      return DeliveryQuoteResponse(statusCode: 400, error: 'Please enter both a dropoff address and a pickup address.');
    }

    await config.initialized;
    dio.options.headers = config.headers;

    // setup form data for the request
    FormData formData = new FormData.fromMap(request.toJson());

    // create a delivery quote
    final response =
        await dio.post('${config.baseUrl}/v1/customers/${config.customerId}/delivery_quotes', data: formData);

    if (response.statusCode == 200) {
      // success
      // return delivery quote response data
      DeliveryQuoteResponse deliveryQuoteResponse = DeliveryQuoteResponse.fromJson(response.data);
      deliveryQuoteResponse.statusCode = response.statusCode;
      return DeliveryQuoteResponse.fromJson(response.data);
    } else {
      // login error
      var json = jsonDecode(response.data.toString());
      return DeliveryQuoteResponse(statusCode: response.statusCode, error: json['message']);
    }
  } // end function createDeliveryQuote

  /*
  * createDelivery - Create a delivery
  * response - Delivery response data
  */
  static Future<DeliveryResponse> createDelivery(DeliveryRequest request, {bool useRobot = false}) async {
    if (request.dropoff_address.isEmpty || request.pickup_address.isEmpty) {
      return DeliveryResponse(statusCode: 400, error: 'Please enter both a dropoff address and a pickup address.');
    }

    await config.initialized;
    dio.options.headers = config.headers;

    if (useRobot) {
      // setup delivery robot request (for testing)
      request.robo_undeliverable_action = 'leave_at_door';
      request.robo_pickup = '00:01:00';
      request.robo_pickup_complete = '00:04:00';
      request.robo_dropoff = '00:10:00';
      request.robo_delivered = '00:10:00';
    } // end if using the delivery robot

    // setup form data for the request
    FormData formData = new FormData.fromMap(request.toJson());

    // create a delivery quote
    final response = await dio.post('${config.baseUrl}/v1/customers/${config.customerId}/deliveries', data: formData);

    if (response.statusCode == 200) {
      // success
      // return delivery quote response data
      DeliveryResponse deliveryResponse = DeliveryResponse.fromJson(response.data);
      deliveryResponse.statusCode = response.statusCode;
      return deliveryResponse;
    } else {
      // login error
      var json = jsonDecode(response.data.toString());
      return DeliveryResponse(statusCode: response.statusCode, error: json['message']);
    } // end if successful response
  } // end function createDelivery

  /*
  * getDelivery - Get a delivery
  * response - Delivery response data
  */
  static Future<DeliveryResponse> getDelivery(String deliveryId) async {
    if (deliveryId.isEmpty) {
      return DeliveryResponse(statusCode: 400, error: 'Please enter a valid delivery ID.');
    }

    await config.initialized;
    dio.options.headers = config.headers;

    // retreive the delivery
    final response = await dio.get('${config.baseUrl}/v1/customers/${config.customerId}/deliveries/$deliveryId');

    if (response.statusCode == 200) {
      // success
      // return delivery
      DeliveryResponse deliveryResponse = DeliveryResponse.fromJson(response.data);
      deliveryResponse.statusCode = response.statusCode;
      return deliveryResponse;
    } else {
      // login error
      var json = jsonDecode(response.data.toString());
      return DeliveryResponse(statusCode: response.statusCode, error: json['message']);
    } // end if successful response
  } // end function createDelivery

} // end class Firebase

/*
 * Config - Configuration class for Resold Postmates specific API calls
 */
class Config {
  String baseUrl;
  String accessToken;
  String customerId;
  Map<String, String> headers = Map<String, String>();
  Future initialized;

  Config() {
    initialized = init();
  }

  init() async {
    final config = {
      'base_url': env.postmatesBaseUrl,
      'access_token': env.postmatesApiKey,
      'customer_id': env.postmatesCustomerId
    };

    baseUrl = config['base_url'];
    accessToken = config['access_token'];
    customerId = config['customer_id'];
    headers['Authorization'] = 'Basic ${this.accessToken}';
    headers['User-Agent'] = 'Resold - Mobile Application';
    headers['Content-Type'] = 'application/x-www-form-urlencoded';
  } // end function init

} // end class Config
