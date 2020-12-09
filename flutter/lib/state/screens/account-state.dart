import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';

class AccountState {
  Vendor vendor;
  List<Product> forSaleProducts;
  List<Product> soldProducts;
  bool displayForSale;

  AccountState({this.vendor, this.forSaleProducts, this.soldProducts, this.displayForSale});
}
