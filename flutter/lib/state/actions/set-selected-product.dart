import 'package:rebloc/rebloc.dart';
import 'package:resold/models/product.dart';

class SetSelectedProductAction extends Action {
  final Product newProduct;

  const SetSelectedProductAction(this.newProduct);
}
