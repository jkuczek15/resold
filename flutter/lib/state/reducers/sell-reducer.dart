import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/set-sell-state.dart';
import 'package:resold/state/app-state.dart';

class SellReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetSellStateAction) {
      state.sellState = action.newState;
    }
    return state;
  }
}
