import 'dart:async';
import 'dart:io';
import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:overlay_support/overlay_support.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/enums/selected-tab.dart';
import 'package:resold/environment.dart';
import 'package:resold/helpers/category-helper.dart';
import 'package:resold/helpers/condition-helper.dart';
import 'package:resold/helpers/local-global-helper.dart';
import 'package:resold/screens/landing/landing.dart';
import 'package:resold/screens/home.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/state/reducers/customer-reducer.dart';
import 'package:resold/state/reducers/product-reducer.dart';
import 'package:resold/state/reducers/home-reducer..dart';
import 'package:resold/state/reducers/search-reducer.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/state/sell-focus-state.dart';
import 'package:resold/state/sell-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:stripe_payment/stripe_payment.dart';
import 'constants/dev-constants.dart';
import 'enums/sort.dart';
import 'models/product.dart';
import 'models/vendor.dart';
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

  // auto-login
  if (env.isDevelopment) {
    // clear from disk
    await CustomerResponse.clear();
    await CustomerResponse.save(TestAccounts.buyer);
  } // end if development

  // get from disk and login
  CustomerResponse customer = await CustomerResponse.load();

  if (env.isDevelopment && customer.email.toLowerCase() == TestAccounts.buyer.email.toLowerCase()) {
    // sign in as seller
    await CustomerResponse.clear();
    await CustomerResponse.save(TestAccounts.seller);
    customer = await CustomerResponse.load();

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

    // sign in as buyer
    await CustomerResponse.clear();
    await CustomerResponse.save(TestAccounts.buyer);
    customer = await CustomerResponse.load();
  } // end if we should automatically post a product

  // setup the for-sale and sold products state
  Vendor vendor = new Vendor();
  List<Product> forSaleProducts = new List<Product>();
  List<Product> soldProducts = new List<Product>();

  // initialize search state
  SearchState searchState = SearchState(
      currentPage: 0,
      distance: '25',
      selectedCategory: 'Cancel',
      selectedCondition: 'Cancel',
      selectedSort: Sort.newest,
      textController: TextEditingController(),
      searchStream: StreamController<List<Product>>.broadcast());

  // initialize application state
  Position currentLocation = Position();
  if (customer.isLoggedIn()) {
    currentLocation = await Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high);

    if (env.isDevelopment) {
      // override location in development mode
      currentLocation = TestLocations.evanston;
    } // end if development

    // fetch inital products
    searchState.initialProducts =
        await Search.fetchSearchProducts(searchState, currentLocation.latitude, currentLocation.longitude);

    // fetch seller data
    await Future.wait([
      Resold.getVendor(customer.vendorId),
      Resold.getVendorProducts(customer.vendorId, 'for-sale'),
      Resold.getVendorProducts(customer.vendorId, 'sold')
    ]).then((data) {
      vendor = data[0];
      forSaleProducts = data[1];
      soldProducts = data[2];
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
          searchState: searchState,
          currentLocation: currentLocation,
          sellState: SellState(
              listingTitleController: TextEditingController(),
              priceController: TextEditingController(),
              detailsController: TextEditingController(),
              focusState: SellFocusState(listingTitleFocused: false, priceFocused: false, detailsFocused: false),
              currentFormStep: 0)),
      blocs: [CustomerReducer(), ProductReducer(), HomeReducer(), SearchReducer()]);

  // run the app
  runApp(StoreProvider<AppState>(
    store: store,
    child: OverlaySupport(
        child: MaterialApp(
      home: customer.isLoggedIn() ? Home() : Landing(),
    )),
  ));
} // end function main
