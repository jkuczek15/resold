import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/update-customer.dart';
import 'package:resold/state/app-state.dart';

class CustomerReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is UpdateCustomerAction) {
      state.customer = action.newCustomer;
    }
    return state;
  }
}
