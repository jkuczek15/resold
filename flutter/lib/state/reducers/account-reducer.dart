import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/delete-product.dart';
import 'package:resold/state/actions/set-account-state.dart';
import 'package:resold/state/app-state.dart';

class AccountReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetAccountStateAction) {
      state.accountState = action.newState;
    } else if (action is DeleteProductAction) {
      state.accountState.forSaleProducts =
          state.accountState.forSaleProducts.where((product) => product.id != action.product.id).toList();
    }
    return state;
  }
}
