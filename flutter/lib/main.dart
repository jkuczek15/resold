import 'package:flutter/material.dart';
import 'package:resold/screens/landing/landing.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/view-models/response/customer-response.dart';
import 'package:resold/services/firebase.dart';

Future<void> main() async {
  // ensure flutter binding
  WidgetsFlutterBinding.ensureInitialized();

  // setup Firebase
  await Firebase.configure();

  // clear from disk
//  await CustomerResponse.clear();

  // get from disk
  CustomerResponse customer = await CustomerResponse.load();

  // run the app
  runApp(MaterialApp(home: customer.isLoggedIn() ? Home(customer) : Landing()));
}

