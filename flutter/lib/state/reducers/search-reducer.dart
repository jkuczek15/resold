import 'package:rebloc/rebloc.dart';
import 'package:resold/services/search.dart';
import 'package:resold/state/actions/filter-search-results.dart';
import 'package:resold/state/app-state.dart';

class SearchReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is FilterSearchResultsAction) {
      state.searchState = action.newState;
      state.searchState.currentPage = 0;
      Search.fetchSearchProducts(state.searchState, state.currentLocation.latitude, state.currentLocation.longitude)
          .then((results) {
        state.searchState.searchStream.add(results);
        state.searchState.initialProducts = results;
      });
    }
    return state;
  }
}
