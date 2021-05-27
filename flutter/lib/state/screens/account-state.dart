import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class AccountState {
  Vendor vendor;
  List<Product> forSaleProducts;
  List<Product> soldProducts;
  bool displayForSale;

  AccountState({this.vendor, this.forSaleProducts, this.soldProducts, this.displayForSale});

  static Future<AccountState> initialState(CustomerResponse customer) async {
    AccountState accountState = AccountState(displayForSale: true);
    if (customer.isLoggedIn()) {
      await Future.wait([
        Resold.getVendor(customer.vendorId),
        Resold.getVendorProducts(customer.vendorId, 'for-sale'),
        Resold.getVendorProducts(customer.vendorId, 'sold'),
      ]).then((data) {
        accountState.vendor = data[0];
        accountState.forSaleProducts = data[1];
        accountState.soldProducts = data[2];
      });
    } // end if customer is logged in
    return accountState;
  } // end function initialState

}
