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
}
