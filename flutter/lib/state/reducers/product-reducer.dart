import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/delete-product.dart';
import 'package:resold/state/actions/set-for-sale.dart';
import 'package:resold/state/app-state.dart';

class ProductReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetForSaleAction) {
      return AppState(state.customer, state.vendor, action.newForSaleProducts, state.soldProducts);
    } else if (action is DeleteProductAction) {
      return AppState(
          state.customer,
          state.vendor,
          state.forSaleProducts.where((product) => product.id != action.deletedProduct.id).toList(),
          state.soldProducts);
    }
    return state;
  }
}
