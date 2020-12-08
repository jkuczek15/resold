import 'package:rebloc/rebloc.dart';
import 'package:resold/state/sell-state.dart';

class SetSellStateAction extends Action {
  final SellState newState;

  const SetSellStateAction(this.newState);
}
