import 'package:rebloc/rebloc.dart';
import 'package:resold/models/product.dart';

class AddProductAction extends Action {
  final Product product;

  const AddProductAction({this.product});
}
