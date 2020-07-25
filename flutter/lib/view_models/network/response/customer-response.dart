import 'package:resold/models/customer/customer-address.dart';
import 'package:resold/view_models/network/response/response.dart';

class CustomerResponse extends Response {

  final int id;
  final String email;
  final String password;
  final String firstName;
  final String lastName;
  final List<CustomerAddress> addresses;

  CustomerResponse({this.id, this.email, this.password, this.firstName, this.lastName, this.addresses, status, error}) : super(status: status, error: error);
}
