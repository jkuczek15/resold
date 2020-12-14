import 'package:geolocator/geolocator.dart';
import 'package:resold/enums/selected-tab.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/screens/account-state.dart';
import 'package:resold/state/screens/orders-state.dart';
import 'package:resold/state/screens/search-state.dart';
import 'package:resold/state/screens/sell/sell-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class AppState {
  SelectedTab selectedTab;
  CustomerResponse customer;
  Position currentLocation;
  SearchState searchState;
  SellState sellState;
  OrdersState ordersState;
  AccountState accountState;

  AppState(
      {this.selectedTab,
      this.customer,
      this.currentLocation,
      this.searchState,
      this.sellState,
      this.ordersState,
      this.accountState});

  static Future<AppState> initialState(CustomerResponse customer, {Position currentLocation}) async {
    // initialize screen state
    SearchState searchState = SearchState.initialState();
    SellState sellState = SellState.initialState();
    OrdersState ordersState = OrdersState.initialState();
    AccountState accountState = AccountState.initialState();

    // initialize application state
    if (customer.isLoggedIn()) {
      if (currentLocation == null) {
        currentLocation = await Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
      } // end if currentLocation is null

      // fetch inital products
      searchState.initialProducts =
          await Search.fetchSearchProducts(searchState, currentLocation.latitude, currentLocation.longitude);

      // fetch seller data
      await Future.wait([
        Resold.getVendor(customer.vendorId),
        Resold.getVendorProducts(customer.vendorId, 'for-sale'),
        Resold.getVendorProducts(customer.vendorId, 'sold'),
        Magento.getPurchasedOrders(customer.id),
        ResoldRest.getVendorOrders(customer.token),
        ResoldFirebase.getRequestedDeliveryQuotes(customer)
      ]).then((data) {
        accountState.vendor = data[0];
        accountState.forSaleProducts = data[1];
        accountState.soldProducts = data[2];
        ordersState.purchasedOrders = data[3];
        ordersState.soldOrders = data[4];
        ordersState.requestedDeliveries = data[5];
      });
    } // end if customer is logged in

    // store app state
    return AppState(
      selectedTab: SelectedTab.home,
      customer: customer,
      currentLocation: currentLocation,
      searchState: searchState,
      sellState: sellState,
      ordersState: ordersState,
      accountState: accountState,
    );
  } // end function initialState
}
