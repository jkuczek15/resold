import 'dart:async';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/enums/selected-tab.dart';
import 'package:resold/environment.dart';
import 'package:resold/models/order.dart';
import 'package:resold/screens/landing/landing.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/state/reducers/customer-reducer.dart';
import 'package:resold/state/reducers/product-reducer.dart';
import 'package:resold/state/reducers/home-reducer..dart';
import 'package:resold/state/reducers/search-reducer.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/services/firebase.dart';
import 'package:stripe_payment/stripe_payment.dart';
import 'enums/sort.dart';
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

  // auto-login
  if (env.isDevelopment) {
    // clear from disk
    await CustomerResponse.clear();
    // await CustomerResponse.save(CustomerResponse(email: 'joe.kuczek@gmail.com', password: 'Resold420!'));
    await CustomerResponse.save(CustomerResponse(email: 'jim.smith@gmail.com', password: 'Resold420!'));
    // await CustomerResponse.save(CustomerResponse(email: 'bob.smith@gmail.com', password: 'Resold420!'));
  } // end if development

  // get from disk and login
  CustomerResponse customer = await CustomerResponse.load();

  // setup the for-sale and sold products state
  Vendor vendor = new Vendor();
  List<Product> forSaleProducts = new List<Product>();
  List<Product> soldProducts = new List<Product>();
  List<Order> purchasedOrders = new List<Order>();
  List<Order> soldOrders = new List<Order>();

  // initialize search state
  SearchState searchState = SearchState(
      distance: '25',
      selectedCategory: 'Cancel',
      selectedCondition: 'Cancel',
      selectedSort: Sort.newest,
      searchBarController: TextEditingController(),
      searchStream: StreamController<List<Product>>.broadcast(),
      mapStream: StreamController<List<Product>>.broadcast());

  // initialize application state
  Position currentLocation = Position();
  if (customer.isLoggedIn()) {
    currentLocation = await Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high);

    if (env.isDevelopment) {
      // override location in development mode
      currentLocation = Position(latitude: 42.052158, longitude: -87.687866);
    } // end if development

    searchState.initialProducts =
        await Search.fetchSearchProducts(searchState, currentLocation.latitude, currentLocation.longitude);
    await Future.wait([
      Resold.getVendor(customer.vendorId),
      Resold.getVendorProducts(customer.vendorId, 'for-sale'),
      Resold.getVendorProducts(customer.vendorId, 'sold'),
      Magento.getPurchasedOrders(customer.id),
      ResoldRest.getVendorOrders(customer.token)
    ]).then((data) {
      vendor = data[0];
      forSaleProducts = data[1];
      soldProducts = data[2];
      purchasedOrders = data[3];
      soldOrders = data[4];
    });
  } // end if customer is logged in

  // store app state
  Store store = Store<AppState>(
      initialState: AppState(
          selectedTab: SelectedTab.home,
          customer: customer,
          vendor: vendor,
          forSaleProducts: forSaleProducts,
          soldProducts: soldProducts,
          purchasedOrders: purchasedOrders,
          soldOrders: soldOrders,
          searchState: searchState,
          currentLocation: currentLocation),
      blocs: [CustomerReducer(), ProductReducer(), HomeReducer(), SearchReducer()]);

  // run the app
  runApp(StoreProvider<AppState>(
      store: store,
      child: MaterialApp(
        home: customer.isLoggedIn() ? Home() : Landing(),
      )));
} // end function main
