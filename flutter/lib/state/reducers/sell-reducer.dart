import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/set-sell-image-state.dart';
import 'package:resold/state/actions/set-sell-state.dart';
import 'package:resold/state/app-state.dart';

class SellReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetSellStateAction) {
      state.sellState = action.newState;
    } else if (action is SetSellImageStateAction) {
      state.sellState.imageState = action.newState;
    }
    return state;
  }
}
