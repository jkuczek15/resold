import 'package:geolocator/geolocator.dart';
import 'package:resold/enums/selected-tab.dart';
import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';
import 'package:resold/state/search-state.dart';
import 'package:resold/state/sell-state.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class AppState {
  SelectedTab selectedTab;
  CustomerResponse customer;
  Position currentLocation;
  Vendor vendor;
  List<Product> forSaleProducts;
  List<Product> soldProducts;
  SearchState searchState;
  SellState sellState;

  AppState(
      {this.selectedTab,
      this.customer,
      this.currentLocation,
      this.vendor,
      this.forSaleProducts,
      this.soldProducts,
      this.searchState,
      this.sellState});
}
