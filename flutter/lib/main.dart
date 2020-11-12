import 'dart:io';
import 'package:flutter/material.dart';
import 'package:resold/environment.dart';
import 'package:resold/screens/landing/landing.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/firebase.dart';
import 'package:stripe_payment/stripe_payment.dart';
import 'overrides/http-override.dart';

Future<void> main() async {
  // ensure flutter binding
  WidgetsFlutterBinding.ensureInitialized();

  // set custom SSL certificate to enable emulator calls
  HttpOverrides.global = new CustomHttpOverrides();

  // setup Firebase
  await Firebase.configure();

  // setup Stripe
  StripePayment.setOptions(
      StripeOptions(publishableKey: env.stripeApiPublicKey, merchantId: env.stripeMerchantId, androidPayMode: env.stripeAndroidPayMode));

  // clear from disk
  await CustomerResponse.clear();

  // auto-login
  // await CustomerResponse.save(CustomerResponse(email: 'joe.kuczek@gmail.com', password: 'Resold420!'));
  await CustomerResponse.save(CustomerResponse(email: 'jim.smith@gmail.com', password: 'Resold420!'));
  // await CustomerResponse.save(CustomerResponse(email: 'bob.smith@gmail.com', password: 'Resold420!'));

  // get from disk and login
  CustomerResponse customer = await CustomerResponse.load();

  // run the app
  runApp(MaterialApp(home: customer.isLoggedIn() ? Home(customer) : Landing()));
} // end function main
