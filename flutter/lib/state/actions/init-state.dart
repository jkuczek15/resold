import 'package:rebloc/rebloc.dart';
import 'package:resold/state/app-state.dart';

class InitStateAction extends Action {
  final AppState newState;

  const InitStateAction(this.newState);
}
