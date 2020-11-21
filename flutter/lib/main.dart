import 'dart:io';
import 'package:flutter/material.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/environment.dart';
import 'package:resold/screens/landing/landing.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/state/reducers/customer-reducer.dart';
import 'package:resold/state/reducers/product-reducer.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/firebase.dart';
import 'package:stripe_payment/stripe_payment.dart';
import 'models/product.dart';
import 'models/vendor.dart';
import 'overrides/http-override.dart';

Future<void> main() async {
  // ensure flutter binding
  WidgetsFlutterBinding.ensureInitialized();

  // set custom SSL certificate to enable emulator calls
  HttpOverrides.global = new CustomHttpOverrides();

  // setup Firebase
  await Firebase.configure();

  // setup Stripe
  StripePayment.setOptions(StripeOptions(
      publishableKey: env.stripeApiPublicKey,
      merchantId: env.stripeMerchantId,
      androidPayMode: env.stripeAndroidPayMode));

  // clear from disk
  await CustomerResponse.clear();

  // auto-login
  await CustomerResponse.save(CustomerResponse(email: 'joe.kuczek@gmail.com', password: 'Resold420!'));
  // await CustomerResponse.save(CustomerResponse(email: 'jim.smith@gmail.com', password: 'Resold420!'));
  // await CustomerResponse.save(CustomerResponse(email: 'bob.smith@gmail.com', password: 'Resold420!'));

  // get from disk and login
  CustomerResponse customer = await CustomerResponse.load();

  // setup the for-sale and sold products state
  Vendor vendor = new Vendor();
  List<Product> forSaleProducts = new List<Product>();
  List<Product> soldProducts = new List<Product>();

  if (customer.isLoggedIn()) {
    // get the initial state, vendor, for-sale products and sold products
    vendor = await Resold.getVendor(customer.vendorId);
    forSaleProducts = await Resold.getVendorProducts(customer.vendorId, 'for-sale');
    soldProducts = await Resold.getVendorProducts(customer.vendorId, 'sold');
  } // end if customer is logged in

  // store app state
  Store store = Store<AppState>(
      initialState: AppState(customer, vendor, forSaleProducts, soldProducts),
      blocs: [CustomerReducer(), ProductReducer()]);

  // run the app
  runApp(StoreProvider<AppState>(
      store: store,
      child: MaterialApp(
        home: customer.isLoggedIn() ? Home() : Landing(),
      )));
} // end function main
