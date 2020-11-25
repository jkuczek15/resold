import 'package:rebloc/rebloc.dart';
import 'package:resold/state/search-state.dart';

class SetSearchStateAction extends Action {
  final SearchState newState;

  const SetSearchStateAction(this.newState);
}
