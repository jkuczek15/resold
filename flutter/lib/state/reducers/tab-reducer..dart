import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/set-selected-tab.dart';
import 'package:resold/state/app-state.dart';

class TabReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetSelectedTabAction) {
      state.selectedTab = action.selectedTab;
    }
    return state;
  }
}
