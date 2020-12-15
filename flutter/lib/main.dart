import 'dart:async';
import 'dart:io';
import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:overlay_support/overlay_support.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/environment.dart';
import 'package:resold/helpers/category-helper.dart';
import 'package:resold/helpers/condition-helper.dart';
import 'package:resold/helpers/local-global-helper.dart';
import 'package:resold/screens/landing/landing.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/state/reducers/customer-reducer.dart';
import 'package:resold/state/reducers/account-reducer.dart';
import 'package:resold/state/reducers/home-reducer.dart';
import 'package:resold/state/reducers/orders-reducer.dart';
import 'package:resold/state/reducers/search-reducer.dart';
import 'package:resold/state/reducers/sell-reducer.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:stripe_payment/stripe_payment.dart';
import 'constants/dev-constants.dart';
import 'models/product.dart';
import 'overrides/http-override.dart';

Future<void> main() async {
  // ensure flutter binding
  WidgetsFlutterBinding.ensureInitialized();

  // set custom SSL certificate to enable emulator calls
  HttpOverrides.global = new CustomHttpOverrides();

  // setup Firebase real-time db
  await Firebase.initializeApp();
  await ResoldFirebase.configure();

  // setup Stripe
  StripePayment.setOptions(StripeOptions(
      publishableKey: env.stripeApiPublicKey,
      merchantId: env.stripeMerchantId,
      androidPayMode: env.stripeAndroidPayMode));

  // auto-login/auto-post
  Position currentLocation;
  if (env.isDevelopment) {
    currentLocation = TestLocations.evanston;
    // await CustomerResponse.save(TestAccounts.seller);
    // await autoPost();
    await CustomerResponse.save(TestAccounts.seller);
  } // end if development

  CustomerResponse customer = await CustomerResponse.load();

  // run the app
  runApp(StoreProvider<AppState>(
    store: Store(
        initialState: await AppState.initialState(customer, currentLocation: currentLocation),
        blocs: [CustomerReducer(), HomeReducer(), SearchReducer(), SellReducer(), OrdersReducer(), AccountReducer()]),
    child: OverlaySupport(
        child: MaterialApp(
      home: customer.isLoggedIn() ? Home() : Landing(),
    )),
  ));
} // end function main

Future autoPost() async {
  // auto-post from the current account
  CustomerResponse customer = await CustomerResponse.load();

  // automatically post a product
  List<String> imagePaths = await Resold.uploadLocalImages(['assets/images/dev/corgi.png']);
  ResoldRest.postProduct(
      customer.token,
      Product(
          name: 'Meatballs',
          price: '1200',
          itemSize: 0,
          description: 'Doesn\'t go good with spaghetti',
          vendorId: customer.vendorId,
          latitude: TestLocations.evanston.latitude,
          longitude: TestLocations.evanston.longitude,
          categoryIds: [int.tryParse(CategoryHelper.getCategoryIdByName('Electronics'))],
          condition: ConditionHelper.getConditionIdByName('New'),
          localGlobal: LocalGlobalHelper.getLocalGlobal()),
      imagePaths);
} // end function autopost
