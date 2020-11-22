import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/delete-product.dart';
import 'package:resold/state/actions/set-for-sale.dart';
import 'package:resold/state/app-state.dart';

class ProductReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetForSaleAction) {
      state.forSaleProducts = action.newForSaleProducts;
    } else if (action is DeleteProductAction) {
      state.forSaleProducts = state.forSaleProducts.where((product) => product.id != action.deletedProduct.id).toList();
    }
    return state;
  }
}
