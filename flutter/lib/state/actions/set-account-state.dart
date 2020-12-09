import 'package:rebloc/rebloc.dart';
import 'package:resold/state/screens/account-state.dart';

class SetAccountStateAction extends Action {
  final AccountState newState;

  const SetAccountStateAction(this.newState);
}
