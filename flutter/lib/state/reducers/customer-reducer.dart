import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/set-customer.dart';
import 'package:resold/state/app-state.dart';

class CustomerReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetCustomerAction) {
      state.customer = action.newCustomer;
    }
    return state;
  }
}
