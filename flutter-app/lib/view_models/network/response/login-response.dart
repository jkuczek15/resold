import 'package:resold/view_models/network/response/response.dart';

class LoginResponse extends Response {
  String email;
  String token;

  LoginResponse({this.email, this.token, status, error}) : super(status: status, error: error);
}