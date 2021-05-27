import 'package:rebloc/rebloc.dart';
import 'package:resold/models/product.dart';

class DeleteProductAction extends Action {
  final Product product;

  const DeleteProductAction({this.product});
}
