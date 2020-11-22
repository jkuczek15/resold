import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/init-state.dart';
import 'package:resold/state/actions/set-selected-tab.dart';
import 'package:resold/state/app-state.dart';

class HomeReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is InitStateAction) {
      return action.newState;
    } else if (action is SetSelectedTabAction) {
      state.selectedTab = action.selectedTab;
    }
    return state;
  }
}
