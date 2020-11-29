import 'package:rebloc/rebloc.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/actions/fetch-search-results.dart';
import 'package:resold/state/actions/set-search-state.dart';
import 'package:resold/state/app-state.dart';

class SearchReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetSearchStateAction) {
      state.searchState = action.newState;
    } else if (action is FetchSearchResultsAction) {
      Search.fetchSearchProducts(state.searchState, state.currentLocation.latitude, state.currentLocation.longitude)
          .then((results) {
        state.searchState.searchStream.add(results);
        state.searchState.initialProducts = results;
      });
    }
    return state;
  }
}
