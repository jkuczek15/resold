import 'package:rebloc/rebloc.dart';
import 'package:resold/state/search-state.dart';

class FilterSearchResultsAction extends Action {
  final SearchState newState;

  FilterSearchResultsAction(this.newState);
}
