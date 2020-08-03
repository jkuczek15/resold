import 'package:resold/models/customer/customer-address.dart';
import 'package:resold/view-models/response/abstract-response.dart';
import 'package:shared_preferences/shared_preferences.dart';

class CustomerResponse extends Response {

  int id;
  String email;
  String password;
  String firstName;
  String lastName;
  String token;
  int vendorId;
  final List<CustomerAddress> addresses;

  CustomerResponse({this.id, this.email, this.password, this.firstName, this.lastName, this.addresses, this.token, this.vendorId, status, error})
      : super(status: status, error: error);

  bool isLoggedIn() {
    return id != null && email != null && token != null;
  }

  static Future save(CustomerResponse response) async {
    try {
      // persist customer settings to disk
      SharedPreferences prefs = await SharedPreferences.getInstance();

      prefs.setInt('customerId', response.id);
      prefs.setInt('vendorId', response.vendorId);
      prefs.setString('firstName', response.firstName);
      prefs.setString('lastName', response.lastName);
      prefs.setString('email', response.email);
      prefs.setString('password', response.password);
      prefs.setString('token', response.token);
    } catch (ex) {
      print(ex);
    }
  }

  static Future<CustomerResponse> load() async {
    CustomerResponse response = CustomerResponse();
    try {
      // persist customer settings to disk
      SharedPreferences prefs = await SharedPreferences.getInstance();

      response.id = prefs.getInt('customerId');
      response.vendorId = prefs.getInt('vendorId');
      response.firstName = prefs.getString('firstName');
      response.lastName = prefs.getString('lastName');
      response.email = prefs.getString('email');
      response.password = prefs.getString('password');
      response.token = prefs.getString('token');
    } catch (ex) {
      print(ex);
    }
    return response;
  }

  static Future clear() async {
    try {
      // clear customer settings from disk
      SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.clear();
    } catch (ex) {
      print(ex);
    }
  }
}
