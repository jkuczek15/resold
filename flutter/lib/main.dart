import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:resold/screens/landing.dart';
import 'package:resold/screens/home.dart';

Future<void> main() async {
  // ensure flutter binding
  WidgetsFlutterBinding.ensureInitialized();
  SharedPreferences prefs = await SharedPreferences.getInstance();

  // clear shared preferences
  await prefs.clear();

  // get shared preferences
  var email = prefs.getString('email');
  var token = prefs.getString('token');

  // run the app
  runApp(MaterialApp(home: isLoggedIn(email, token) ? Home(email, token) : Landing()));
}

bool isLoggedIn(String email, String token) {
  return email != null && token != null;
}
