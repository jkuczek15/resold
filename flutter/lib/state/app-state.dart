import 'package:resold/models/product.dart';
import 'package:resold/models/vendor.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class AppState {
  CustomerResponse customer;
  Vendor vendor;
  List<Product> forSaleProducts;
  List<Product> soldProducts;

  AppState(this.customer, this.vendor, this.forSaleProducts, this.soldProducts);
}
