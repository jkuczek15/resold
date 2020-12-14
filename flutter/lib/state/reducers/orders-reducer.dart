import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/request-delivery.dart';
import 'package:resold/state/actions/set-orders-state.dart';
import 'package:resold/state/app-state.dart';

class OrdersReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetOrdersStateAction) {
      state.ordersState = action.newState;
    } else if (action is RequestDeliveryAction) {
      state.ordersState.requestedDeliveries.add(action.quote);
    }
    return state;
  }
}
