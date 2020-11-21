import 'package:rebloc/rebloc.dart';
import 'package:resold/models/product.dart';

class SetForSaleAction extends Action {
  final List<Product> newForSaleProducts;

  const SetForSaleAction(this.newForSaleProducts);
}
