import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:resold/screens/landing.dart';
import 'package:resold/screens/home.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // check if logged in
  SharedPreferences prefs = await SharedPreferences.getInstance();
  var email = prefs.getString('email');

  // run the app
  runApp(MaterialApp(home: email == null ? Landing() : Home()));
}