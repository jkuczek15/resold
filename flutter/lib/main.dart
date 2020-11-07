import 'package:flutter/material.dart';
import 'package:resold/screens/landing/landing.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/firebase.dart';
import 'package:stripe_payment/stripe_payment.dart';

Future<void> main() async {
  // ensure flutter binding
  WidgetsFlutterBinding.ensureInitialized();

  // setup Firebase
  await Firebase.configure();

  // setup Stripe
  StripePayment.setOptions(StripeOptions(
      publishableKey: 'pk_test_6QOUWv18fiwTf0QzwAzudvxK',
      merchantId: 'Test',
      androidPayMode: 'test'));

  // clear from disk
  await CustomerResponse.clear();

  // auto-login
  await CustomerResponse.save(
      CustomerResponse(email: 'joe.kuczek@gmail.com', password: 'Bigjoe3092'));
  // await CustomerResponse.save(
  //     CustomerResponse(email: 'jim.smith@gmail.com', password: 'Bigjoe3092'));

  // get from disk and login
  CustomerResponse customer = await CustomerResponse.load();

  // run the app
  runApp(MaterialApp(home: customer.isLoggedIn() ? Home(customer) : Landing()));
}
