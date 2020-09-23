import 'package:resold/view-models/response/abstract-response.dart';

class LoginResponse extends Response {
  String email;
  String token;

  LoginResponse({this.email, this.token, status, error}) : super(status: status, error: error);
}