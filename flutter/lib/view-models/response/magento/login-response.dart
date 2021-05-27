import 'package:resold/view-models/response/abstract-response.dart';

class LoginResponse extends Response {
  String email;
  String token;

  LoginResponse({this.email, this.token, statusCode, error}) : super(statusCode: statusCode, error: error);
}