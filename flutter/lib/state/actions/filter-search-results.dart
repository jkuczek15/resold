import 'package:rebloc/rebloc.dart';
import 'package:resold/state/screens/search-state.dart';

class FilterSearchResultsAction extends Action {
  final SearchState newState;

  FilterSearchResultsAction(this.newState);
}
