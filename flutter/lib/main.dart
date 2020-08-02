import 'package:flutter/material.dart';
import 'package:resold/screens/landing.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/view-models/response/customer-response.dart';

Future<void> main() async {
  // ensure flutter binding
  WidgetsFlutterBinding.ensureInitialized();

  // clear from disk
  await CustomerResponse.clear();

  // get from disk
  CustomerResponse customer = await CustomerResponse.load();

  // run the app
  runApp(MaterialApp(home: isLoggedIn(customer) ? Home(customer) : Landing()));
}

bool isLoggedIn(CustomerResponse customer) {
  return customer.id != null && customer.email != null && customer.token != null;
}
