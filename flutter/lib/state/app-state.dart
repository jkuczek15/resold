import 'package:geolocator/geolocator.dart';
import 'package:resold/enums/selected-tab.dart';
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
    // initialize application state
    if (customer.isLoggedIn()) {
      if (currentLocation == null) {
        currentLocation = await Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
      } // end if currentLocation is null
    } // end if customer is logged in

    // initialize screen state
    AppState appState = AppState(
        selectedTab: SelectedTab.home,
        customer: customer,
        currentLocation: currentLocation,
        sellState: SellState.initialState());

    await Future.wait([
      SearchState.initialState(customer, currentLocation),
      OrdersState.initialState(customer),
      AccountState.initialState(customer)
    ]).then((data) {
      appState.searchState = data[0];
      appState.ordersState = data[1];
      appState.accountState = data[2];
    });

    return appState;
  } // end function initialState
}
