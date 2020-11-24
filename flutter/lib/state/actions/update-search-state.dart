import 'package:rebloc/rebloc.dart';
import 'package:resold/state/search-state.dart';

class UpdateSearchStateAction extends Action {
  final SearchState newState;

  const UpdateSearchStateAction(this.newState);
}
