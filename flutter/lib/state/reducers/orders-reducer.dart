import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/set-orders-state.dart';
import 'package:resold/state/app-state.dart';

class OrdersReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetOrdersStateAction) {
      state.ordersState = action.newState;
    }
    return state;
  }
}
