import 'package:resold/models/customer/customer-address.dart';
import 'package:resold/view-models/request/magento/login-request.dart';
import 'package:resold/view-models/response/abstract-response.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:resold/services/magento.dart';

class CustomerResponse extends Response {
  int id;
  String email;
  String password;
  String firstName;
  String lastName;
  String token;
  String deviceToken;
  int vendorId;
  final List<CustomerAddress> addresses;
  String get fullName => this.firstName + ' ' + this.lastName;

  CustomerResponse(
      {this.id,
      this.email,
      this.password,
      this.firstName,
      this.lastName,
      this.addresses,
      this.token,
      this.deviceToken,
      this.vendorId,
      statusCode,
      error})
      : super(statusCode: statusCode, error: error);

  bool isLoggedIn() {
    return token != null;
  } // end function isLoggedIn

  static Future<CustomerResponse> save(CustomerResponse response) async {
    try {
      // persist customer settings to disk
      SharedPreferences prefs = await SharedPreferences.getInstance();
      prefs.setString('email', response.email);
      prefs.setString('password', response.password);
    } catch (ex) {
      print(ex);
    }
    return response;
  } // end function save

  static Future<CustomerResponse> load() async {
    CustomerResponse response = CustomerResponse();
    try {
      // load customer settings from disk and login
      SharedPreferences prefs = await SharedPreferences.getInstance();

      // login so we get a new token each time we load the customer
      return await Magento.loginCustomer(
          LoginRequest(username: prefs.getString('email'), password: prefs.getString('password')));
    } catch (ex) {
      print(ex);
    }
    return response;
  } // end function load

  static Future<CustomerResponse> clear() async {
    try {
      // clear customer settings from disk
      SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.clear();
    } catch (ex) {
      print(ex);
    }
    return CustomerResponse();
  } // end function clear
}
